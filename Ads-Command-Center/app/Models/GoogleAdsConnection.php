<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAdsConnection extends Model
{
    protected $fillable = ['organization_id', 'name', 'developer_token', 'client_id', 'client_secret', 'refresh_token', 'login_customer_id', 'status', 'last_synced_at'];

    protected $hidden = ['developer_token', 'client_id', 'client_secret', 'refresh_token'];

    protected function casts(): array
    {
        return ['developer_token' => 'encrypted', 'client_id' => 'encrypted', 'client_secret' => 'encrypted', 'refresh_token' => 'encrypted', 'last_synced_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(GoogleAdsAccount::class);
    }
}
