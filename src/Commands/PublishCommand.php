<?php

namespace Liv\InertiaJsPreset\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liv:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the UI scaffolding to infyom folder';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        (new Filesystem)->copyDirectory(dirname(__DIR__, 1).'/inertiajs-stubs/resources/infyom', resource_path('infyom'));
        $this->info('UI scaffolding published successfully.');
    }
}