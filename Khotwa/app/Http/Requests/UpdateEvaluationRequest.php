<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UpdateEvaluationRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'punctuality'  => ['sometimes', 'integer', 'between:1,5'],
            'work_quality' => ['sometimes', 'integer', 'between:1,5'],
            'teamwork'     => ['sometimes', 'integer', 'between:1,5'],
            'initiative'   => ['sometimes', 'integer', 'between:1,5'],
            'discipline'   => ['sometimes', 'integer', 'between:1,5'],

            'initiated' => ['sometimes', 'boolean'],
            'mentored' => ['sometimes', 'boolean'],
            'creative_contribution' => ['sometimes', 'boolean'],
            'impactful' => ['sometimes', 'boolean'],
            'inspirational' => ['sometimes', 'boolean'],

            'notes'        => ['nullable', 'string'],
        ];
    }
}
