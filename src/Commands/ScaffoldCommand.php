<?php

namespace Liv\InertiaJsPreset\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ScaffoldCommand extends Command
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liv:scaffold {model} {--tableName=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the UI scaffolding for the application';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');
        if (!$model) {
            $this->error('Please provide a model name');
            return;
        }
        $model = Str::studly($model);
        $modelPlural = Str::plural($model);
        $tableName = $this->option('tableName') ?: Str::plural(Str::snake($modelPlural));
        $this->call('infyom:scaffold', [
            'model' => $model,
            '--views' => 'index,create',
            '--fromTable' => true,
            '--tableName' => $tableName,
            '--factory' => true,
        ]);
        $modelFolderName = $tableName ? Str::studly($tableName) : $modelPlural;
        $this->refactoringInfyomViews($modelFolderName, $modelPlural);
        $this->refactoringInfyomController($modelPlural);
    }

    private function refactoringInfyomViews($modelFolderName, $modelPlural) {
        $modelPageDirectory = resource_path('js/Pages/'.$modelFolderName);
        $oldFlderPath = resource_path('js/Pages/'.Str::snake($modelPlural));
        if (!file_exists($oldFlderPath)) {
            rename($oldFolderPath, $modelPageDirectory);
        }
        $createFile = $this->getViewFileContent($modelFolderName, 'create');
        $fields = $this->getViewFileContent($modelFolderName, 'fields');
        $file = str_replace('$FIELDS$', $fields, $createFile);
        $createPath = $modelPageDirectory.'/create.blade.php';
        $this->filesystem->put($createPath, $file);
        $this->filesystem->move($createPath, $modelPageDirectory.'/Create.vue');
        $this->filesystem->move($modelPageDirectory.'/index.blade.php', $modelPageDirectory.'/Index.vue');
        $allBladeFile = $this->filesystem->glob(resource_path('js/Pages/'.$modelFolderName.'/*.blade.php'));
        $this->filesystem->delete($allBladeFile);
        $this->info('Infyom views refactoring completed');
    }

    private function refactoringInfyomController($modelPlural) {
        $studlyModelName = Str::studly($modelPlural);
        $controllerPath = app_path('Http/Controllers/Dashboard/'.$studlyModelName.'Controller.php');
        $controllerContent = $this->getControllerFileContent($controllerPath, $modelPlural);
        $file = str_replace($modelPlural.'/', $studlyModelName.'/', $controllerContent);
        $this->filesystem->put($controllerPath, $file);
        $this->info('Infyom controller refactoring completed');
    }

    private function getViewFileContent($modelName, $viewName) {
        $directoryPath = resource_path('js/Pages/'.$modelName.'/');
        try {
            return $this->filesystem->get($directoryPath.$viewName.'.blade.php');
        } catch (\Exception $e) {
            return $this->error('View File not found => '. $e->getMessage());
        }
    }
    
    private function getControllerFileContent($controllerPath) {
        try {
            return $this->filesystem->get($filePath);
        } catch (\Exception $e) {
            return $this->error('Controller File not found => '. $e->getMessage());
        }
    }

}