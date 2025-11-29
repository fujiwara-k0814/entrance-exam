<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path',
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    public function delivery_address()
    {
        return $this->hasOne(DeliveryAddress::class);
    }

    public function sentMessages()
    {
        return $this->hasmany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasmany(Message::class, 'receiver_id');
    }

    public function evaluationsReceived()
    {
        return $this->hasmany(UserEvaluation::class, 'targeter_id');
    }

    public function evaluationsGiven()
    {
        return $this->hasmany(UserEvaluation::class, 'evaluator_id');
    }

    //Users→Comments→Itemsの紐づけ
    public function commentedItem()
    {
        return $this->hasManyThrough(
            Item::class,
            Comment::class,
            'user_id',
            'id',
            'id',
            'item_id'
        );
    }

    //Users→Likes→Itemsの紐づけ
    public function likedItems()
    {
        return $this->hasManyThrough(
            Item::class,
            Like::class,
            'user_id',
            'id',
            'id',
            'item_id'
        );
    }

    //Users→Sells→Itemの紐づけ
    public function soldItems()
    {
        return $this->hasManyThrough(
            Item::class,
            Sell::class,
            'user_id',
            'id',
            'id',
            'item_id'
        );
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
