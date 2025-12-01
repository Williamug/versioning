<?php

namespace Williamug\Versioning;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VersioningServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('versioning')
            ->hasConfigFile();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->registerBladeDirectives();
    }

    /**
     * Register Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // Main directive for tag version
        Blade::directive('app_version', function ($format = null) {
            $format = $format ?: "'tag'";

            return "<?php echo \Williamug\Versioning\Versioning::getVersion({$format}); ?>";
        });

        // Additional helper directives
        Blade::directive('app_version_tag', function () {
            return "<?php echo \Williamug\Versioning\Versioning::tag(); ?>";
        });

        Blade::directive('app_version_full', function () {
            return "<?php echo \Williamug\Versioning\Versioning::full(); ?>";
        });

        Blade::directive('app_version_commit', function () {
            return "<?php echo \Williamug\Versioning\Versioning::commit(); ?>";
        });
    }
}
