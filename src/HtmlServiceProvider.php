<?php

namespace Collective\Html;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class HtmlServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHtmlBuilder();

        $this->registerFormBuilder();

        $this->app->alias('html', HtmlBuilder::class);
        $this->app->alias('form', FormBuilder::class);
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder(): void
    {
        $this->app->singleton('html', static function ($app) {
            return new HtmlBuilder(
                $app->make('url'), $app->make('view')
            );
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder(): void
    {
        $this->app->singleton('form', static function ($app) {
            return (new FormBuilder(
                $app->make('html'), $app->make('url'), $app->make('view'), $app->make('request')
            ))->setSessionStore($app->make('session.store'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['html', 'form', HtmlBuilder::class, FormBuilder::class];
    }
}
