<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'postal_code',
        'address',
        'building'
    ];

    public function item(){
        return $this->hasOne(Item::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
