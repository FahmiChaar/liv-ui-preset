<?php

namespace App\DataTables;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use DB;
use Inertia\Inertia;

class VueDataTable {

    protected $rowPerPage = 10;
    protected $actionsView;
    protected $showActionsView = true;
    protected $model;
    protected $attributes = [];
    protected $isInertia = false;
    protected $inertiaView;

    public function render($view, $data = [])
    {
        if (request()->ajax() && request()->wantsJson()) {
            return $this->getData();
        }
        $datatableData = ['datatable' => $this->getData()];
        return view($view, $data)->with($datatableData);
    }
    
    public function inertiaRender($view, $data = [])
    {
        if (request()->ajax() && request()->wantsJson() && request()->has('datatable')) {
            return $this->getData();
        }
        $this->isInertia = true;
        $this->showActionsView = false;
        return Inertia::render($view,
            array_merge([
                'datatable' => !request()->has('noData') ? $this->getData() : null
            ], $data)    
        );
    }

    public function getData() {
        $model = $this->model();
        $modelName = substr($model, strrpos($model, "\\")+1, strlen($model));
        $pluralModelName = Str::plural(Str::camel($modelName));
        return [
            'headers' => $this->getColumns(),
            'isInertia' => $this->isInertia,
            'inertiaView' => $this->inertiaView ?? $pluralModelName,
            'query' => $this->buildQuery(),
            'model' => $this->model()
        ];
    }

    protected function editedColumns($item) {
        return [];
    }

    public function buildQuery() {
        $columns = $this->getColumns();
        $query = $this->query();
        $model = app()->make($this->model());
        $datatableArray = $model->datatable ?: [];
        $datatableArray = $model->fillable ? array_merge($datatableArray, $model->fillable) : $datatableArray;
        if ($columns && count($columns) > 0) {
            $selectedFields = array_unique(array_merge($this->pluckColumns($columns), $datatableArray));
            $query->select($selectedFields);
            $searchTerm = request()->search;
            if ($searchTerm) {
                $searchFields = $model->searchable ?: $selectedFields;
                $query = $this->buildSearchQuery($searchTerm, $query, $searchFields, $model->customSearch);
            }
        }
        $queryPaginated = $query->paginate($this->rowPerPage);
        return $this->transformQuery($queryPaginated);
    }

    private function buildSearchQuery($search, $query, $searchFields, $customSearch) {
        //query: "select * from `courses` where (LOWER(`courses`.`id`) LIKE ? or LOWER(`courses`.`title`) LIKE ? or LOWER(`courses`.`description`) LIKE ? or LOWER(`courses`.`start_at`) LIKE ? or LOWER(`courses`.`price`) LIKE ?) order by `id` desc limit 10 offset 0"
        $model = $this->model();
        $makedModel = app()->make($model);
        $modelsFolder = substr($model, 0, strrpos($model, "\\")+1);
        $keyWord = "%$search%";
        $searchFields = array_diff($searchFields, $makedModel->datatbleSearchIgnore ?: []);
        $query->where(function($query) use ($searchFields, $keyWord, $modelsFolder, $makedModel) {
            foreach($searchFields as $index => $field) {
                $operator = $index != 0 ? 'or' : '';
                $isRelationArray = is_array($field);
                if ($isRelationArray ||strrpos($field, "_id") != false) {
                    if ($isRelationArray) {
                        $relation = $field['model'];
                        $relationName = $field['relation'];
                    }else {
                        $relation = Str::studly(Str::before($field, '_id'));
                        $relationName = $relation;
                    }
                    $relationModelClass = $modelsFolder . $relation;
                    $relationModelPath = str_replace('\\', '/', Str::after($relationModelClass, "App\\"));
                    if (file_exists(app_path("$relationModelPath.php"))) {
                        $relationModel = app()->make($relationModelClass);
                        $query->{$operator . 'whereHas'}($relationName, function($q) use ($keyWord, $relationModel) {
                            foreach($relationModel->fillable as $index => $column) {
                                $operator = $index != 0 ? 'or' : '';
                                $q->{$operator . 'whereRaw'}(DB::raw("LOWER({$column}) LIKE ?"), [$keyWord]);
                            }
                            return $q;
                        });
                    }
                }else {
                    $searchTerm = DB::raw("LOWER({$field}) LIKE ?");
                    $query->{$operator . 'whereRaw'}($searchTerm, [$keyWord]);
                }
            }
        });
        // if ($customSearch && count($customSearch) > 0) {
        //     $explodedFields = [];
        //     foreach ($customSearch as $index => $customField) {
        //         $explodedFields = explode(',', $customField);
        //         foreach ($explodedFields as $explodedField) {
        //             if (Str::contains($explodedField, '{')) {
        //                 $field = Str::between($explodedField, '{', '}');
        //                 $query->whereRaw(DB::raw("LOWER({$field}) = {$search}"));
        //             }
        //         }
        //         // $t = preg_match('/{(.*?)\}/s', $customField, $matches);
        //     }
        // }
        return $query;
    }

    private function transformQuery($queryPaginated) {
        $model = $this->model();
        $modelName = substr($model, strrpos($model, "\\")+1, strlen($model));
        $pluralModelName = Str::plural(strtolower(Str::snake($modelName)));
        $dataTransformed = $queryPaginated
        ->getCollection()
        ->map(function($item) use ($pluralModelName) {
            $data = [];
            foreach ($this->editedColumns($item) as $key => $value) {
                $data[$key] = $value;
            }
            if ($this->showActionsView) {
                $actionsViewPath = $this->actionsView ?: "dashboard.{$pluralModelName}.datatables_actions";
                try {
                    $data['actions'] = view($actionsViewPath, ['item' => $item])->render();
                }catch(Exception $e) {}
            }
            return array_merge($item->toArray(), $data);
        })->toArray();

        return new LengthAwarePaginator(
            $dataTransformed,
            $queryPaginated->total(),
            $queryPaginated->perPage(),
            $queryPaginated->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $queryPaginated->currentPage()
                ]
            ]
        );
    }

    private function pluckColumns($columns) {
        $newColumns = [];
        $model = app()->make($this->model());
        $newColumns = collect($columns)->map(function($item) use ($model) {
            $itemValue = is_array($item) ? $item['value'] : $item;
            if (!$model->fillable && !$model->datatable)
                throw new Exception('Add attributes to fillable or datatable array');
            if (in_array($itemValue, $model->fillable ?: []) || in_array($itemValue, $model->datatable ?: []) || $itemValue == 'id') {
                return $itemValue;
            }
        });
        return array_filter($newColumns->toArray());
    }

    protected function getColumns() {
        $model = app()->make($this->model());
        return $model->fillable ?: [];
    }

    /**
     * Set a custom class attribute.
     *
     * @param mixed      $key
     * @param mixed|null $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->attributes = array_merge($this->attributes, $key);
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

}