<?php

namespace Infinity\Actions;

use Infinity\Resources\Resource;
use JetBrains\PhpStorm\Pure;

abstract class AbstractAction implements ActionInterface
{
    protected Resource $resource;
    protected array $data;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the resource.
     *
     * @return \Infinity\Resources\Resource
     */
    public function getResource(): Resource
    {
        return $this->resource;
    }

    /**
     * Get the data array.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(array $params = []): string
    {
        return infinity_route(sprintf("%s.%s", $this->resource->getIdentifier(), $this->action()), $params);
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * Convert any attributes to parsable HTML.
     *
     * @return string
     */
    #[Pure] public function convertAttributesToHtml(): string
    {
        $result = '';

        foreach ($this->getAttributes() as $key => $attribute) {
            $result .= $key.'="'.$attribute.'"';
        }

        return $result;
    }
}
