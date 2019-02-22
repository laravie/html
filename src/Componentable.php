<?php

namespace Collective\Html;

use BadMethodCallException;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;

trait Componentable
{
    /**
     * The registered components.
     *
     * @var array
     */
    protected static $components = [];

    /**
     * Register a custom component.
     *
     * @param  string  $name
     * @param  mixed  $view
     * @param  array  $signature
     *
     * @return void
     */
    public static function component(string $name, $view, array $signature): void
    {
        static::$components[$name] = compact('view', 'signature');
    }

    /**
     * Check if a component is registered.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public static function hasComponent(string $name): bool
    {
        return isset(static::$components[$name]);
    }

    /**
     * Render a custom component.
     *
     * @param  string  $name
     * @param  array  $arguments
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function renderComponent(string $name, array $arguments): Htmlable
    {
        $component = static::$components[$name];
        $data      = $this->getComponentData($component['signature'], $arguments);

        return new HtmlString(
          $this->view->make($component['view'], $data)->render()
        );
    }

    /**
     * Prepare the component data, while respecting provided defaults.
     *
     * @param  array $signature
     * @param  array $arguments
     *
     * @return array
     */
    protected function getComponentData(array $signature, array $arguments): array
    {
        $data = [];

        $i = 0;
        foreach ($signature as $variable => $default) {
            // If the "variable" value is actually a numeric key, we can assume that
            // no default had been specified for the component argument and we'll
            // just use null instead, so that we can treat them all the same.
            if (\is_numeric($variable)) {
                $variable = $default;
                $default  = null;
            }

            $data[$variable] = $arguments[$i] ?? $default;

            ++$i;
        }

        return $data;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function __call(string $method, array $parameters): Htmlable
    {
        if (static::hasComponent($method)) {
            return $this->renderComponent($method, $parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}
