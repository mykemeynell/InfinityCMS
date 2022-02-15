<?php

namespace Infinity\Resources\Handlers;

class BooleanHandler extends Handler
{
    protected string $viewName = 'infinity::fields.checkbox';

    public function fieldDataType(): string
    {
        return 'boolean';
    }
}
