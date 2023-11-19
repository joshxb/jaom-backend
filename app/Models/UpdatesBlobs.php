<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatesBlobs extends Model
{
    use HasFactory;

    protected $fillable = [
        'updates_blob_id',
        'data_blob',
        'file_name'
    ];
}
