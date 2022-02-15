<?php

namespace Infinity\Resources\Fields;

use JetBrains\PhpStorm\Pure;

/**
 * @method static \Infinity\Resources\Fields\Boolean make(string $field)
 */
class Boolean extends Field
{
    protected mixed $trueValue = 'Yes';
    protected mixed $falseValue = 'No';

    /**
     * @inheritDoc
     */
    public function display(): mixed
    {
        return $this->rawValue ? $this->trueValue : $this->falseValue;
    }

    public function setValueIfTrue($value): static
    {
        $this->trueValue = value($value);
        return $this;
    }

    public function setValueIfFalse($value): static
    {
        $this->falseValue = value($value);
        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->display();
    }
}
