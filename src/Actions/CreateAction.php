<?php

namespace Infinity\Actions;

use JetBrains\PhpStorm\ArrayShape;

class CreateAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('infinity::generic.create_new');
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return fa('far fa-plus', 'fad fa-plus');
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['class' => "string"])] public function getAttributes(): array
    {
        return [
            'class' => 'bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-2',
        ];
    }

    /**
     * @inheritDoc
     */
    public function action(): string
    {
        return 'create';
    }
}
