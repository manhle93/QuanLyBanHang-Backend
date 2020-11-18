<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->hasOne('App\User', 'user_id', 'id');
    }
}
