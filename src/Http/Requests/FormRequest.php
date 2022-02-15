<?php

namespace Infinity\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class FormRequest extends BaseFormRequest
{
    /**
     * Get the payload namespace.
     *
     * @return string
     */
    abstract public function getPayloadNamespace(): string;

    /**
     * Create a Symfony ParameterBag containing the values from namespace.
     *
     * @param string|null $namespace
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getParameterBag(?string $namespace = null): ParameterBag
    {
        return new ParameterBag(
            $this->get($namespace ?: $this->getPayloadNamespace())
        );
    }
}
