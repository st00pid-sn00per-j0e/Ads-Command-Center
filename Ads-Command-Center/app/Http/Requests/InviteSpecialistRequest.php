<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteSpecialistRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Use policy to determine authorization
        return $this->user() && $this->user()->can('invite', \App\Models\User::class);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
