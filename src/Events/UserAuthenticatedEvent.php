<?php

namespace Infinity\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Infinity\Models\Users\User;

class UserAuthenticatedEvent
{
    use Dispatchable, SerializesModels;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
