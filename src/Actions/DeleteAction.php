<?php

namespace Infinity\Actions;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DeleteAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('infinity::generic.delete');
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return fa('far fa-trash-alt', 'fad fa-trash');
    }

    /**
     * @inheritDoc
     */
    #[Pure] #[ArrayShape([
        'class' => "string",
        'data-id' => "mixed",
        'id' => "string"
    ])] public function getAttributes(): array
    {
        return [
            'class'   => 'bg-red-500 text-white active:bg-red-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 ml-2',
        ];
    }


    /**
     * @inheritDoc
     */
    public function action(): string
    {
        return 'showDelete';
    }
}
