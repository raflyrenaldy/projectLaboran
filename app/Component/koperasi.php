<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class koperasi extends Model
{
    protected $fillable = [
    	'id_user','id_user_pinjam','keterangan','jumlah','status',
    ];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_user_pinjam()
    {
        return $this->belongsTo('App\User', 'id_user_pinjam', 'id');
    }
}
