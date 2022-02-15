<?php

namespace Infinity\Resources\Fields;

class Password extends Field
{
    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
//        return $this->rawValue;
        return '***';
    }
}
