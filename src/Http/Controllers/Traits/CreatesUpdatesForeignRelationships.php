<?php

namespace Infinity\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

trait CreatesUpdatesForeignRelationships
{
    /**
     * Update belongs to many relationship.
     *
     * @param \Illuminate\Database\Eloquent\Relations\BelongsToMany $relationType
     * @param array                                                 $attributes
     * @param \Illuminate\Database\Eloquent\Model                   $parentModel
     *
     * @return void
     */
    protected function updateBelongsToMany(BelongsToMany $relationType, array $attributes, Model $parentModel): void
    {
        $pivotTable = $relationType->getTable();
        $pivotBuilder = DB::table($pivotTable);

        // First remove all entries where the current model is set as the foreignPivotKey.
        $pivotBuilder->where($relationType->getForeignPivotKeyName(), $relationType->getParent()->getKey())->delete();

        $parent = $relationType->getParent()->getKey()
            ? $relationType->getParent()
            : $parentModel;

        // Create new entries with foreignPivotKey and relatedPivotKey on table
        foreach($attributes as $attribute) {
            $pivotBuilder->insert([
                $relationType->getForeignPivotKeyName() => $parent->getKey(),
                $relationType->getRelatedPivotKeyName() => $attribute
            ]);
        }
    }
}
