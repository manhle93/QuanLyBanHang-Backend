<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'code', 'description', 'system'];
    public $timestamps = false;
    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'role_menus');
    }
}
