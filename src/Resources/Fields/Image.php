<?php

namespace Infinity\Resources\Fields;

class Image extends Field
{
    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->rawValue;
    }
}
