<?php

namespace Infinity\Resources\Handlers;

class ImageHandler extends Handler
{
    /**
     * @inheritDoc
     */
    public function fieldDataType(): string
    {
        return 'image';
    }
}
