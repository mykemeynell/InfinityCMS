<?php

namespace Infinity\Resources;

use Infinity\Actions\ViewAction;
use Infinity\Resources\Fields\ID;
use Infinity\Resources\Fields\Relationship;
use Infinity\Resources\Fields\Text;

class Group extends Resource
{
    public static string $model = 'Infinity\Models\Group';
    public static string $icon = 'fas fa-users / fad fa-users';

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return [
            ID::make()->hidden(),
            Text::make('name'),
            Text::make('description')->empty('&mdash; None &mdash;'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function formFields(): array
    {
        return array_merge_recursive($this->fields(), [
            Relationship::make('permissions')->by('getDisplayName')
        ]);
    }

    /**
     * @inheritDoc
     */
    public function excludedActions(): array
    {
        return [ViewAction::class];
    }
}
