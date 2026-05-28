<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'category',
        'due_date',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_completed', false)
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now()->toDateString());
    }

    // Accessors
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'success',
            default  => 'secondary',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'personal' => 'bi-person',
            'work'     => 'bi-briefcase',
            'shopping' => 'bi-cart',
            'health'   => 'bi-heart-pulse',
            default    => 'bi-tag',
        };
    }
}
