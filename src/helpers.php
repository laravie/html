<?php

use Illuminate\Contracts\Support\Htmlable;

if (! \function_exists('link_to')) {
    /**
     * Generate a HTML link.
     *
     * @param  string  $url
     * @param  string|null  $title
     * @param  array  $attributes
     * @param  bool|null  $secure
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    function link_to(string $url, ?string $title = null, array $attributes = [], ?bool $secure = null, bool $escape = true): Htmlable
    {
        return \app('html')->link($url, $title, $attributes, $secure, $escape);
    }
}

if (! \function_exists('link_to_asset')) {
    /**
     * Generate a HTML link to an asset.
     *
     * @param  string  $url
     * @param  string|null  $title
     * @param  array  $attributes
     * @param  bool|null  $secure
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    function link_to_asset(string $url, ?string $title = null, array $attributes = [], ?bool $secure = null): Htmlable
    {
        return \app('html')->linkAsset($url, $title, $attributes, $secure);
    }
}

if (! \function_exists('link_to_route')) {
    /**
     * Generate a HTML link to a named route.
     *
     * @param  string  $name
     * @param  string|null  $title
     * @param  array  $parameters
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    function link_to_route(string $name, ?string $title = null, array $parameters = [], array $attributes = []): Htmlable
    {
        return \app('html')->linkRoute($name, $title, $parameters, $attributes);
    }
}

if (! \function_exists('link_to_action')) {
    /**
     * Generate a HTML link to a controller action.
     *
     * @param  string  $action
     * @param  string|null  $title
     * @param  array  $parameters
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    function link_to_action(string $action, ?string $title = null, array $parameters = [], array $attributes = []): Htmlable
    {
        return \app('html')->linkAction($action, $title, $parameters, $attributes);
    }
}
