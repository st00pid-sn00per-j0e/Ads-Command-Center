<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRequest extends Model
{
    public const TYPES = ['campaign_create', 'campaign_update', 'campaign_pause', 'campaign_enable', 'budget_change', 'ad_create', 'ad_update', 'ad_pause', 'keyword_create', 'keyword_update', 'keyword_pause'];

    public const TERMINAL_STATUSES = ['rejected', 'executed', 'failed', 'cancelled'];

    protected $fillable = ['organization_id', 'google_ads_account_id', 'campaign_id', 'requested_by', 'approved_by', 'type', 'status', 'payload', 'reason', 'admin_comment', 'submitted_at', 'approved_at', 'rejected_at', 'executed_at', 'execution_error'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'submitted_at' => 'datetime', 'approved_at' => 'datetime', 'rejected_at' => 'datetime', 'executed_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsAccount::class, 'google_ads_account_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
