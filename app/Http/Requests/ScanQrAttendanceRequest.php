<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id'  => ['required', 'integer', 'exists:campaigns,id'],
            'qr_code_data' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required'  => 'Campaign ID is required.',
            'campaign_id.exists'    => 'Selected campaign does not exist.',
            'qr_code_data.required' => 'QR Code data is required from scanner.',
        ];
    }
}