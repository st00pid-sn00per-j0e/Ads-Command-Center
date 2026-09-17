<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = ['google_ads_account_id', 'google_resource_name', 'google_campaign_id', 'name', 'status', 'campaign_type', 'daily_budget_micros', 'start_date', 'end_date', 'last_synced_at'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'last_synced_at' => 'datetime'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsAccount::class, 'google_ads_account_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(CampaignMetric::class);
    }
}
