<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepenseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date_depense' => 'required|date',
            'categorie_id' => 'required|exists:categories,id'
        ];
    }
}
