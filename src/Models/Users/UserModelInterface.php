<?php

namespace Infinity\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Infinity\Models\Group;

/**
 * @property string|null $group_id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property null|Carbon $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property bool $is_suspended
 * @property null|Carbon $last_logged_in_at
 */
interface UserModelInterface
{
    /**
     * Get the name as it should be displayed on screen.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the email address of the user account.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Test whether the user account has been suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool;

    /**
     * Get the last logged in date.
     *
     * @return \Carbon\Carbon|null
     */
    public function getLastLoggedIn(): ?Carbon;

    /**
     * Get the date that the email was verified at.
     *
     * @return \Carbon\Carbon|null
     */
    public function getEmailVerifiedAt(): ?Carbon;

    /**
     * Get the group that the user is assigned to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo;
}
