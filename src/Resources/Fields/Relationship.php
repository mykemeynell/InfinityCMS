<?php

namespace Infinity\Resources\Fields;

use Illuminate\Support\Collection;

/**
 * @method static \Infinity\Resources\Fields\Relationship make(string $fieldOrUsing)
 */
class Relationship extends Field
{
    protected string $using;
    protected string $by;
    public bool $canBeEmpty = false;

    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        $relationship = $this->model->{$this->getUsing()};

        return $relationship instanceof Collection
            ? $relationship->map(function($r) {
                return call_user_func_array([$r, $this->by], []);
            })->implode(", ")
            : call_user_func_array([$relationship, $this->by], []);
    }

    /**
     * Set the relationship method on the model.
     *
     * @param string $relationship
     *
     * @return $this
     */
    public function using(string $relationship): Relationship
    {
        $this->using = $relationship;
        return $this;
    }

    /**
     * Set the by method that is used to output the label values.
     *
     * @param string $method
     *
     * @return $this
     */
    public function by(string $method): Relationship
    {
        $this->by = $method;
        return $this;
    }

    /**
     * Allows the selected options to have an empty/null value.
     *
     * @return $this
     */
    public function canBeEmpty(): Relationship
    {
        $this->canBeEmpty = true;
        return $this;
    }

    /**
     * Get the using property value.
     *
     * @return string
     */
    public function getUsing(): string
    {
        if(empty($this->using)) {
            return $this->field;
        }

        return $this->using;
    }

    /**
     * Get the by property value.
     *
     * @return string
     */
    public function getBy(): string
    {
        return $this->by;
    }
}
