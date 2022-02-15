<?php

namespace Infinity\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Infinity\Resources\Fields\FieldSet;
use Infinity\Resources\Fields\Relationship;

trait ParsesRelationships
{
    use DeletesForeignRelationships, CreatesUpdatesForeignRelationships;

    /**
     * Update any fields that are specified as relationships.
     *
     * @param array|\Illuminate\Support\Collection $attributes
     * @param \Illuminate\Support\Collection       $relationships
     * @param \Illuminate\Database\Eloquent\Model  $parentModel
     *
     * @return void
     * @throws \Exception
     */
    protected function updateForeignRelationships(array|Collection $attributes, Collection $relationships, Model $parentModel)
    {
        if(!$attributes instanceof Collection) {
            $attributes = collect($attributes);
        }

        // Narrowing down the attributes, so we only have the ones that are
        // actually relationships.
        $attributes = $attributes->only($relationships->keys());

        foreach($relationships as $relationship) {
            /** @var \Infinity\Resources\Fields\Relationship $relationship */
            $relationType = $this->getRelationshipType($relationship);

            if(!method_exists($this, sprintf("update%s", class_basename($relationType)))) continue;

            match ($relationType::class) {
                BelongsToMany::class => $this->updateBelongsToMany($relationType, $attributes->get($relationship->getFieldName()), $parentModel),
                default => throw new \Exception(sprintf("Relation type [%s] cannot be handled.", $relationType::class))
            };
        }
    }

    /**
     * Handle deleting foreign relationships.
     *
     * @throws \Exception
     */
    protected function deleteForeignRelationships(Collection $relationships, Model $parentModel): void
    {
        foreach($relationships as $relationship) {
            /** @var \Infinity\Resources\Fields\Relationship $relationship */
            $relationType = $this->getRelationshipType($relationship);

            if(!method_exists($this, sprintf("delete%s", class_basename($relationType)))) continue;

            match ($relationType::class) {
                BelongsToMany::class => $this->deleteBelongsToMany($relationType, $relationship, $parentModel),
                default => throw new \Exception(sprintf("Relation type [%s] cannot be handled.", $relationType::class))
            };
        }
    }

    /**
     * Remove fields from attributes that are marked as relationships.
     *
     * @param array                               $attributes
     * @param \Infinity\Resources\Fields\FieldSet $fields
     *
     * @return array
     */
    protected function removeForeignRelationships(array $attributes, FieldSet $fields): array
    {
        return Arr::except($attributes, $fields->getRelationships()->reject(function($relationship) {
            /** @var \Infinity\Resources\Fields\Relationship $relationship */
            /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
            $relation = call_user_func([$relationship->getModel(), $relationship->getUsing()]);

            return !Str::contains(Str::afterLast($relation::class, "\\"), "Many");
        })->keys()->toArray());
    }

    /**
     * Get the model relation type.
     *
     * @param \Infinity\Resources\Fields\Relationship $relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function getRelationshipType(Relationship $relationship): Relation
    {
        return call_user_func([$relationship->getModel(), $relationship->getUsing()]);
    }
}
