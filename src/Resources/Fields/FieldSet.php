<?php

namespace Infinity\Resources\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Infinity\Resources\Resource;

class FieldSet implements \ArrayAccess, \IteratorAggregate
{
    protected array $attributes = [];
    protected Model $model;

    /**
     * @throws \Exception
     */
    public function __construct(Resource $resource, Model $model, string $fieldsSetName = 'fields')
    {
        $this->attributes = $resource->mapModelFieldValue($model, $fieldsSetName);
        $this->model = $model;
    }

    /**
     * Get any field that is a relationship type.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRelationships(): Collection
    {
        return collect($this->attributes)->filter(function (Field $field) {
            return $field instanceof Relationship;
        });
    }

    /**
     * Get the foreign relationships from the current fieldset.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getForeignRelationships(): Collection
    {
        return collect($this->attributes)->filter(function (Field|Relationship $field) {
            if(
                !method_exists($field->getModel(), $field->getFieldName()) ||
                !is_a($field, Relationship::class)
            ) {
                return false;
            }

            /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
            $relation = call_user_func([$field->getModel(), $field->getFieldName()]);

            return Str::contains(Str::afterLast($relation::class, "\\"), "Many");
        });
    }

    /**
     * Get the field set as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(): Model
    {
        return $this->model;
    }

    /**
     * Set the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Get columns that have been bound into this field set.
     *
     * @return \Illuminate\Support\Collection
     */
    public function boundColumns(): Collection
    {
        return collect(array_keys($this->toArray()));
    }

    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function __get(string $name)
    {
        return $this->offsetGet($name);
    }

    public function __set(string $name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function __unset(string $name): void
    {
        $this->offsetUnset($name);
    }

    /**
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        if(method_exists($this->model, $name)) {
            return call_user_func_array([$this->model, $name], $arguments);
        }

        throw new \Exception(sprintf("Method %s was not found on object %s", $name, get_class($this->model)));
    }

    public function offsetExists(mixed $offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset)
    {
        unset($this->attributes[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
}
