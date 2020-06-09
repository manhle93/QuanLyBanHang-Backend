<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TinhThanhScope;

class DanCu extends Model
{
    protected $fillable = [
        'name', 'phone','toa_nha_id','tinh_thanh_id','don_vi_pccc_id','search'
    ];

    public function toaNha()
    {
        return $this->belongsTo('App\ToaNha');
    }

    public function tinhThanh()
    {
        return $this->belongsTo('App\TinhThanh');
    }

    public function donViPccc()
    {
        return $this->belongsTo('App\DonViPccc');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TinhThanhScope);
    }
}
