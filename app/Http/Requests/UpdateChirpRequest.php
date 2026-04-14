<?php

namespace App\Http\Requests;

use App\Models\Chirp;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChirpRequest extends FormRequest
{
    public function authorize(): bool
    {
        $chirp = $this->route('chirp');

        return $chirp instanceof Chirp
            && ($this->user()?->can('update', $chirp) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Write something before saving your chirp.',
            'message.max' => 'Chirps can be up to 255 characters long.',
        ];
    }
}
