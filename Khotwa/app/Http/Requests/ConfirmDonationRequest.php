<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'donation_id'    => [
                'required',
                Rule::exists('donations', 'id')->where(function ($query) {
                    return $query->where('payment_status', 'pending');
                }),
            ],
            'transaction_id' => 'required|string|max:255',
            'payment_status' => 'required|string|in:paid,failed',
            'method'         => 'required|string|max:50',
            'amount'         => 'required|numeric|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'donation_id.exists' => 'The selected donation ID is invalid or has already been processed.',
        ];
    }
}
