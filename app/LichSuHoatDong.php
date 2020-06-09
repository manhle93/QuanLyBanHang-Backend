<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LichSuHoatDong extends Model
{
    protected $guarded = [];
    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
