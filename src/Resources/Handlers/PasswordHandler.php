<?php

namespace Infinity\Resources\Handlers;

class PasswordHandler extends Handler
{
    /**
     * @inheritDoc
     */
    public function fieldDataType(): string
    {
        return 'password';
    }
}
