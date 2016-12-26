<?php

namespace Collective\Html\Traits;

use Illuminate\Contracts\Support\Arrayable;

trait SelectionTrait
{
    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array   $list
     * @param  string  $selected
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function select($name, $list = [], $selected = null, $options = [])
    {
        // When building a select box the "value" attribute is really the selected one
        // so we will use that when checking the model or session for a value which
        // should provide a convenient method of re-populating the forms on post.
        $selected = $this->getValueAttribute($name, $selected);

        $options['id'] = $this->getIdAttribute($name, $options);

        ! isset($options['name']) && $options['name'] = $name;

        // We will simply loop through the options and build an HTML value for each of
        // them until we have an array of HTML declarations. Then we will join them
        // all together into one single HTML element that can be put on the form.
        $html = [];

        if (isset($options['placeholder'])) {
            $html[] = $this->placeholderOption($options['placeholder'], $selected);
            unset($options['placeholder']);
        }

        if (isset($options['native-placeholder'])) {
            $options['placeholder'] = $options['native-placeholder'];
            unset($options['native-placeholder']);
        }

        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption($display, $value, $selected);
        }

        // Once we have all of this HTML, we can join this into a single element after
        // formatting the attributes into an HTML "attributes" string, then we will
        // build out a final select statement, which will contain all the values.
        $options = $this->getHtmlBuilder()->attributes($options);

        $list = implode('', $html);

        return $this->toHtmlString("<select{$options}>{$list}</select>");
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function getSelectOption($display, $value, $selected)
    {
        if (is_array($display)) {
            return $this->optionGroup($display, $value, $selected);
        }

        return $this->option($display, $value, $selected);
    }

    /**
     * Create an option group form element.
     *
     * @param  array   $list
     * @param  string  $label
     * @param  string  $selected
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function optionGroup($list, $label, $selected)
    {
        $html = [];

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected);
        }

        return $this->toHtmlString('<optgroup label="'.$this->entities($label, true).'">'.implode('', $html).'</optgroup>');
    }

    /**
     * Create a select element option.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function option($display, $value, $selected)
    {
        $selected = $this->getSelectedValue($value, $selected);

        $options = ['value' => $value, 'selected' => $selected];

        return $this->toHtmlString('<option'.$this->html->attributes($options).'>'.$this->entities($display, true).'</option>');
    }

    /**
     * Create a placeholder select element option.
     *
     * @param string  $display
     * @param string  $selected
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function placeholderOption($display, $selected)
    {
        $selected = $this->getSelectedValue(null, $selected);
        $value    = '';

        $options = compact('selected', 'value');

        return $this->toHtmlString('<option'.$this->html->attributes($options).'>'.$this->entities($display, true).'</option>');
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string  $value
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable  $selected
     *
     * @return string
     */
    protected function getSelectedValue($value, $selected)
    {
        if ($selected instanceof Arrayable) {
            $selected = $selected->toArray();
        }

        if (is_array($selected)) {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string) $value == (string) $selected) ? 'selected' : null;
    }

    /**
     * Create a select range field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function selectRange($name, $begin, $end, $selected = null, $options = [])
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range, $selected, $options);
    }

    /**
     * Create a select year field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function selectYear($name, $begin, $end, $selected = null, $options = [])
    {
        return $this->selectRange($name, $begin, $end, $selected, $options);
    }

    /**
     * Create a select month field.
     *
     * @param  string  $name
     * @param  string  $selected
     * @param  array   $options
     * @param  string  $format
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function selectMonth($name, $selected = null, $options = [], $format = '%B')
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = ucfirst(strftime($format, mktime(0, 0, 0, $month, 1)));
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
    abstract protected function entities($value, $encoding = false);

    /**
     * Get html builder.
     *
     * @return \Orchestra\Html\Support\HtmlBuilder
     */
    abstract public function getHtmlBuilder();

    /**
     * Transform the string to an Html serializable object.
     *
     * @param  string  $html
     *
     * @return \Illuminate\Support\HtmlString
     */
    abstract protected function toHtmlString($html);

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return string
     */
    abstract public function getIdAttribute($name, $attributes);

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return string
     */
    abstract public function getValueAttribute($name, $value = null);
}
