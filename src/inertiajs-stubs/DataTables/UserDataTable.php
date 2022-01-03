<?php

namespace App\DataTables;

use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class UserDataTable extends VueDataTable {

    protected $rowPerPage = 10;

    public function query() {
        $sortField = request()->get('sort');
        $query = QueryBuilder::for(User::query());
        if ($sortField) {
            return $query->allowedSorts($sortField);
        }
        $query->defaultSort('-updated_at');
        return $query;
    }

    public function model()
    {
        return User::class;
    }

    protected function editedColumns($user)
    {
        return [];
    }

    protected function getColumns() {
        return [
            ['value'=> 'id', 'text'=> 'id', 'visible'=> false],
            ['value'=> 'name', 'text'=> 'Name'],
            ['value'=> 'email', 'text'=> 'Email'],
            ['value' => 'actions', 'text' => 'Actions', 'sortable'=> false]
        ];
    }

}
