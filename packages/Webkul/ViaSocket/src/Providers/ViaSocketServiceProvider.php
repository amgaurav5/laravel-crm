<?php

namespace Webkul\ViaSocket\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\ViaSocket\Listeners\LeadCreated;
use Webkul\ViaSocket\Services\EmbedTokenService;

class ViaSocketServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->app->register(ModuleServiceProvider::class);

        if (! $this->isEnabled()) {
            return;
        }

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'viasocket');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'viasocket');

        Route::middleware(['web', 'admin_locale', 'user'])
            ->prefix(config('app.admin_path').'/settings/viasocket')
            ->group(__DIR__.'/../Routes/web.php');

        Event::listen('lead.create.after', [LeadCreated::class, 'handle']);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/viasocket.php', 'viasocket');

        $this->app->singleton(EmbedTokenService::class, fn () => new EmbedTokenService(
            (string) config('viasocket.org_id'),
            (string) config('viasocket.project_id'),
            (string) config('viasocket.embed_secret'),
        ));

        if ($this->isEnabled()) {
            $this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');

            $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');
        }
    }

    /**
     * The module is off unless the embed credentials are configured.
     */
    protected function isEnabled(): bool
    {
        return filled(config('viasocket.org_id'))
            && filled(config('viasocket.project_id'))
            && filled(config('viasocket.embed_secret'));
    }
}
