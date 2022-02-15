<?php

namespace Infinity\Resources\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Infinity\Facades\Infinity;
use Infinity\Resources\Fields\Traits\UsesConditionalAttributes;
use Infinity\Resources\Handlers\HandlerInterface;
use JetBrains\PhpStorm\Pure;

abstract class Field
{
    use UsesConditionalAttributes;

    protected string $displayName;
    protected string $field;
    protected mixed $rawValue;
    protected mixed $modelValue;
    protected mixed $emptyValue = null;
    protected bool $hidden = false;
    protected bool $disabled = false;
    protected bool $readOnly = false;
    protected array $gates = [];
    protected Model $model;
    protected mixed $handler = null;
    protected ?string $view = null;
    protected array $viewData = [];

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * Handle a request for a field's value for output.
     *
     * @return mixed
     */
    abstract public function display(): mixed;

    /**
     * Set the empty value.
     *
     * @param $value
     *
     * @return $this
     */
    public function empty($value): Field
    {
        $this->emptyValue = $value;
        return $this;
    }

    /**
     * Set the view.
     *
     * @param string $name
     * @param array  $viewData
     *
     * @return $this
     */
    public function view(string $name, array $viewData = []): Field
    {
        $this->view = $name;
        $this->viewData = $viewData;
        return $this;
    }

    /**
     * Get the view.
     *
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $displayValue = $this->getDisplayValue();

        if(!empty($this->view)) {
            if(view()->exists($this->view)) {
                $view = view($this->view);

                $conditionalLogic = array_key_exists('conditional', $this->viewData)
                    ? $this->viewData['conditional']
                    : [];

                unset($this->viewData['conditional']);

                $attributes = $this->parseConditionalAttributes($this->getFieldName(), $conditionalLogic, $this->model, $this->viewData, $this->handler)->toArray();

                $viewData = array_merge(
                    $this->viewData, [
                    'modelValue' => $this->modelValue,
                    'rawValue' => $this->rawValue,
                    'model' => $this->model,
                    'field' => $this,
                    'displayValue' => $displayValue,
                    'attributes' => collect($attributes)
                ]);

                foreach($viewData as $key => $value) {
                    $view->with($key, $value);
                }

                return $view;
            }
        }

        return $displayValue;
    }

    /**
     * @throws \Exception
     */
    protected function getDisplayValue()
    {
        if(empty($this->handler)) {
            if($display = $this->display()) {
                return $display;
            }

            return $this->emptyValue;
        }

        if($this->handler instanceof HandlerInterface) {
            return call_user_func_array([$this->handler, 'handle'], [$this, $this->view]);
        }

        if(is_array($this->handler) && count($this->handler) == 2) {
            return call_user_func_array([$this->handler[0], $this->handler[1]], [$this->model]);
        }

        if(is_callable($this->handler)) {
            return call_user_func_array($this->handler, [$this->model]);
        }

        throw new \Exception(sprintf("No handler for field [%s] was found", class_basename($this)));
    }

    /**
     * Set a custom handler for this field.
     *
     * @param $handler
     *
     * @return $this
     */
    public function setHandler($handler): Field
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Test if this field has a handler property assigned.
     *
     * @return bool
     */
    public function hasHandler(): bool
    {
        return !empty($this->handler);
    }

    /**
     * Get the field handler.
     *
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return $this->handler;
    }

    /**
     * Set the field as hidden.
     *
     * @return $this
     */
    public function hidden(): Field
    {
        $this->hidden = true;
        return $this;
    }

    /**
     * Set the field as disabled.
     *
     * @return \Infinity\Resources\Fields\Field
     */
    public function disabled(): Field
    {
        $this->disabled = true;
        return $this;
    }

    /**
     * Test if the field is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Set the field as read only.
     *
     * @return $this
     */
    public function readOnly(): Field
    {
        $this->readOnly = true;
        return $this;
    }

    /**
     * Test if the field is readonly.
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * Test if a field is hidden.
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set required gates specific to a field.
     *
     * @param ...$gates
     *
     * @return $this
     */
    public function can(...$gates): Field
    {
        $this->gates = $gates;
        return $this;
    }

    /**
     * Get the gates assigned to a field.
     *
     * @return array
     */
    public function getGates(): array
    {
        return $this->gates;
    }

    /**
     * Test if this field has specified gates.
     *
     * @return bool
     */
    public function hasGates(): bool
    {
        return !empty($this->gates);
    }

    /**
     * Test if current user can interact with field.
     *
     * @return bool
     */
    public function currentUserCan(): bool
    {
        if(!$this->hasGates()) {
            return true;
        }

        foreach($this->getGates() as $gate) {
            if(auth()->user()->can($gate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Make the field.
     *
     * @param string $field
     *
     * @return \Infinity\Resources\Fields\Field
     */
    #[Pure] public static function make(string $field): Field
    {
        return new static($field);
    }

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        if(!empty($this->displayName)) {
            return $this->displayName;
        }

        return Str::title(Str::replace('_', ' ', implode(" ", preg_split('/(?=[A-Z])/', $this->field))));
    }

    /**
     * Set the display name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setDisplayName(string $name): Field
    {
        $this->displayName = $name;

        return $this;
    }

    /**
     * Get the field of the database that his field object targets.
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->field;
    }

    /**
     * Set the field raw value.
     *
     * @param $value
     *
     * @return $this
     */
    public function setRawValue($value): Field
    {
        $this->rawValue = $value;

        return $this;
    }

    /**
     * Get the raw value.
     *
     * @return mixed
     */
    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }

    /**
     * Set the value that has been generated by the model.
     *
     * @param $value
     *
     * @return $this
     */
    public function setModelValue($value): Field
    {
        $this->modelValue = $value;
        return $this;
    }

    /**
     * Get the model value.
     *
     * @return mixed
     */
    public function getModelValue(): mixed
    {
        return $this->modelValue;
    }

    /**
     * Set the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return $this
     */
    public function setModel(Model $model): Field
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Output the field as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return value($this->rawValue);
    }
}
