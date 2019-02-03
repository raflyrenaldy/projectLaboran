<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class ruangan extends Model
{
    protected $fillable = [
    	'name','jumlah_kursi','slug',
    ];
}
