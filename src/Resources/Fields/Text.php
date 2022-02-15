<?php

namespace Infinity\Resources\Fields;

class Text extends Field
{
    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->rawValue;
    }
}
