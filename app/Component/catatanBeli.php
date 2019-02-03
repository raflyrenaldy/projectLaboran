<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class catatanBeli extends Model
{
    protected $fillable = [
    	'id_user','name','harga','jumlah','status',
    ];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

}
