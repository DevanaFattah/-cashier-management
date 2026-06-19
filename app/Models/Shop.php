<?php

namespace App\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $guarded = ['id'];

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
