<?php

namespace Infinity\Resources\Handlers;

use Illuminate\View\View;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Resource;

class FluidHandler extends Handler
{
    protected string $fieldDataType = 'text';

    public function handle(Field $field, Resource $resource): View
    {
        $this->fieldDataType = $field->getModel()->getAttribute('type');

        return parent::handle($field, $resource);
    }

    /**
     * @inheritDoc
     */
    public function fieldDataType(): string
    {
        return $this->fieldDataType;
    }
}
