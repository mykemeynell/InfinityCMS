<?php

namespace Infinity\Resources\Handlers;

use Illuminate\View\View;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Resource;

class Handler extends AbstractHandler
{
    public function handle(Field $field, Resource $resource): View
    {
        $this->setAdditionalViewData('resource', $resource);
        $this->setAdditionalViewData('field', $field);
        $this->setAdditionalViewData('modelValue', $field->getModelValue());
        $this->setAdditionalViewData('rawValue', $field->getRawValue());
        $this->setAdditionalViewData('model', $field->getModel());
        $this->setAdditionalViewData('isDisabled', $field->isDisabled());
        $this->setAdditionalViewData('isReadOnly', $field->isReadOnly());

        return $this->makeView();
    }

    /**
     * @inheritDoc
     */
    public function fieldDataType(): string
    {
        return 'text';
    }
}
