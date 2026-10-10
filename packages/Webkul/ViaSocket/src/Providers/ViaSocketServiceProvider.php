<?php

namespace Webkul\ViaSocket\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\ViaSocket\Listeners\KrayinEventListener;
use Webkul\ViaSocket\Services\EmbedTokenService;
use Webkul\ViaSocket\Support\KrayinEvents;

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
            ->prefix(config('app.admin_path').'/integrations')
            ->group(__DIR__.'/../Routes/web.php');

        foreach (array_keys(KrayinEvents::all()) as $event) {
            Event::listen($event, fn ($model) => app(KrayinEventListener::class)->handle($event, $model));
        }
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
