<?php

namespace Infinity\Resources\Handlers;

use Illuminate\View\View;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Fields\Relationship;
use Infinity\Resources\Resource;
use Infinity\Traits\CanDisplay;

class RelationshipHandler extends Handler
{
    protected string $viewName = 'infinity::fields.relationship';

    /**
     * @param \Infinity\Resources\Fields\Field|\Infinity\Resources\Fields\Relationship $field
     * @param \Infinity\Resources\Resource                                             $resource
     *
     * @return \Illuminate\View\View
     */
    public function handle(Field|Relationship $field, Resource $resource): View
    {
        /** @var Relationship $field */

        $this->setViewOptionsData($field);

        $field->setRawValue(
            call_user_func([$field->getModel(), $field->getUsing()])
        );

        return parent::handle($field, $resource);
    }

    public function fieldDataType(): string
    {
        return 'relationship';
    }

    /**
     * @param \Infinity\Resources\Fields\Relationship $field
     *
     * @return void
     */
    protected function setViewOptionsData(Relationship $field): void
    {
        // TODO: Set the options that are available to be output so if something is marked as disabled for example - it wont be displayed.
        $model = $field->getModel();

        /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
        $relation = call_user_func([$model, $field->getUsing()]);


        // TODO: Add ability to limit returned options when fetching potential values from database.
        $query = $relation->getModel()->newQuery();

        $options = $query->get()->map(function ($object) {
            if(!class_uses_trait($object, CanDisplay::class)) {
                throw new \Exception(sprintf("%s is being used as apart of a relationship, so needs to make use of the %s trait.", $object::class, CanDisplay::class));
            }

            return collect([
                'label' => call_user_func([$object, 'getDisplayName']),
                'value' => call_user_func([$object, 'getKey'])
            ]);
        });

        if($field->canBeEmpty) {
            $options->prepend(collect([
                'label' => sprintf("- %s -", __('infinity::generic.none')),
                'value' => null
            ]));
        }

        $this->setAdditionalViewData('options', $options);
    }
}
