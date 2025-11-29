<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'targeter_id',
        'evaluator_id',
        'score',
    ];

    public function targeter()
    {
        return $this->belongsTo(User::class, 'targeter_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
