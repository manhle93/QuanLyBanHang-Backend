<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ThamDinhPheDuyet extends Model
{
    protected $guarded = [];
    
    public function files()
    {
        return $this->hasMany('App\File', 'reference_id')->where('type', 'tham_duyet');
    }
}
