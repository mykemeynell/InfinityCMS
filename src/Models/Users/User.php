<?php

namespace Infinity\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Infinity\Database\Observers\UsernameObserver;
use Infinity\Traits\CanDisplay;
use Infinity\Traits\TestAttributes;
use Infinity\Traits\UsesPermissions;
use JetBrains\PhpStorm\Pure;
use UuidColumn\Concern\HasUuidObserver;
use UuidColumn\Observer\UuidObserver;

class User extends Authenticatable implements UserModelInterface
{
    use HasUuidObserver, UsesPermissions, CanDisplay, TestAttributes;

    protected $guarded = [];

    /** @inheritdoc */
    public $incrementing = false;

    /** @inheritdoc */
    protected $keyType = 'string';

    /** @inheritdoc */
    protected $fillable = [
        'name',
        'email',
        'username',
        'email_verified_at',
        'password',
        'remember_token',
        'last_logged_in_at',
        'is_suspended',
        'group_id'
    ];

    /** @inheritdoc */
    protected $hidden = [
        'password',
    ];

    /** @inheritdoc */
    protected $table = 'users';

    /** @inheritdoc */
    protected $dates = [
        'last_logged_in_at',
        'email_verified_at',
    ];

    /** @inheritdoc */
    protected $casts = [
        'is_suspended' => 'bool',
    ];

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();
        static::observe(UUIDObserver::class);
        static::observe(UsernameObserver::class);
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getDisplayName(): string
    {
        return ucwords($this->getName());
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    /**
     * @inheritDoc
     */
    public function getLastLoggedIn(): ?Carbon
    {
        return $this->last_logged_in_at;
    }

    /**
     * @inheritDoc
     */
    public function getEmailVerifiedAt(): ?Carbon
    {
        return $this->email_verified_at;
    }

    /**
     * Test if the email has been verified.
     *
     * @return bool
     */
    public function emailIsVerified(): bool
    {
        return !empty($this->email_verified_at);
    }

    /**
     * Test if the user has an ID.
     *
     * @return bool
     */
    public function hasId(): bool
    {
        return !empty($this->getKey());
    }

    /**
     * Set the password attribute.
     *
     * @param $value
     *
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Set the is_suspended attribute.
     *
     * @param $value
     *
     * @return void
     */
    public function setIsSuspendedAttribute($value): void
    {
        $this->attributes['is_suspended'] = $value == "on";
    }
}
