<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MessagesBlob extends Model
{
    use HasFactory;

    protected $fillable = [
        'messages_blob_id',
        'data_blob',
        'file_name'
    ];

    public function message()
    {
        return DB::table('message_blobs')
            ->join('messages', 'message_blobs.messages_blob_id', '=', 'messages.messages_blob_id')
            ->select('messages.*')
            ->get();
    }

}
