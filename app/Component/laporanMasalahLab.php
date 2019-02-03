<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class laporanMasalahLab extends Model
{
    protected $fillable = [
    	'id_ruangan','id_masalah',
    ];

    public function get_ruangan()
    {
        return $this->belongsTo('App\Component\ruangan', 'id_ruangan', 'id');
    }

    public function get_masalah()
    {
        return $this->belongsTo('App\Component\masalahLab', 'id_masalah', 'id');
    }
}
