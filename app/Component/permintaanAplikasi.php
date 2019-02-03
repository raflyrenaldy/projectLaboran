<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class permintaanAplikasi extends Model
{
    protected $fillable = [
    	'id_user','id_ruangan','id_thnajaran','name','name_dosen','status','deadline',
    ];

    protected $dates = ['deadline'];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_ruangan()
    {
        return $this->belongsTo('App\Component\ruangan', 'id_ruangan', 'id');
    }

    public function get_thnajaran()
    {
        return $this->belongsTo('App\Component\tahunAjaran', 'id_thnajaran', 'id');
    }
}
