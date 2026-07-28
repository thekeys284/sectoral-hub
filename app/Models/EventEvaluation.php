<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventEvaluation extends Model
{
    use HasFactory;

    protected $table = 'event_evaluations';

    protected $fillable = [
        'event_id',
        'question_text',
        'type',
        'is_master',
        'is_active',
    ];

    protected $casts = [
        'is_master' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(EventEvaluationAnswer::class, 'evaluation_id');
    }
}