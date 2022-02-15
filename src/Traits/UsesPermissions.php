<?php

namespace Infinity\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Infinity\Facades\Infinity;

/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsTo belongsTo($related, $foreignKey = null, $localKey = null)
 * @property \Infinity\Models\Group $group
 */
trait UsesPermissions
{
    /**
     * Get the group of a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            Infinity::model('Group')
        );
    }

    /**
     * Test if user belongs to a group.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup(string $name): bool
    {
        $group = $this->group()->pluck('name')->toArray();

        return in_array($name, $group);
    }

    /**
     * Test if the user has permission to execute an action.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasPermission(string $name): bool
    {
        $this->loadPermissions();

        $_permissions = $this->group->permissions
            ->flatten()->pluck('key')->unique()
            ->toArray();

        return in_array($name, $_permissions);
    }

    /**
     * Load the permissions.
     *
     * @return void
     */
    private function loadPermissions(): void
    {
        if (!$this->relationLoaded('group')) {
            $this->load('group');
        }
    }
}
