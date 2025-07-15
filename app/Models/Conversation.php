<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    /**
     * Get the sender of the conversation.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the conversation.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The users that participate in the conversation.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user');
    }
}


