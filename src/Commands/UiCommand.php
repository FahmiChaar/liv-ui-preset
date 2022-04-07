<?php

namespace Liv\InertiaJsPreset\Commands;

use Illuminate\Console\Command;
use Liv\InertiaJsPreset\InertiaJsPreset;

class UiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liv:ui';

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
        InertiaJsPreset::install();
        $this->info('Inertia.js scaffolding installed successfully.');
        $this->info('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }
}