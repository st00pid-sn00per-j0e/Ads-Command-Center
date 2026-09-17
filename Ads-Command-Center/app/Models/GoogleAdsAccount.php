<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAdsAccount extends Model
{
    protected $fillable = ['google_ads_connection_id', 'customer_id', 'manager_customer_id', 'parent_customer_id', 'name', 'currency_code', 'time_zone', 'account_type', 'status', 'last_synced_at'];

    protected function casts(): array
    {
        return ['last_synced_at' => 'datetime'];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsConnection::class, 'google_ads_connection_id');
    }

    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'google_ads_account_specialist')->withPivot(['assigned_by', 'status'])->withTimestamps();
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
