<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineUser extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function isUserOnline($userId)
    {
        return static::where('user_id', $userId)->exists();
    }
}