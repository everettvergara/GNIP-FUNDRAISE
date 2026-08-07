<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paymentReleaseId = $this->input('id');

        return [
            'control_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_releases', 'control_number')->ignore($paymentReleaseId),
            ],
            'released_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
