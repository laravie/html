<?php

namespace Collective\Html\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;

trait Checker
{
    /**
     * Create a checkbox input field.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool|null  $checked
     * @param  array  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function checkbox(string $name, $value = 1, ?bool $checked = null, array $options = []): Htmlable
    {
        return $this->checkable('checkbox', $name, $value, $checked, $options);
    }

    /**
     * Create a radio button input field.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool|null  $checked
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function radio(string $name, $value = null, ?bool $checked = null, array $options = []): Htmlable
    {
        \is_null($value) && $value = $name;

        return $this->checkable('radio', $name, $value, $checked, $options);
    }

    /**
     * Create a checkable input field.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool|null  $checked
     * @param  array   $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    protected function checkable(string $type, string $name, $value, ?bool $checked, array $options): Htmlable
    {
        $this->type = $type;

        $checked = $this->getCheckedState($type, $name, $value, $checked);

        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input($type, $name, $value, $options);
    }

    /**
     * Get the check state for a checkable input.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  mixed   $value
     * @param  bool|null  $checked
     *
     * @return bool|null
     */
    protected function getCheckedState(string $type, string $name, $value, ?bool $checked): ?bool
    {
        switch ($type) {
            case 'checkbox':
                return $this->getCheckboxCheckedState($name, $value, $checked);
            case 'radio':
                return $this->getRadioCheckedState($name, $value, $checked);
            default:
                return $this->compareValues($name, $value);
        }
    }

    /**
     * Get the check state for a checkbox input.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool|null  $checked
     *
     * @return bool|null
     */
    protected function getCheckboxCheckedState(string $name, $value, ?bool $checked): ?bool
    {
        $request = $this->request($name);

        if (isset($this->session) && ! $this->oldInputIsEmpty() && \is_null($this->old($name)) && ! $request) {
            return false;
        }

        if ($this->missingOldAndModel($name) && ! $request) {
            return $checked;
        }

        $posted = $this->getValueAttribute($name, $checked);

        if (\is_array($posted)) {
            return \in_array($value, $posted);
        } elseif ($posted instanceof Collection) {
            return $posted->contains('id', $value);
        }

        return (bool) $posted;
    }

    /**
     * Get the check state for a radio input.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  bool|null  $checked
     *
     * @return bool|null
     */
    protected function getRadioCheckedState(string $name, $value, ?bool $checked): ?bool
    {
        $request = $this->request($name);

        if ($this->missingOldAndModel($name) && ! $request) {
            return $checked;
        }

        return $this->compareValues($name, $value);
    }

    /**
     * Determine if the provide value loosely compares to the value assigned to the field.
     * Use loose comparison because Laravel model casting may be in affect and therefore
     * 1 == true and 0 == false.
     *
     * @param  string|null  $name
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function compareValues(?string $name, $value): bool
    {
        return $this->getValueAttribute($name) == $value;
    }
}
