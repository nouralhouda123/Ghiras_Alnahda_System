<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,banned'],

            'ban_reason' => [
                'required_if:status,banned',
                'nullable',
                'string',
                'max:255'
            ],

            'banned_until' => [
                'required_if:status,banned',
                'nullable',
                'date',
                'after:now'
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be active or banned',

            'banned_until.date' => 'Ban end date must be a valid date',
            'banned_until.after' => 'Ban end date must be in the future',
        ];
    }
}
