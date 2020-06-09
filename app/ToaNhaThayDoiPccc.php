<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToaNhaThayDoiPccc extends Model
{
    protected $guarded = [];
    public function files()
    {
        return $this->hasMany('App\File', 'reference_id')->where('type', 'thay_doi_pccc');
    }
}
