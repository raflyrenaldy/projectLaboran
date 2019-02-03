<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class tahunAjaran extends Model
{
    protected $fillable = [
    	'name','waktu_mulai','waktu_berakhir','slug',
    ];

    protected $dates = ['waktu_mulai','waktu_berakhir'];
}
