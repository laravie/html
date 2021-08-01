<?php

namespace Collective\Html\Concerns;

use Illuminate\Support\Arr;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property string  $type
 */
trait Selection
{
    /**
     * Create a select box field.
     *
     * @param  string $name
     * @param  iterable  $list
     * @param  string|array|null  $selected
     * @param  array  $selectAttributes
     * @param  array  $optionsAttributes
     * @param  array  $optgroupsAttributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function select(
        string $name,
        iterable $list = [],
        $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
        array $optgroupsAttributes = []
    ): Htmlable {
        $this->type = 'select';

        // When building a select box the "value" attribute is really the selected one
        // so we will use that when checking the model or session for a value which
        // should provide a convenient method of re-populating the forms on post.
        $selected = $this->getValueAttribute($name, $selected);

        $selectAttributes['id'] = $this->getIdAttribute($name, $selectAttributes);

        if (! isset($selectAttributes['name'])) {
            $selectAttributes['name'] = $name;
        }

        // We will simply loop through the options and build an HTML value for each of
        // them until we have an array of HTML declarations. Then we will join them
        // all together into one single HTML element that can be put on the form.
        $html = [];

        if (isset($selectAttributes['placeholder'])) {
            $html[] = $this->placeholderOption($selectAttributes['placeholder'], $selected);
            unset($selectAttributes['placeholder']);
        }

        if (isset($selectAttributes['native-placeholder'])) {
            $selectAttributes['placeholder'] = $selectAttributes['native-placeholder'];
            unset($selectAttributes['native-placeholder']);
        }

        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption(
                $display, $value, $selected, $optionsAttributes[$value] ?? [], $optgroupsAttributes[$value] ?? []
            );
        }

        // Once we have all of this HTML, we can join this into a single element after
        // formatting the attributes into an HTML "attributes" string, then we will
        // build out a final select statement, which will contain all the values.
        $options = $this->getHtmlBuilder()->attributes($selectAttributes);

        $list = \implode('', $html);

        return $this->toHtmlString("<select{$options}>{$list}</select>");
    }

    /**
     * Create a datalist box field.
     *
     * @param  string $id
     * @param  array  $list
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function datalist(string $id, array $list = []): Htmlable
    {
        $this->type = 'datalist';

        $attributes['id'] = $id;

        $html = [];

        if (Arr::isAssoc($list)) {
            foreach ($list as $value => $display) {
                $html[] = $this->option($display, $value, null, []);
            }
        } else {
            foreach ($list as $value) {
                $html[] = $this->option($value, $value, null, []);
            }
        }

        $attributes = $this->getHtmlBuilder()->attributes($attributes);

        $list = \implode('', $html);

        return $this->toHtmlString("<datalist{$attributes}>{$list}</datalist>");
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string|array  $display
     * @param  string  $value
     * @param  string|array|null  $selected
     * @param  array  $attributes
     * @param  array  $optgroups
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function getSelectOption(
        $display,
        string $value,
        $selected,
        array $attributes = [],
        array $optgroups = []
    ): Htmlable {
        if (\is_iterable($display)) {
            return $this->optionGroup($display, $value, $selected, $optgroups, $attributes);
        }

        return $this->option($display, $value, $selected, $attributes);
    }

    /**
     * Create an option group form element.
     *
     * @param  iterable   $list
     * @param  string  $label
     * @param  string|array|null  $selected
     * @param  array  $attributes
     * @param  array  $optionsAttributes
     * @param  int  $level
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function optionGroup(
        iterable $list,
        string $label,
        $selected,
        array $attributes = [],
        array $optionsAttributes = [],
        int $level = 0
    ): Htmlable {
        $html  = [];
        $space = \str_repeat('&nbsp;', $level);

        foreach ($list as $value => $display) {
            if (\is_iterable($display)) {
                $html[] = $this->optionGroup(
                    $display, $value, $selected, $attributes, $optionsAttributes[$value] ?? [], $level + 5
                );
            } else {
                $html[] = $this->option(
                    $space.$display, $value, $selected, $optionsAttributes[$value] ?? []
                );
            }
        }

        return $this->toHtmlString(\sprintf(
            '<optgroup label="%s"%s>%s</optgroup>',
            $this->entities($label),
            $this->getHtmlBuilder()->attributes($attributes),
            \implode('', $html)
        ));
    }

    /**
     * Create a select element option.
     *
     * @param  string  $display
     * @param  string|int  $value
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable|bool|null  $selected
     * @param  array  $attributes
     * @param  array  $optgroups
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function option(
        string $display,
        $value,
        $selected,
        array $attributes = [],
        array $optgroups = []
    ): Htmlable {
        $selected = $this->getSelectedValue($value, $selected);

        $options = ['value' => $value, 'selected' => $selected] + $attributes;

        return $this->toHtmlString(\sprintf(
            '<option%s>%s</option>',
            $this->getHtmlBuilder()->attributes($options),
            $this->entities($display)
        ));
    }

    /**
     * Create a placeholder select element option.
     *
     * @param string  $display
     * @param string|iterable|null  $selected
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function placeholderOption(string $display, $selected): Htmlable
    {
        $options = [
            'selected' => $this->getSelectedValue(null, $selected),
            'disabled' => true,
            'value'    => '',
        ];

        return $this->toHtmlString(\sprintf(
            '<option%s>%s</option>',
            $this->getHtmlBuilder()->attributes($options),
            $this->entities($display)
        ));
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string|int|bool|null  $value
     * @param  mixed|null  $selected
     *
     * @return string|bool|null
     */
    protected function getSelectedValue($value, $selected)
    {
        $selection = $selected instanceof Arrayable ? $selected->toArray() : $selected;

        if (\is_array($selection)) {
            if (\in_array($value, $selection, true) || \in_array((string) $value, $selection, true)) {
                return 'selected';
            } elseif ($selected instanceof Collection) {
                return $selected->contains($value) ? 'selected' : null;
            }

            return null;
        }

        if (\is_int($value) && \is_bool($selected)) {
            return (bool) $value === $selected;
        }

        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    /**
     * Create a select range field.
     *
     * @param  string  $name
     * @param  string|int  $begin
     * @param  string|int  $end
     * @param  string|array|null  $selected
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function selectRange(string $name, $begin, $end, $selected = null, array $options = []): Htmlable
    {
        $range = \array_combine($range = \range($begin, $end), $range);

        return $this->select($name, $range, $selected, $options);
    }

    /**
     * Create a select year field.
     *
     * @param  string  $name
     * @param  string|int  $begin
     * @param  string|int  $end
     * @param  string|array|null  $selected
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function selectYear(string $name, $begin, $end, $selected = null, array $options = []): Htmlable
    {
        return $this->selectRange($name, $begin, $end, $selected, $options);
    }

    /**
     * Create a select month field.
     *
     * @param  string  $name
     * @param  string|array|null  $selected
     * @param  array   $options
     * @param  string  $format
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function selectMonth(string $name, $selected = null, array $options = [], string $format = '%B'): Htmlable
    {
        $months = [];

        foreach (\range(1, 12) as $month) {
            $months[$month] = \ucfirst((string) \strftime($format, (int) \mktime(0, 0, 0, $month, 1)));
        }

        return $this->select($name, $months, $selected, $options);
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

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return ?string
     */
    abstract public function getIdAttribute(string $name, array $attributes): ?string;

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  string|array|null  $value
     *
     * @return mixed
     */
    abstract public function getValueAttribute(string $name, $value = null);
}
