<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * we don't use this model in the cms
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'department_id', 'ticket_id', 'title', 'priority', 'message', 'status','picture'
    ];
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
