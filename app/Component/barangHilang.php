<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class barangHilang extends Model
{
    protected $fillable = [
    	'id_user','id_ruangan','name','keterangan','waktu_penemuan','waktu_diambil','status','npm','phone','foto','name_mhs',
    ];

    protected $dates = ['waktu_penemuan','waktu_diambil'];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_ruangan()
    {
        return $this->belongsTo('App\Component\ruangan', 'id_ruangan', 'id');
    }
}
