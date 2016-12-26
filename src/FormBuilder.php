<?php

namespace Collective\Html;

use BadMethodCallException;
use Illuminate\Support\HtmlString;
use Collective\Html\Traits\InputTrait;
use Collective\Html\Traits\CheckerTrait;
use Collective\Html\Traits\CreatorTrait;
use Illuminate\Support\Traits\Macroable;
use Collective\Html\Traits\SelectionTrait;
use Collective\Html\Traits\SessionHelperTrait;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class FormBuilder
{
    use Componentable, CheckerTrait, CreatorTrait, InputTrait, Macroable, SelectionTrait, SessionHelperTrait {
        Componentable::__call as componentCall;
        Macroable::__call as macroCall;
    }

    /**
     * The HTML builder instance.
     *
     * @var \Collective\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Create a new form builder instance.
     *
     * @param  \Collective\Html\HtmlBuilder  $html
     * @param  \Illuminate\Contracts\Routing\UrlGenerator  $url
     * @param  \Illuminate\Contracts\View\Factory  $view
     */
    public function __construct(HtmlBuilder $html, UrlGeneratorContract $url, ViewFactoryContract $view)
    {
        $this->url  = $url;
        $this->html = $html;
        $this->view = $view;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string  $value
     * @param  bool  $encoding
     *
     * @return string
     */
    protected function entities($value, $encoding = false)
    {
        return $this->html->entities($value, $encoding);
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function token()
    {
        if (empty($this->csrfToken) && ! is_null($this->session)) {
            $this->csrfToken = $this->session->token();
        }

        return $this->hidden('_token', $this->csrfToken);
    }

    /**
     * Create a new model based form builder.
     *
     * @param  mixed  $model
     * @param  array  $options
     *
     * @return string
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;

        return $this->open($options);
    }

    /**
     * Set the model instance on the form builder.
     *
     * @param  mixed  $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function label($name, $value = null, $options = [])
    {
        $this->labels[] = $name;

        $options = $this->html->attributes($options);

        $value = e($this->formatLabel($name, $value));

        return $this->toHtmlString('<label for="'.$name.'"'.$options.'>'.$value.'</label>');
    }

    /**
     * Format the label value.
     *
     * @param  string  $name
     * @param  string|null  $value
     *
     * @return string
     */
    protected function formatLabel($name, $value)
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Determine if old input or model input exists for a key.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function missingOldAndModel($name)
    {
        return (is_null($this->old($name)) && is_null($this->getModelValueAttribute($name)));
    }

    /**
     * Create a HTML reset input element.
     *
     * @param  string  $value
     * @param  array   $attributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function reset($value, $attributes = [])
    {
        return $this->input('reset', null, $value, $attributes);
    }

    /**
     * Create a hidden input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function hidden($name, $value = null, $options = [])
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * Create a submit button element.
     *
     * @param  string  $value
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function submit($value = null, $options = [])
    {
        return $this->input('submit', null, $value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string  $value
     * @param  array   $options
     * @param  bool    $escape
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function button($value = null, $options = [], $escape = true)
    {
        if (! array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        if ($escape) {
            $value = $this->html->entities($value);
        }

        return $this->toHtmlString('<button'.$this->html->attributes($options).'>'.$value.'</button>');
    }

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return string
     */
    public function getIdAttribute($name, $attributes)
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (in_array($name, $this->labels)) {
            return $name;
        }
    }

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return string
     */
    public function getValueAttribute($name, $value = null)
    {
        if (is_null($name)) {
            return $value;
        }

        if (! is_null($this->old($name)) && $name != '_method') {
            return $this->old($name);
        }

        if (! is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return $this->getModelValueAttribute($name);
        }
    }

    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getModelValueAttribute($name)
    {
        if (method_exists($this->model, 'getFormValue')) {
            return $this->model->getFormValue($this->transformKey($name));
        }

        return data_get($this->model, $this->transformKey($name));
    }

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    protected function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Transform the string to an Html serializable object.
     *
     * @param  string  $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function toHtmlString($html)
    {
        return new HtmlString($html);
    }

    /**
     * Get html builder.
     *
     * @return \Collective\Html\HtmlBuilder
     */
    public function getHtmlBuilder()
    {
        return $this->html;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasComponent($method)) {
            return $this->renderComponent($method, $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}
