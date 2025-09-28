<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'message',
        'read_at',
        'edited_at'
    ];

    protected $dates = [
        'read_at',
        'edited_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    /**
     * Check if the message has been edited
     *
     * @return bool
     */
    public function isEdited()
    {
        return !is_null($this->edited_at);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
