<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class uangKas extends Model
{
    protected $fillable = [
    	'id_user','id_koperasi','id_pembelian','name','kas_masuk','kas_keluar','saldo',
    ];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_koperasi()
    {
        return $this->belongsTo('App\Component\koperasi', 'id_koperasi', 'id');
    }

    public function get_pembelian()
    {
        return $this->belongsTo('App\Component\catatanBeli', 'id_pembelian', 'id');
    }

}
