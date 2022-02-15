<?php

namespace Infinity\Resources\Fields;

/**
 * @method static \Infinity\Resources\Fields\Link make(string $field)
 */
class Link extends Field
{
    public function __construct($field)
    {
        $this->view('infinity::fields.link');
        $this->viewData['attributes']['target'] = '_self';

        parent::__construct($field);
    }

    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->rawValue;
    }

    /**
     * Set the route name.
     *
     * @param string $routeName
     * @param array  $routeParamFieldBindings
     *
     * @return \Infinity\Resources\Fields\Link
     */
    public function to(string $routeName, array $routeParamFieldBindings = []): Link
    {
        $this->viewData['routeName'] = $routeName;
        $this->viewData['routeParamFieldBindings'] = $routeParamFieldBindings;

        return $this;
    }

    /**
     * Sets the link target.
     *
     * @param string $target
     *
     * @return \Infinity\Resources\Fields\Link
     */
    public function target(string $target): Link
    {
        $this->viewData['attributes']['target'] = $target;
        return $this;
    }
}
