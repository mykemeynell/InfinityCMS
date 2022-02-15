<?php

namespace Infinity\Cards;

use Infinity\Models\Users\User;
use Infinity\Resources\Resource;

class RecentUsers extends Card
{
    public static string $title = 'Recent Users';
    public static array $groups = ['admin'];

    public function __construct(Resource $resource)
    {
        $this->setTitleButton('View All', infinity_route($resource->getIdentifier() . '.index'));

        $this->addCardData('users',
            User::query()
                ->limit(5)
                ->orderBy('last_logged_in_at', 'DESC')
                ->get()
        );
    }

    /**
     * @inheritDoc
     */
    public function view(): string
    {
        return 'infinity::cards.recent-users';
    }
}
