<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name'
    ];

    public function item(){
        return $this->hasOne(Item::class);
    }
}
