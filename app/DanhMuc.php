<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $guarded = [];
    public function children()
    {
        return $this->hasMany('App\DanhMuc', 'parent_id');
    }
    public function scopeDanhMucCha($query)
    {
        return $query->where('parent_id', null);
    }
}
