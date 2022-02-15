<?php

namespace Infinity\Actions;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class ViewAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return __('infinity::generic.view');
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return fa('far fa-eye', 'fad fa-eye');
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
        return 'show';
    }
}
