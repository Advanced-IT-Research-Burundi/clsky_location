<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Champs non modifiables → optionnels ou absents
            'reservation_id' => ['sometimes', 'integer', 'exists:reservations,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],

            // Champs réellement modifiables
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'payment_method' => ['sometimes', 'in:card,bank_transfer,cash'],
            'transaction_id' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,completed,failed,refunded'],
        ];
    }
}
