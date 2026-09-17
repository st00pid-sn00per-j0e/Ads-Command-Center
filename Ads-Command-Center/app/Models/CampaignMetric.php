<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMetric extends Model
{
    protected $fillable = ['campaign_id', 'date', 'impressions', 'clicks', 'cost_micros', 'conversions', 'conversion_value', 'ctr', 'average_cpc_micros', 'cost_per_conversion_micros'];

    protected function casts(): array
    {
        return ['date' => 'date', 'conversions' => 'decimal:4', 'conversion_value' => 'decimal:4'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
