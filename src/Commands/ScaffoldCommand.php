<?php

namespace Liv\InertiaJsPreset\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScaffoldCommand extends Command
{
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
    }
}