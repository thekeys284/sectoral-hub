<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\BelongsTo;

class EventQuestion extends Model
{
    use HasFactory;
    protected $table = 'event_questions';
    protected $fillable = ['event_id','type','question_text','options','correct_answer'];
    
    protected $casts = [
        'options' => 'array'
    ];
    public function event():BelongsTo{
        return $this->belongsTo(Event::class, 'event_id');
    }
}
