<?php

namespace App\Models;

use App\Models\GroupMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo(GroupMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
