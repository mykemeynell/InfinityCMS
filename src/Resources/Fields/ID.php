<?php

namespace Infinity\Resources\Fields;

use JetBrains\PhpStorm\Pure;

class ID extends Field
{
    /**
     * @inheritDoc
     */
    #[Pure] public static function make(string $field = 'id'): Field
    {
        return parent::make($field);
    }

    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->rawValue;
    }
}
