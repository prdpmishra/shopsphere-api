<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\ValidationException;
use App\Validation\Validator;

abstract class FormRequest extends Request
{
    abstract public function rules(): array;

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
