<?php

declare(strict_types=1);

namespace App\Requests;

use App\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
