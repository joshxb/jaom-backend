<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation;
use App\Models\User;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'body'
    ];


    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class);
    }
}
