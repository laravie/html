<?php

namespace Collective\Html;

use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class HtmlBuilder
{
    use Componentable,
        Macroable,
        Concerns\Obfuscate {
            Componentable::__call as componentCall;
            Macroable::__call as macroCall;
        }

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $url;

    /**
     * The View Factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new HTML builder instance.
     *
     * @param \Illuminate\Contracts\Routing\UrlGenerator  $url
     * @param \Illuminate\Contracts\View\Factory  $view
     */
    public function __construct(UrlGeneratorContract $url, ViewFactoryContract $view)
    {
        $this->url  = $url;
        $this->view = $view;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string|null  $value
     * @param  bool  $encoding
     *
     * @return string
     */
    public function entities($value, bool $encoding = false): string
    {
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }

        return \htmlentities((string) $value, ENT_QUOTES, 'UTF-8', $encoding);
    }

    /**
     * Convert entities to HTML characters.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function decode(string $value): string
    {
        return \html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a link to a JavaScript file.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @param  bool|null  $secure
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function script(string $url, array $attributes = [], ?bool $secure = null): Htmlable
    {
        $attributes['src'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<script'.$this->attributes($attributes).'></script>');
    }

    /**
     * Generate a link to a CSS file.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @param  bool|null  $secure
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function style(string $url, array $attributes = [], ?bool $secure = null): Htmlable
    {
        $defaults = ['media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet'];

        $attributes = \array_merge($defaults, $attributes);

        $attributes['href'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<link'.$this->attributes($attributes).'>');
    }

    /**
     * Generate an HTML image element.
     *
     * @param  string  $url
     * @param  string|null  $alt
     * @param  array  $attributes
     * @param  bool|null  $secure
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function image(string $url, ?string $alt = null, array $attributes = [], ?bool $secure = null): Htmlable
    {
        $attributes['alt'] = $alt;

        return $this->toHtmlString(
            '<img src="'.$this->url->asset($url, $secure).'"'.$this->attributes($attributes).'>'
        );
    }

    /**
     * Generate a link to a Favicon file.
     *
     * @param string  $url
     * @param array  $attributes
     * @param bool|null  $secure
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function favicon(string $url, array $attributes = [], ?bool $secure = null): Htmlable
    {
        $defaults = ['rel' => 'shortcut icon', 'type' => 'image/x-icon'];

        $attributes = \array_merge($defaults, $attributes);

        $attributes['href'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<link'.$this->attributes($attributes).'>');
    }

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
    public function link(
        string $url,
        ?string $title = null,
        array $attributes = [],
        ?bool $secure = null,
        bool $escape = true
    ): Htmlable {
        $url = $this->url->to($url, [], $secure);

        if (\is_null($title)) {
            $title = $url;
        }

        if ($escape) {
            $title = $this->entities((string) $title);
        }

        return $this->toHtmlString('<a href="'.$this->entities($url).'"'.$this->attributes($attributes).'>'.$title.'</a>');
    }

    /**
     * Generate a HTTPS HTML link.
     *
     * @param  string  $url
     * @param  string|null  $title
     * @param  array  $attributes
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function secureLink(
        string $url,
        ?string $title = null,
        array $attributes = [],
        bool $escape = true
    ): Htmlable {
        return $this->link($url, $title, $attributes, true, $escape);
    }

    /**
     * Generate a HTML link to an asset.
     *
     * @param  string  $url
     * @param  string|null  $title
     * @param  array  $attributes
     * @param  bool|null  $secure
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function linkAsset(
        string $url,
        ?string $title = null,
        array $attributes = [],
        ?bool $secure = null,
        bool $escape = true
    ): Htmlable {
        $url = $this->url->asset($url, $secure);

        return $this->link($url, $title ?: $url, $attributes, $secure, $escape);
    }

    /**
     * Generate a HTTPS HTML link to an asset.
     *
     * @param  string  $url
     * @param  string|null  $title
     * @param  array  $attributes
     * @param bool   $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function linkSecureAsset(
        string $url,
        ?string $title = null,
        array $attributes = [],
        bool $escape = true
    ): Htmlable {
        return $this->linkAsset($url, $title, $attributes, true, $escape);
    }

    /**
     * Generate a HTML link to a named route.
     *
     * @param  string  $name
     * @param  string|null  $title
     * @param  array  $parameters
     * @param  array  $attributes
     * @param bool   $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function linkRoute(
        string $name,
        ?string $title = null,
        array $parameters = [],
        array $attributes = [],
        ?bool $secure = null,
        bool $escape = true
    ): Htmlable {
        return $this->link($this->url->route($name, $parameters), $title, $attributes, $secure, $escape);
    }

    /**
     * Generate a HTML link to a controller action.
     *
     * @param  string  $action
     * @param  string|null  $title
     * @param  array  $parameters
     * @param  array  $attributes
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function linkAction(
        string $action,
        ?string $title = null,
        array $parameters = [],
        array $attributes = [],
        ?bool $secure = null,
        bool $escape = true
    ): Htmlable {
        return $this->link($this->url->action($action, $parameters), $title, $attributes, $secure, $escape);
    }

    /**
     * Generate a HTML link to an email address.
     *
     * @param  string  $email
     * @param  string|null  $title
     * @param  array   $attributes
     * @param  bool  $escape
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function mailto(string $email, ?string $title = null, array $attributes = [], bool $escape = true): Htmlable
    {
        $email = $this->email($email);

        $title = $title ?: $email;

        if ($escape) {
            $title = $this->entities($title);
        }

        $email = $this->obfuscate('mailto:').$email;

        return $this->toHtmlString('<a href="'.$email.'"'.$this->attributes($attributes).'>'.$title.'</a>');
    }

    /**
     * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
     *
     * @param  string  $email
     *
     * @return string
     */
    public function email(string $email): string
    {
        return \str_replace('@', '&#64;', $this->obfuscate($email));
    }

    /**
     * Generates non-breaking space entities based on number supplied.
     *
     * @param int $num
     *
     * @return string
     */
    public function nbsp(int $num = 1): string
    {
        return \str_repeat('&nbsp;', $num);
    }

    /**
     * Generate an ordered list of items.
     *
     * @param  iterable  $list
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function ol(iterable $list, array $attributes = []): Htmlable
    {
        return $this->listing('ol', $list, $attributes);
    }

    /**
     * Generate an un-ordered list of items.
     *
     * @param  iterable  $list
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function ul(iterable $list, array $attributes = []): Htmlable
    {
        return $this->listing('ul', $list, $attributes);
    }

    /**
     * Generate a description list of items.
     *
     * @param  array   $list
     * @param  array   $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function dl(array $list, array $attributes = []): Htmlable
    {
        $attributes = $this->attributes($attributes);

        $html = "<dl{$attributes}>";

        foreach ($list as $key => $value) {
            $html .= "<dt>{$key}</dt>";

            foreach ((array) $value as $description) {
                $html .= "<dd>{$description}</dd>";
            }
        }

        $html .= '</dl>';

        return $this->toHtmlString($html);
    }

    /**
     * Create a listing HTML element.
     *
     * @param  string  $type
     * @param  iterable  $list
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function listing(string $type, iterable $list, array $attributes = []): Htmlable
    {
        $html = '';

        if (\count($list) === 0) {
            return $this->toHtmlString($html);
        }

        // Essentially we will just spin through the list and build the list of the HTML
        // elements from the array. We will also handled nested lists in case that is
        // present in the array. Then we will build out the final listing elements.
        foreach ($list as $key => $value) {
            $html .= $this->listingElement($key, $type, $value);
        }

        $attributes = $this->attributes($attributes);

        return $this->toHtmlString("<{$type}{$attributes}>{$html}</{$type}>");
    }

    /**
     * Create the HTML for a listing element.
     *
     * @param  mixed  $key
     * @param  string  $type
     * @param  mixed  $value
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function listingElement($key, string $type, $value): Htmlable
    {
        if (\is_array($value)) {
            return $this->nestedListing($key, $type, $value);
        }

        return $this->toHtmlString('<li>'.$this->entities($value).'</li>');
    }

    /**
     * Create the HTML for a nested listing attribute.
     *
     * @param  mixed   $key
     * @param  string  $type
     * @param  mixed   $value
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function nestedListing($key, string $type, $value): Htmlable
    {
        if (\is_int($key)) {
            return $this->listing($type, $value);
        }

        return $this->toHtmlString('<li>'.$key.$this->listing($type, $value)->toHtml().'</li>');
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     *
     * @return string
     */
    public function attributes(array $attributes): string
    {
        $html = [];

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array) $attributes as $key => $value) {
            if (\is_array($value) && $key !== 'class') {
                foreach ((array) $value as $name => $val) {
                    $element = $this->attributeElement($key.'-'.$name, $val);
                    if (! \is_null($element)) {
                        $html[] = $element;
                    }
                }
            } else {
                $element = $this->attributeElement($key, $value);
                if (! \is_null($element)) {
                    $html[] = $element;
                }
            }
        }

        return \count($html) > 0 ? ' '.\implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string|int  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function attributeElement($key, $value)
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        if (\is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
        if (\is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (\is_array($value) && $key === 'class') {
            return 'class="'.\implode(' ', $value).'"';
        }

        if (! \is_null($value)) {
            return $key.'="'.$this->entities($value).'"';
        }

        return null;
    }

    /**
     * Generate a meta tag.
     *
     * @param string|null  $name
     * @param string  $content
     * @param array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function meta(?string $name, $content, array $attributes = []): Htmlable
    {
        $attributes['name']    = $name;
        $attributes['content'] = $content;

        return $this->toHtmlString('<meta'.$this->attributes($attributes).'>');
    }

    /**
     * Generate an html tag.
     *
     * @param  string  $tag
     * @param  mixed  $content
     * @param  array  $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function tag(string $tag, $content, array $attributes = []): Htmlable
    {
        $content = \is_array($content) ? \implode('', $content) : $content;

        return $this->toHtmlString('<'.$tag.$this->attributes($attributes).'>'.$content.'</'.$tag.'>');
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
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasComponent($method)) {
            return $this->renderComponent($method, $parameters);
        }

        return $this->macroCall($method, $parameters);
    }
}
