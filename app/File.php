<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable =[
        'name',
        'file_id',
        'nguoi_tao',
        'type',
        'reference_id',
        'id',
    ];
}
