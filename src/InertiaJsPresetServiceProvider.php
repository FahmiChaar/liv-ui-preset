<?php

namespace Liv\InertiaJsPreset;

use Illuminate\Support\ServiceProvider;

class InertiaJsPresetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        
    }

    public function register() {
        $this->commands([
            Commands\UiCommand::class,
            Commands\ScaffoldCommand::class,
            Commands\PublishCommand::class,
        ]);
    }
}
