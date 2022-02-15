<?php

namespace Infinity\Models;

use Illuminate\Support\Str;
use Infinity\Facades\Infinity;

/**
 * @property string $key
 * @property string $name
 * @property string|null $description
 */
class Permission extends Model
{
    /** @inheritdoc */
    public $timestamps = false;

    /** @inheritdoc */
    protected $table = 'permissions';

    /** @inheritdoc */
    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    /** @inheritdoc */
    protected $hidden = [];

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return !empty($this->name)
            ? $this->name
            : Str::title(Str::replace('_', ' ', implode(" ", preg_split('/(?=[A-Z])/', $this->getPermissionKey()))));;
    }

    /**
     * Get the permission key, this is unique to each permission for a group.
     *
     * @return string
     */
    public function getPermissionKey(): string
    {
        return $this->key;
    }

    /**
     * Get the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get groups that have permission assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(
    ): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Infinity::model('Group'),
            Infinity::model('GroupPermissionRelationship')->getTable(),
            'permission_id',
            'group_id'
        );
    }
}
