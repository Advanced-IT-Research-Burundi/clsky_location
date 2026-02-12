<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
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
        'receiver_id' => ['required', 'exists:users,id'],
        'property_id' => ['nullable', 'exists:properties,id'],
        'subject' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
        'attachments.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf,doc,docx', 'max:10240'],
        ];
    }
}
