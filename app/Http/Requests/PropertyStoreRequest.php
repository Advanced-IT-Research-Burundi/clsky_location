<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'postal_code' => ['required', 'string'],

            'price' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'area' => ['required', 'numeric', 'min:0'],
            'floor' => ['nullable', 'integer', 'min:0'],

            'type' => ['required', 'string', 'in:apartment,studio,duplex'],
            'status' => ['required', 'string', 'in:available,rented,maintenance'],

            // ✅ checkbox = nullable + boolean
            'furnished' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],

            // ✅ images
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],

            // ✅ services
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'furnished' => $this->boolean('furnished'),
            'featured' => $this->boolean('featured'),
        ]);
    }
}
