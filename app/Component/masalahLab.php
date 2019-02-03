<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class masalahLab extends Model
{
    protected $fillable = [
    	'id_user','id_thnajaran','name','keterangan','waktu_mulai','waktu_selesai','status','yang_bertugas','ruangan','slug',
    ];

    protected $dates = ['waktu_mulai','waktu_selesai'];
    
    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_thnajaran()
    {
        return $this->belongsTo('App\Component\tahunAjaran', 'id_thnajaran', 'id');
    }
}
