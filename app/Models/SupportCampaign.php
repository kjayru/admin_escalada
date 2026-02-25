<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportCampaign extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function methods(): HasMany
    {
        return $this->hasMany(SupportMethod::class, 'campaign_id')->orderBy('sort_order');
    }

    public function activeMethods(): HasMany
    {
        return $this->hasMany(SupportMethod::class, 'campaign_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }
}
