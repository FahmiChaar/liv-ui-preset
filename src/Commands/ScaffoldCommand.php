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
        $tableName = $this->option('tableName') ?: Str::lower($modelPlural);
        $this->call('infyom:scaffold', [
            'model' => $model,
            '--views' => 'index,create',
            '--fromTable' => true,
            '--tableName' => $tableName,
            '--factory' => true,
        ]);
        $this->refactoringInfyomViews($modelPlural);
    }

    private function refactoringInfyomViews($model) {
        $modePageDirectory = resource_path('js/Pages/'.$model);
        $createFile = $this->getFileContent($model, 'create');
        $fields = $this->getFileContent($model, 'fields');
        $file = str_replace('$FIELDS$', $fields, $createFile);
        $createPath = $modePageDirectory.'/create.blade.php';
        $this->filesystem->put($createPath, $file);
        $this->filesystem->move($createPath, $modePageDirectory.'/Create.vue');
        $this->filesystem->move($modePageDirectory.'/index.blade.php', $modePageDirectory.'/Index.vue');
        $allBladeFile = $this->filesystem->glob(resource_path('js/Pages/'.$model.'/*.blade.php'));
        $this->filesystem->delete($allBladeFile);
        $this->info('Infyom views refactoring completed');
    }

    private function getFileContent($modelName, $viewName) {
        $directoryPath = resource_path('js/Pages/'.$modelName.'/');
        try {
            return $this->filesystem->get($directoryPath.$viewName.'.blade.php');
        } catch (\Exception $e) {
            return $this->error('File not found => '. $e->getMessage());
        }
    }

}