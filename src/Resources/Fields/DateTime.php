<?php

namespace Infinity\Resources\Fields;

class DateTime extends Field
{
    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->modelValue;
    }
}
