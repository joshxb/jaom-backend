<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupUser;
use App\Models\GroupMessage;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class GroupChat extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(GroupUser::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }
}
