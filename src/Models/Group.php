<?php

namespace Infinity\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Infinity\Facades\Infinity;
use Infinity\Traits\CanDisplay;
use Infinity\Traits\TestAttributes;
use UuidColumn\Concern\HasUuidObserver;

/**
 * @property string $name
 * @property \Illuminate\Support\Collection $permissions
 */
class Group extends Pivot
{
    use HasUuidObserver, CanDisplay, TestAttributes;

    /** @inheritdoc  */
    protected $table = 'groups';

    /** @inheritdoc */
    public $incrementing = false;

    /** @inheritdoc */
    protected $keyType = 'string';

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'description',
    ];

    /** @inheritdoc */
    protected $hidden = [];

    /** @inheritdoc  */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * Returns the group name as it should be displayed.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return ucwords($this->name);
    }

    /**
     * Get the users of a group.
     *
     * @return \Illuminate\Support\Collection
     */
    public function users(): Collection
    {
        return $this->hasMany(
            Infinity::model('User'),
            'group_id',
            'id'
        )->get();
    }

    /**
     * Count the number of users that belongs to a group.
     *
     * @return int
     */
    public function countUsers(): int
    {
        return $this->users()->count();
    }

    /**
     * Get the permissions through the pivot table.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Infinity::model('Permission'),
            Infinity::model('GroupPermissionRelationship')->getTable(),
            'group_id',
            'permission_id'
        );
    }
}
