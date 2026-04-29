<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Slimani\MediaManager\Models\File as SlimaniFile;

class Member extends Model
{
    protected $fillable = [
        'member_group_id',
        'name',
        'role',
        'bio',
        'featured_home',
        'featured_media_id',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'featured_home' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(MemberGroup::class, 'member_group_id');
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(SlimaniFile::class, 'featured_media_id');
    }
}
