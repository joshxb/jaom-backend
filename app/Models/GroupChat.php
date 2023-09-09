<?php

namespace App\Models;

use App\Models\GroupMessage;
use App\Models\GroupUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class GroupChat extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'user_id', 'group_image', 'left_active_count', 'type'];

    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }
}
