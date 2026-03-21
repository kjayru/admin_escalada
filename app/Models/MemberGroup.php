<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'status',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class)->orderBy('sort_order');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(Member::class)
            ->where('status', 'active')
            ->orderBy('sort_order');
    }
}
