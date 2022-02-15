<?php

namespace Infinity\Resources\Handlers;

class WYSIWYGHandler extends Handler
{
    /**
     * @inheritDoc
     */
    public function fieldDataType(): string
    {
        return 'wysiwyg';
    }
}
