<?php

namespace Infinity\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Infinity\Resources\Fields\Relationship;

trait DeletesForeignRelationships
{
    /**
     * Deletes BelongsToMany relationships.
     *
     * @param \Illuminate\Database\Eloquent\Relations\BelongsToMany $relationType
     * @param \Infinity\Resources\Fields\Relationship               $relationship
     * @param \Illuminate\Database\Eloquent\Model                   $parentModel
     *
     * @return void
     */
    protected function deleteBelongsToMany(BelongsToMany $relationType, Relationship $relationship, Model $parentModel): void
    {
        DB::table($relationType->getTable())
            ->where(
                $relationType->getForeignPivotKeyName(),
                $relationType->getParent()->getKey()
            )->delete();
    }
}
