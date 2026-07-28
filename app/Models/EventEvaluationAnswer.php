<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Diperbaiki ke Relations\BelongsTo

class EventEvaluationAnswer extends Model
{
    use HasFactory;

    protected $table = 'event_evaluation_answers';

    protected $fillable = [
        'registration_id',
        'evaluation_id',
        'rating',
        'answer_text'
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(EventEvaluation::class, 'evaluation_id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'registration_id');
    }
}