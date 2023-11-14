<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessagesBlob extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_messages_blob_id',
        'data_blob',
        'file_name'
    ];
}
