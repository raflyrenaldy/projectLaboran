<?php

namespace App\Component;

use Illuminate\Database\Eloquent\Model;

class peminjamanInventory extends Model
{
    protected $fillable = [
    	'id_user','id_user_pinjam','id_inventory','keterangan','jumlah','status',
    ];

    public function get_user()
    {
        return $this->belongsTo('App\User', 'id_user', 'id');
    }

    public function get_user_pinjam()
    {
        return $this->belongsTo('App\User', 'id_user_pinjam', 'id');
    }

    public function get_inventory()
    {
        return $this->belongsTo('App\Component\inventory', 'id_inventory', 'id');
    }
}
