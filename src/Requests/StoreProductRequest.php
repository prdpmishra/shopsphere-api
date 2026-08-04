<?php

declare(strict_types=1);

namespace App\Requests;

use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Validation\Validator;

class StoreProductRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function validated(): array
    {
        $data = $this->all();

        $validator = new Validator($data);

        $validator->validate($this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        return $data;
    }
}
