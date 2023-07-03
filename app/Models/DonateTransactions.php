<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonateTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'user_id',
        'phone',
        'email',
        'location',
        'payment_method',
        'amount',
        'screenshot_img',
    ];
}
