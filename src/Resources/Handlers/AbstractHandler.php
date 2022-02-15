<?php

namespace Infinity\Resources\Handlers;

use Illuminate\View\View;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Resource;

abstract class AbstractHandler implements HandlerInterface
{
    protected Field $field;
    protected string $viewName;
    protected array $additionalViewData = [];

    /**
     * @inheritDoc
     */
    abstract public function handle(Field $field, Resource $resource): View;

    /**
     * @inheritDoc
     */
    abstract public function fieldDataType(): string;

    /**
     * Set the additional view data.
     *
     * @param array $viewData
     *
     * @return void
     */
    protected function setAdditionalViewDataArray(array $viewData): void
    {
        $this->additionalViewData = $viewData;
    }

    /**
     * Set a single key of additional view data.
     *
     * @param string $key
     * @param        $viewData
     *
     * @return void
     */
    protected function setAdditionalViewData(string $key, $viewData): void
    {
        $this->additionalViewData[$key] = $viewData;
    }

    /**
     * Make the view.
     *
     * @return \Illuminate\View\View
     */
    protected function makeView(): View
    {
        $view = view($this->getView());

        foreach($this->additionalViewData as $key => $value) {
            $view->with($key, $value);
        }

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function setViewName(string $viewName): HandlerInterface
    {
        $this->viewName = $viewName;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getView(): string
    {
        return !empty($this->viewName)
            ? $this->viewName
            : sprintf("infinity::fields.%s", $this->fieldDataType());
    }
}
