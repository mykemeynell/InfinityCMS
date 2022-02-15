<?php

namespace Infinity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Infinity\Facades\Infinity;

/**
 * @property string $group_id
 * @property string $permission_id
 */
class GroupPermissionRelationship extends Model
{
    /** @inheritdoc  */
    protected $table = 'group_permission';

    /** @inheritdoc  */
    protected $fillable = [
        'group_id',
        'permission_id',
    ];

    /** @inheritdoc  */
    public $timestamps = false;

    /**
     * Get the group ID.
     *
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->group_id;
    }

    /**
     * Get the permission ID.
     *
     * @return string
     */
    public function getPermissionId(): string
    {
        return $this->permission_id;
    }

    /**
     * Get the user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(
            Infinity::modelClass('Permission')
        );
    }

    /**
     * Get the group relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            Infinity::modelClass('Group')
        );
    }
}
