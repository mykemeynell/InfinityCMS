<?php

namespace Infinity\Http\Requests;

class SaveUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getPayloadNamespace(): string
    {
        return 'user';
    }
}
