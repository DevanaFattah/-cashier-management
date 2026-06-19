<?php

namespace App\Models;

use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'snap_token',
    ];

    public function details() 
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
}
