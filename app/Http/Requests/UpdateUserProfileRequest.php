<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tagline' => ['string', 'nullable'],
            'about' => ['string', 'min:20', 'nullable'],
            'formatted_address' => ['string', 'nullable'],
            'location.latitude' => ['numeric', 'min:-90', 'max:90', 'nullable'],
            'location.longitude' => ['numeric', 'min:-180', 'max:180', 'nullable']
        ];
    }
}
