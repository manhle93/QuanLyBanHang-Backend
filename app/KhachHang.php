<?php

namespace App;
use App\Scopes\ActiveScope;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $guarded = [];
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function donDatHangs(){
        return $this->hasMany('App\DonDatHang', 'user_id', 'user_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope());
    }
}
