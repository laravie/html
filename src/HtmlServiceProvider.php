<?php

namespace Collective\Html;

use Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->app->singleton('html', function ($app) {
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
        $this->app->singleton('form', function ($app) {
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
