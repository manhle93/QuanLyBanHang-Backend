<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'phone', 'active', 'role_id', 'avatar_url','tinh_thanh_id','quan_huyen_id','toa_nha_id','search', 'trang_thai_khoa', 'thoi_gian_bat_dau_khoa', 'so_lan_nhap_sai'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }
    public function employee()
    {
        return $this->hasOne('App\Employee');
    }
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh','tinh_thanh_id')->select('id','name','code');
    }
    public function quyanHuyen()
    {
        return $this->belongsTo('App\QuyanHuyen','quan_huyen_id', 'id')->select('id','name','code','tinh_thanh_id');
    }
    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha','toa_nha_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function dayOffs()
    {
        return $this->hasMany('App\DayOff', 'user_approve', 'id');
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        if ($this->role->code == "admin")
            return [
                'is_setup' => $this->company_id != null,
            ];
        return [];
    }
}
