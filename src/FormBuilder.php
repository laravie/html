<?php

namespace Collective\Html;

use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class FormBuilder
{
    use Componentable,
        Concerns\Checker,
        Concerns\Creator,
        Concerns\Input,
        Concerns\Selection,
        Concerns\SessionHelper,
        Macroable {
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
     * The View factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * The request implementation.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Consider Request variables while auto fill.
     *
     * @var bool
     */
    protected $considerRequest = false;

    /**
     * Create a new form builder instance.
     *
     * @param  \Collective\Html\HtmlBuilder  $html
     * @param  \Illuminate\Contracts\Routing\UrlGenerator  $url
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(
        HtmlBuilder $html,
        UrlGeneratorContract $url,
        ViewFactoryContract $view,
        Request $request
    ) {
        $this->url     = $url;
        $this->html    = $html;
        $this->view    = $view;
        $this->request = $request;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string  $value
     * @param  bool  $encoding
     *
     * @return string
     */
    protected function entities(string $value, bool $encoding = false): string
    {
        return $this->html->entities($value, $encoding);
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function token(): Htmlable
    {
        if (empty($this->csrfToken) && ! \is_null($this->session)) {
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
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function model($model, array $options = []): Htmlable
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
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * Get the current model instance on the form builder.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function label(string $name, ?string $value = null, array $options = []): Htmlable
    {
        $this->labels[] = $name;

        $options = $this->html->attributes($options);

        $value = $this->entities($this->formatLabel($name, $value));

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
    protected function formatLabel(string $name, ?string $value): string
    {
        return $value ?? \ucwords(\str_replace('_', ' ', $name));
    }

    /**
     * Determine if old input or model input exists for a key.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function missingOldAndModel(string $name): bool
    {
        return \is_null($this->old($name)) && \is_null($this->getModelValueAttribute($name));
    }

    /**
     * Create a HTML reset input element.
     *
     * @param  string|null  $value
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function reset(?string $value = null, array $attributes = []): Htmlable
    {
        return $this->input('reset', null, $value, $attributes);
    }

    /**
     * Create a hidden input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function hidden(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * Create a submit button element.
     *
     * @param  string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function submit(?string $value = null, array $options = []): Htmlable
    {
        return $this->input('submit', null, $value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string|null  $value
     * @param  array  $options
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function button(?string $value = null, array $options = [], bool $escape = true): Htmlable
    {
        if (! \array_key_exists('type', $options)) {
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
     * @param  string|null  $name
     * @param  array  $attributes
     *
     * @return string|null
     */
    public function getIdAttribute(?string $name, array $attributes): ?string
    {
        if (\array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (\in_array($name, $this->labels)) {
            return $name;
        }

        return null;
    }

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string|null  $name
     * @param  mixed  $value
     *
     * @return mixed
     */
    public function getValueAttribute(?string $name, $value = null)
    {
        if (\is_null($name)) {
            return $value;
        }

        if (! \is_null($old = $this->old($name)) && $name !== '_method') {
            return $old;
        }

        if (\class_exists(Kernel::class, false)) {
            $hasNullMiddleware = \app(Kernel::class)->hasMiddleware(ConvertEmptyStringsToNull::class);

            if ($hasNullMiddleware && \is_null($old) && \is_null($value)
                && ! \is_null($this->view->shared('errors'))
                && \count(\is_countable($this->view->shared('errors')) ? $this->view->shared('errors') : []) > 0
            ) {
                return null;
            }
        }

        if (! \is_null($request = $this->request($name)) && $name !== '_method') {
            return $request;
        }

        if (! \is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return $this->getModelValueAttribute($name);
        }
    }

    /**
     * Get value from current Request.
     *
     * @param string $name
     *
     * @return array|string|null
     */
    protected function request(string $name)
    {
        if (! $this->considerRequest || ! isset($this->request)) {
            return null;
        }

        return $this->request->input($this->transformKey($name));
    }

    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string  $name
     *
     * @return mixed
     */
    protected function getModelValueAttribute(string $name)
    {
        if (! isset($this->model)) {
            return null;
        }

        $key = $this->transformKey($name);

        if (\method_exists($this->model, 'getFormValue')) {
            return $this->model->getFormValue($key);
        }

        return \data_get($this->model, $key);
    }

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    protected function transformKey(string $key): string
    {
        return \str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Transform the string to an Html serializable object.
     *
     * @param  string  $html
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function toHtmlString(string $html): Htmlable
    {
        return new HtmlString($html);
    }

    /**
     * Get html builder.
     *
     * @return \Collective\Html\HtmlBuilder
     */
    public function getHtmlBuilder(): HtmlBuilder
    {
        return $this->html;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return \Illuminate\Contracts\Support\Htmlable|mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasComponent($method)) {
            return $this->renderComponent($method, $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Take Request in fill process.
     *
     * @param  bool  $consider
     *
     * @return $this
     */
    public function considerRequest(bool $consider = true): self
    {
        $this->considerRequest = $consider;

        return $this;
    }
}
