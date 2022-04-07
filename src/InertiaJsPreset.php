<?php

namespace Liv\InertiaJsPreset;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Liv\InertiaJsPreset\Commands\Preset;

class InertiaJsPreset extends Preset
{
    public static function install()
    {
        static::updatePackages();
        static::updateBootstrapping();
        static::updateComposer(false);
        static::publishHandleInertiaRequests();
        // static::publishInertiaServiceProvider();
        // static::registerInertiaServiceProvider();
        static::updateHttpFolder();
        static::datatables();
        static::updatePublicFolder();
        static::updateWelcomePage();
        static::updateGitignore();
        static::scaffoldComponents();
        static::scaffoldRoutes();
        static::infyomResources();
        static::removeNodeModules();
    }

    protected static function updatePackageArray(array $packages)
    {
        return array_merge([
            "@inertiajs/inertia"=> "^0.8.4",
            "@inertiajs/inertia-vue"=> "^0.5.5",
            "@inertiajs/progress"=> "^0.2.4",
            "date-fns"=> "^2.22.1",
            "lang.js"=> "^1.1.14",
            "sortablejs"=> "^1.13.0",
            "v-runtime-template"=> "^1.10.0",
            "vee-validate"=> "^3.4.5",
            "vue"=> "^2.6.12",
            "tailwindcss"=> "^2.1.2",
            "vue-sweetalert2"=> "^4.2.0",
            "vuetify"=> "^2.4.3",
            'vue-template-compiler' => '^2.6.10',
            "postcss-import"=> "^14.0.1",
            "autoprefixer"=> "^10.2.4",
            "@vue/compiler-sfc"=> "^3.0.5",
        ], $packages);
    }

    protected static function updateComposerArray(array $packages)
    {
        return array_merge([
            "inertiajs/inertia-laravel" => "^0.4.3",
            "tightenco/ziggy" => "^1.0",
            "spatie/laravel-query-builder" => "^3.3",
            "laracasts/flash" => "^3.2",
        ], $packages);
    }

    protected static function updateBootstrapping()
    {
        copy(__DIR__.'/inertiajs-stubs/webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__.'/inertiajs-stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/inertiajs-stubs/webpack.config.js', base_path('webpack.config.js'));

        copy(__DIR__.'/inertiajs-stubs/resources/js/app.js', resource_path('js/app.js'));

        copy(__DIR__.'/inertiajs-stubs/resources/css/app.css', resource_path('css/app.css'));
        // copy(__DIR__.'/inertiajs-stubs/resources/sass/app.scss', resource_path('sass/app.scss'));
        // copy(__DIR__.'/inertiajs-stubs/resources/sass/_nprogress.scss', resource_path('sass/_nprogress.scss'));
    }
    
    public function infyomResources() {
        // copy(__DIR__.'/inertiajs-stubs/config/infyom/laravel_generator.php', base_path('config/infyom/laravel_generator.php'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/config/infyom', config_path('infyom'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/resources/infyom', resource_path('infyom'));
    }

    protected static function updateWelcomePage()
    {
        (new Filesystem)->delete(resource_path('views/welcome.blade.php'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/resources/views/layouts', resource_path('views/layouts'));
        copy(__DIR__.'/inertiajs-stubs/resources/views/app.blade.php', resource_path('views/app.blade.php'));
    }
    
    protected static function updateHttpFolder()
    {
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/Http/Controllers/API', app_path('Http/Controllers/API'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/Http/Controllers/Dashboard', app_path('Http/Controllers/Dashboard'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/Http/Resources', app_path('Http/Resources'));
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/Http/Requests/Auth', app_path('Http/Requests/Auth'));
        copy(__DIR__.'/inertiajs-stubs/Http/Kernel.php', app_path('Http/Kernel.php'));
    }
    
    protected static function updatePublicFolder()
    {
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/public/images', base_path('public/images'));
        copy(__DIR__.'/inertiajs-stubs/public/mix-manifest.json', base_path('public/mix-manifest.json'));
    }
   
    protected static function datatables()
    {
        (new Filesystem)->copyDirectory(__DIR__.'/inertiajs-stubs/DataTables', base_path('DataTables'));
    }

    protected static function updateGitignore()
    {
        file_put_contents(
            base_path('.gitignore'),
            file_get_contents(__DIR__.'/inertiajs-stubs/gitignore'),
            FILE_APPEND
        );
    }

    protected static function scaffoldComponents()
    {
        tap(new Filesystem, function ($fs) {
            $fs->deleteDirectory(base_path('node_modules'));
            $fs->copyDirectory(__DIR__.'/inertiajs-stubs/resources/js/Layouts', resource_path('js/Layouts'));
            $fs->copyDirectory(__DIR__.'/inertiajs-stubs/resources/js/plugins', resource_path('js/plugins'));
            $fs->copyDirectory(__DIR__.'/inertiajs-stubs/resources/js/components', resource_path('js/components'));
            $fs->copyDirectory(__DIR__.'/inertiajs-stubs/resources/js/Pages', resource_path('js/Pages'));
        });
    }

    protected static function scaffoldRoutes()
    {
        copy(__DIR__.'/inertiajs-stubs/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/inertiajs-stubs/routes/api.php', base_path('routes/api.php'));
        copy(__DIR__.'/inertiajs-stubs/routes/auth.php', base_path('routes/auth.php'));
    }

    protected static function updateComposer($dev = true)
    {
        if (! file_exists(base_path('composer.json'))) {
            return;
        }

        $configurationKey = $dev ? 'require-dev' : 'require';

        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        $packages[$configurationKey] = static::updateComposerArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('composer.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    public static function publishHandleInertiaRequests()
    {
        copy(
            __DIR__.'/inertiajs-stubs/Http/Middleware/HandleInertiaRequests.php', 
            app_path('Http/Middleware/HandleInertiaRequests.php')
        );
    }

    public static function registerInertiaServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', Container::getInstance()->getNamespace());

        $appConfig = file_get_contents(app_path('Htpp/Kernel.php'));

        if (Str::contains($appConfig, $namespace.'\\Middleware\\HandleInertiaRequests::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\\RouteServiceProvider::class,".$eol,
            "{$namespace}\\Providers\\RouteServiceProvider::class,".$eol."        {$namespace}\Providers\InertiaJsServiceProvider::class,".$eol,
            $appConfig
        ));

        file_put_contents(app_path('Providers/InertiaJsServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/InertiaJsServiceProvider.php'))
        ));
    }
}
