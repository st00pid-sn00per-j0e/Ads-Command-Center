<?php

namespace App\Http\Requests;

use App\Models\ApprovalRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->status === 'active';
    }

    public function rules(): array
    {
        return ['google_ads_account_id' => ['required', 'integer', 'exists:google_ads_accounts,id'], 'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'], 'type' => ['required', Rule::in(ApprovalRequest::TYPES)], 'payload' => ['required', 'array'], 'reason' => ['required', 'string', 'max:2000']];
    }
}
