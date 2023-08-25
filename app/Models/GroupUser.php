<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupChat;

class GroupUser extends Model
{
    use HasFactory;

    protected $table = 'group_user';

    protected $fillable = [
        'group_id',
        'user_id',
        'left_active_count'
    ];

    public function group()
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class, 'group_id');
    }
}
