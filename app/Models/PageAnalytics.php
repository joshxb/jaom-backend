<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'userAgent', 'device', 'browser', 'os', 'os_version', 'browser_version', 'deviceType', 'orientation'
    ];
}
