<?php

namespace Infinity\Resources;

use Infinity\Actions\ViewAction;
use Infinity\Cards\RecentUsers;
use Infinity\Resources\Fields\Boolean;
use Infinity\Resources\Fields\DateTime;
use Infinity\Resources\Fields\ID;
use Infinity\Resources\Fields\Link;
use Infinity\Resources\Fields\Password;
use Infinity\Resources\Fields\Relationship;
use Infinity\Resources\Fields\Text;
use Infinity\Resources\Routes\AdditionalRoute;

class User extends Resource
{
    public static string $model = 'Infinity\Models\Users\User';
    public static ?string $controller = 'Infinity\Http\Controllers\InfinityUsersController';
    public static string $icon = 'fas fa-user-friends / fad fa-user-friends';

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return [
            ID::make()->hidden(),
            Text::make('name'),
            Text::make('email'),
            Link::make('username')->to('infinity.users.showProfile', ['username' => 'username']),
            Relationship::make('group_id')->setDisplayName('Group')->using('group')->by('getDisplayName'),

            Boolean::make('is_suspended')
                ->setDisplayName('Status')
                ->setValueIfFalse('Active')
                ->setValueIfTrue('Suspended')
                ->view('infinity::fields.badge', [
                    'attributes' => [
                        'class' => 'text-white',
                    ],
                    'conditional' => [
                        'class' => [
                            'is_suspended:true' => 'bg-red-500',
                            'is_suspended:false' => 'bg-green-500'
                        ]
                    ]
                ]),

            DateTime::make('last_logged_in_at')->empty('Never')
        ];
    }

    /**
     * @inheritDoc
     */
    public function formFields(): array
    {
        return [
            ID::make()->hidden(),
            Text::make('name'),
            Text::make('email'),
            Text::make('username'),
            Password::make('password'),

            Relationship::make('group_id')
                ->can('users.edit')
                ->setDisplayName('Group')
                ->using('group')
                ->by('getDisplayName'),

            Boolean::make('is_suspended')
        ];
    }

    /**
     * @inheritDoc
     */
    public function cards(): array
    {
        return [RecentUsers::class];
    }

    /**
     * @inheritDoc
     */
    public function excludedActions(): array
    {
        return [ViewAction::class];
    }

    /**
     * @inheritDoc
     */
    public function additionalRoutes(): array
    {
        return [
            AdditionalRoute::make('me')->setAction('showProfile')->setName('showMyProfile'),
            AdditionalRoute::make('profile/{username}')->setAction('showProfile')->setName('showProfile'),
            AdditionalRoute::make('edit/me')->setAction('showEditMe')
        ];
    }

    /**
     * @inheritDoc
     */
    public function additionalGates(): array
    {
        return ['viewProfile', 'editOwnProfile', 'changeGroup'];
    }
}
