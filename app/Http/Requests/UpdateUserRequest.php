<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $this->user],
            'phone' => ['sometimes', 'unique:users,phone,' . $this->user],
            'status' => ['sometimes', 'in:active,banned,suspended'],
            'department_id' => ['sometimes', 'exists:departments,id'],
        ];
    }
}
