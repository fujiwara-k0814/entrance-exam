<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'condition_id',
        'delivery_address_id',
        'image_path',
        'name',
        'brand',
        'price',
        'description',
        'buyer_completed',
        'seller_completed',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function delivery_address()
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function sell()
    {
        return $this->hasOne(Sell::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    //Items→Comments→Usersの紐づけ
    public function commentedUser(){
        return $this->hasManyThrough(
            User::class,
            Comment::class,
            'item_id',
            'id',
            'id',
            'user_id'
        );
    }

    //検索項目
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }
    }
}
