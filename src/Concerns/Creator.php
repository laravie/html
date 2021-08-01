<?php

namespace Collective\Html\Concerns;

use Illuminate\Support\Arr;
use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\Support\Htmlable;

trait Creator
{
    /**
     * The current model instance for the form.
     *
     * @var mixed
     */
    protected $model;

    /**
     * An array of label names we've created.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $url;

    /**
     * The reserved form open attributes.
     *
     * @var array
     */
    protected $reserved = ['method', 'url', 'route', 'action', 'files'];

    /**
     * The form methods that should be spoofed, in uppercase.
     *
     * @var array
     */
    protected $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

    /**
     * Open up a new HTML form.
     *
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function open(array $options = []): Htmlable
    {
        $method = $options['method'] ?? 'post';

        // We need to extract the proper method from the attributes. If the method is
        // something other than GET or POST we'll use POST since we will spoof the
        // actual method since forms don't support the reserved methods in HTML.
        $attributes = [
            'method'         => $this->getMethod($method),
            'action'         => $this->getAction($options),
            'accept-charset' => 'UTF-8',
        ];

        // If the method is PUT, PATCH or DELETE we will need to add a spoofer hidden
        // field that will instruct the Symfony request to pretend the method is a
        // different method than it actually is, for convenience from the forms.
        $append = $this->getAppendage($method);

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

        // Finally we're ready to create the final form HTML field. We will attribute
        // format the array of attributes. We will also add on the appendage which
        // is used to spoof requests for this PUT, PATCH, etc. methods on forms.
        $attributes = \array_merge($attributes, Arr::except($options, $this->reserved));

        // Finally, we will concatenate all of the attributes into a single string so
        // we can build out the final form open statement. We'll also append on an
        // extra value for the hidden _method field if it's needed for the form.
        $attributes = $this->getHtmlBuilder()->attributes($attributes);

        return $this->toHtmlString('<form'.$attributes.'>'.$append);
    }

    /**
     * Close the current form.
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function close(): Htmlable
    {
        $this->labels = [];

        $this->model = null;

        return $this->toHtmlString('</form>');
    }

    /**
     * Get the form appendage for the given method.
     *
     * @param  string  $method
     *
     * @return string
     */
    protected function getAppendage(string $method): string
    {
        list($method, $appendage) = [\strtoupper($method), ''];

        // If the HTTP method is in this list of spoofed methods, we will attach the
        // method spoofer hidden input to the form. This allows us to use regular
        // form to initiate PUT and DELETE requests in addition to the typical.
        if (\in_array($method, $this->spoofedMethods)) {
            $appendage .= $this->hidden('_method', $method)->toHtml();
        }

        // If the method is something other than GET we will go ahead and attach the
        // CSRF token to the form, as this can't hurt and is convenient to simply
        // always have available on every form the developers creates for them.
        if ($method != 'GET') {
            $appendage .= $this->token()->toHtml();
        }

        return $appendage;
    }

    /**
     * Parse the form action method.
     *
     * @param  string  $method
     *
     * @return string
     */
    protected function getMethod(string $method): string
    {
        $method = \strtoupper($method);

        return $method != 'GET' ? 'POST' : $method;
    }

    /**
     * Get the form action from the options.
     *
     * @param  array   $options
     *
     * @return string
     */
    protected function getAction(array $options): string
    {
        // We will also check for a "route" or "action" parameter on the array so that
        // developers can easily specify a route or controller action when creating
        // a form providing a convenient interface for creating the form actions.
        if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

        // If an action is available, we are attempting to open a form to a controller
        // action route. So, we will use the URL generator to get the path to these
        // actions and return them from the method. Otherwise, we'll use current.

        if (isset($options['route'])) {
            return $this->getRouteAction($options['route']);
        } elseif (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->url->current();
    }

    /**
     * Get the action for a "url" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getUrlAction($options): string
    {
        if (\is_array($options)) {
            return $this->url->to($options[0], \array_slice($options, 1));
        }

        return $this->url->to($options);
    }

    /**
     * Get the action for a "route" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getRouteAction($options): string
    {
        if (\is_array($options)) {
            $parameters = array_slice($options, 1);

            if (array_keys($options) === [0, 1]) {
                $parameters = head($parameters);
            }

            return $this->url->route($options[0], $parameters);
        }

        return $this->url->route($options);
    }

    /**
     * Get the action for an "action" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getControllerAction($options): string
    {
        if (\is_array($options)) {
            return $this->url->action($options[0], \array_slice($options, 1));
        }

        return $this->url->action($options);
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    abstract public function token(): Htmlable;

    /**
     * Get html builder.
     *
     * @return \Collective\Html\HtmlBuilder
     */
    abstract public function getHtmlBuilder(): HtmlBuilder;

    /**
     * Transform the string to an Html serializable object.
     *
     * @param  string  $html
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    abstract protected function toHtmlString(string $html): Htmlable;
}
