<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_menus');
    }
    public function children()
    {
        return $this->hasMany('App\Menu', 'parent_id');
    }
    public function scopeMenu($query)
    {
        return $query->whereNull('parent_id');
    }
}
