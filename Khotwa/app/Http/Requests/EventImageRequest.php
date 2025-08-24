<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventImageRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}
