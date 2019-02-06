<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Component\uangKas;
use App\Component\catatanBeli;
use App\Component\koperasi;

class uangKasController extends Controller
{
     public function index()
    {
    	$data = uangKas::all();

    	$response = [
                'msg' => 'Data Uang Kas',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = uangKas::findOrFail($id);
    	$response = [
                'msg' => 'Data Uang Kas ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'name' => 'required',
       ]);
    	$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
	    $lastSaldo = $getLastSaldo->saldo;
    	if($request->input('kas_masuk') != null){
	    	$saldoNow = $lastSaldo + $request->input('kas_masuk');
	    	$uangKas = new uangKas ([
	    		'id_user' => Auth::User()->id,
	    		'name' => $request->input('name'),
	    		'kas_masuk' => $request->input('kas_masuk'),
	    		'kas_keluar' => 0,
	    		'saldo' => $saldoNow
	    	]);
    	}else{
	    	$saldoNow = $lastSaldo - $request->input('kas_keluar');    
	    	$uangKas = new uangKas ([
	    		'id_user' => Auth::User()->id,
	    		'name' => $request->input('name'),
	    		'kas_masuk' => 0,
	    		'kas_keluar' => $request->input('kas_keluar'),
	    		'saldo' => $saldoNow
	    	]);		
    	}   	
    	if($uangKas->save()){    			
    		$response = [
                'msg' => 'Data uang kas berhasil di buat!',
                'data' => $uangKas
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah data uang kas!'
            ];
            return response()->json($response,201);
    	}    	
    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'name' => 'required'
       ]);

    	$uangKas = uangKas::findOrFail($id);
    	$uangKas->id_user = Auth::User()->id; 
    	$uangKas->name = $request->input('name');
    	$getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$uangKas->id)->first();
        $lastSaldo = $getLastSaldo->saldo;
    	if($request->input('kas_masuk') != null){
	    	$saldoNow = $lastSaldo + $request->input('kas_masuk');
	    	$uangKas->kas_masuk = $request->input('kas_masuk');
	    	$uangKas->kas_keluar = 0;
	    	$uangKas->saldo = $saldoNow;
    	}else{
	    	$saldoNow = $lastSaldo - $request->input('kas_keluar');   
	    	$uangKas->kas_masuk = 0;
	    	$uangKas->kas_keluar = $request->input('kas_keluar'); 	    		
	    	$uangKas->saldo = $saldoNow;
    	}
           		    	   	

    	if($uangKas->save()){
    	$updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$uangKas->id)->get();
        foreach ($updateAnotherSaldo as $key) {
            $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
            $lastSaldo = $getLastSaldo->saldo;
            $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;              
       		$key->saldo = $saldoNow;
      		$key->save();
        } 
    		$response = [
                'msg' => 'uang Kas ' .$uangKas->name .' berhasil di perbarui!',
                'data' => $uangKas
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui data uang kas!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
            $uangKas = uangKas::findOrFail($id);
            $idUangKas = $uangKas->id;
               
    	if($uangKas->delete()){
    		 $updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$idUangKas)->get();
            foreach ($updateAnotherSaldo as $key) {
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;  
                $key->saldo = $saldoNow;
                $key->save();
            }   
    		$response = [
                'msg' => 'berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus Koperasi!'
            ];
            return response()->json($response,201);
    	}
    }
    
}
