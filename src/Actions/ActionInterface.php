<?php

namespace Infinity\Actions;

interface ActionInterface
{
    /**
     * Get the action identifier.
     *
     * @return string
     */
    public function action(): string;

    /**
     * Get the action title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the action icon.
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Get any attributes assigned to this action.
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Get the route assigned to this action.
     *
     * @param array $params
     *
     * @return string
     */
    public function getRoute(array $params = []): string;
}
