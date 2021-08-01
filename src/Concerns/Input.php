<?php

namespace Collective\Html\Concerns;

use DateTime;
use Illuminate\Contracts\Support\Htmlable;

trait Input
{
    /**
     * The types of inputs to not fill values on by default.
     *
     * @var array
     */
    protected $skipValueTypes = ['file', 'password', 'checkbox', 'radio'];

    /**
     * Create a form input field.
     *
     * @param  string  $type
     * @param  string|null  $name
     * @param  string|int|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function input(string $type, ?string $name, $value = null, array $options = []): Htmlable
    {
        ! isset($options['name']) && $options['name'] = $name;

        // We will get the appropriate value for the given field. We will look for the
        // value in the session for the value in the old input data then we'll look
        // in the model instance if one is set. Otherwise we will just use empty.
        $id = $this->getIdAttribute($name, $options);

        if (! \in_array($type, $this->skipValueTypes)) {
            $value = $this->getValueAttribute($name, $value);
        }

        // Once we have the type, value, and ID we can merge them into the rest of the
        // attributes array so we can convert them into their HTML attribute format
        // when creating the HTML element. Then, we will return the entire input.

        $options['type']  = $type;
        $options['value'] = $value;
        $options['id']    = $id;

        return $this->toHtmlString('<input'.$this->html->attributes($options).'>');
    }

    /**
     * Create a search input field.
     *
     * @param  string $name
     * @param  string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function search(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('search', $name, $value, $options);
    }

    /**
     * Create a text input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function text(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('text', $name, $value, $options);
    }

    /**
     * Create a password input field.
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function password(string $name, array $options = []): Htmlable
    {
        return $this->input('password', $name, '', $options);
    }

    /**
     * Create a range input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function range(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('range', $name, $value, $options);
    }

    /**
     * Create a color input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function color(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('color', $name, $value, $options);
    }

    /**
     * Create an e-mail input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function email(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('email', $name, $value, $options);
    }

    /**
     * Create a tel input field.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function tel(string $name, $value = null, array $options = []): Htmlable
    {
        return $this->input('tel', $name, $value, $options);
    }

    /**
     * Create a number input field.
     *
     * @param  string  $name
     * @param  string|int|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function number(string $name, $value = null, array $options = []): Htmlable
    {
        return $this->input('number', $name, $value, $options);
    }

    /**
     * Create a date input field.
     *
     * @param  string  $name
     * @param  \DateTime|string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function date(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d');
        }

        return $this->input('date', $name, $value, $options);
    }

    /**
     * Create a datetime input field.
     *
     * @param  string $name
     * @param  \DateTime|string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function datetime(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format(DateTime::RFC3339);
        }

        return $this->input('datetime', $name, $value, $options);
    }

    /**
     * Create a datetime-local input field.
     *
     * @param  string $name
     * @param  \DateTime|string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function datetimeLocal(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d\TH:i');
        }

        return $this->input('datetime-local', $name, $value, $options);
    }

    /**
     * Create a time input field.
     *
     * @param  string  $name
     * @param  \DateTime|string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function time(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format('H:i');
        }


        return $this->input('time', $name, $value, $options);
    }

    /**
     * Create a week input field.
     *
     * @param  string  $name
     * @param  \DateTime|string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function week(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-\WW');
        }

        return $this->input('week', $name, $value, $options);
    }

    /**
     * Create a month input field.
     *
     * @param  string  $name
     * @param  \DateTime|string|null  $value
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function month(string $name, $value = null, array $options = []): Htmlable
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m');
        }

        return $this->input('month', $name, $value, $options);
    }

    /**
     * Create a url input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function url(string $name, ?string $value = null, array $options = []): Htmlable
    {
        return $this->input('url', $name, $value, $options);
    }

    /**
     * Create a file input field.
     *
     * @param  string  $name
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function file(string $name, array $options = []): Htmlable
    {
        return $this->input('file', $name, null, $options);
    }

    /**
     * Create a HTML image input element.
     *
     * @param  string  $url
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function image(string $url, ?string $name = null, array $attributes = []): Htmlable
    {
        $attributes['src'] = $this->url->asset($url);

        return $this->input('image', $name, null, $attributes);
    }

    /**
     * Create a textarea input field.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function textarea(string $name, ?string $value = null, array $options = []): Htmlable
    {
        ! isset($options['name']) && $options['name'] = $name;

        // Next we will look for the rows and cols attributes, as each of these are put
        // on the textarea element definition. If they are not present, we will just
        // assume some sane default values for these attributes for the developer.
        $options = $this->setTextAreaSize($options);

        $options['id'] = $this->getIdAttribute($name, $options);

        $value = (string) $this->getValueAttribute($name, $value);

        unset($options['size']);

        // Next we will convert the attributes into a string form. Also we have removed
        // the size attribute, as it was merely a short-cut for the rows and cols on
        // the element. Then we'll create the final textarea elements HTML for us.
        $options = $this->html->attributes($options);

        return $this->toHtmlString('<textarea'.$options.'>'.$this->entities($value).'</textarea>');
    }

    /**
     * Set the text area size on the attributes.
     *
     * @param  array  $options
     *
     * @return array
     */
    protected function setTextAreaSize(array $options): array
    {
        if (isset($options['size'])) {
            return $this->setQuickTextAreaSize($options);
        }

        // If the "size" attribute was not specified, we will just look for the regular
        // columns and rows attributes, using sane defaults if these do not exist on
        // the attributes array. We'll then return this entire options array back.
        $options['cols'] = $options['cols'] ?? 50;

        $options['rows'] = $options['rows'] ?? 10;

        return $options;
    }

    /**
     * Set the text area size using the quick "size" attribute.
     *
     * @param  array  $options
     *
     * @return array
     */
    protected function setQuickTextAreaSize(array $options): array
    {
        $segments = \explode('x', $options['size']);

        $options['cols'] = $segments[0];
        $options['rows'] = $segments[1];

        return $options;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string  $value
     * @param  bool  $encoding
     *
     * @return string
     */
    abstract protected function entities(string $value, bool $encoding = false): string;

    /**
     * Transform the string to an Html serializable object.
     *
     * @param  string  $html
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    abstract protected function toHtmlString(string $html): Htmlable;
}
