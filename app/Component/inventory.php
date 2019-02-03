<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    protected $fillable = [
    	'id_user','name','jumlah','keterangan',
    ];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }
}
