<?php

namespace Infinity\Resources\Handlers;

use Illuminate\View\View;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Resource;

interface HandlerInterface
{
    /**
     * Handle the resource field.
     *
     * @param \Infinity\Resources\Fields\Field $field
     * @param \Infinity\Resources\Resource     $resource
     *
     * @return \Illuminate\View\View
     */
    public function handle(Field $field, Resource $resource): View;

    /**
     * Set the view name.
     *
     * @param string $viewName
     *
     * @return \Infinity\Resources\Handlers\HandlerInterface
     */
    public function setViewName(string $viewName): HandlerInterface;

    /**
     * Get the view name if specified, or return default.
     *
     * @return string
     */
    public function getView(): string;

    /**
     * The data type of the field - this is also used to generate the view name
     * if no view is specified.
     *
     * @return string
     */
    public function fieldDataType(): string;
}
