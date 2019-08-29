<?php

namespace Collective\Html\Eloquent;

use Illuminate\Support\Str;

trait FormAccessible
{
    /**
     * Get form value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getFormValue(string $key)
    {
        $value = $this->getAttributeFromArray($key);

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        if (\in_array($key, $this->getDates())) {
            if (! \is_null($value)) {
                $value = $this->asDateTime($value);
            }
        }

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasFormMutator($key)) {
            return $this->mutateFormAttribute($key, $value);
        }

        $keys = \explode('.', $key);

        if ($this->isNestedModel($keys[0])) {
            $relatedModel = $this->getRelation($keys[0]);

            unset($keys[0]);
            $key = \implode('.', $keys);

            if (\method_exists($relatedModel, 'hasFormMutator') && $key !== '' && $relatedModel->hasFormMutator($key)) {
                return $relatedModel->getFormValue($key);
            }

            return \data_get($relatedModel, empty($key) ? null : $key);
        }

        // No form mutator, let the model resolve this
        return \data_get($this, $key);
    }

    /**
     * Check for a nested model.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function isNestedModel(string $key): bool
    {
        return \in_array($key, \array_keys($this->getRelations()));
    }

    /**
     * Detect whether key has form mutator.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function hasFormMutator(string $key): bool
    {
        return \method_exists($this, 'form'.Str::studly($key).'Attribute');
    }

    /**
     * Mutate form attribute.
     *
     * @param string  $key
     * @param mixed  $value
     *
     * @return mixed
     */
    private function mutateFormAttribute(string $key, $value)
    {
        return $this->{'form'.Str::studly($key).'Attribute'}($value);
    }
}
