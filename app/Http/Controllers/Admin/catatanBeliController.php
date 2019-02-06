<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Component\catatanBeli;
use App\Component\uangKas;

class catatanBeliController extends Controller
{
    public function index()
    {
    	$data = catatanBeli::all();

    	$response = [
                'msg' => 'Data catatan beli',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = catatanBeli::findOrFail($id);
    	$response = [
                'msg' => 'Data catatan beli ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'name' => 'required',
           'harga' => 'required',
           'jumlah' => 'required'
       ]);
    	if($request->input('status') == null){
    		$catatanBeli = new catatanBeli ([
    		'id_user' => Auth::User()->id,
    		'name' => $request->input('name'),
    		'harga' => $request->input('harga'),
    		'jumlah' => $request->input('jumlah'),
    		'status' => 'New'
    	]);
    		if($catatanBeli->save()){
    		$response = [
                'msg' => 'catatan Beli ' .$catatanBeli->name .' berhasil di buat!',
                'data' => $catatanBeli
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah catatan Beli!'
            ];
            return response()->json($response,201);
    	}
    	}else{
    		$catatanBeli = new catatanBeli ([
    		'id_user' => Auth::User()->id,
    		'name' => $request->input('name'),
    		'harga' => $request->input('harga'),
    		'jumlah' => $request->input('jumlah'),
    		'status' => $request->input('status')
    	]);
    		if($catatanBeli->save()){
    			$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
	    		$lastSaldo = $getLastSaldo->saldo;
	    		$jumlahBeli = $catatanBeli->harga * $catatanBeli->jumlah;
	    		$saldoNow = $lastSaldo - $jumlahBeli;
	    		$kaskeluar = new uangKas ([
	    			'id_user' => Auth::User()->id,
	    			'id_pembelian' => $catatanBeli->id,
	    			'name' => 'Pembelian ' .$catatanBeli->name,
                    'kas_masuk' => 0,
	    			'kas_keluar' => $jumlahBeli,
	    			'saldo' => $saldoNow
	    		]);
	    		$kaskeluar->save();
    		$response = [
                'msg' => 'catatan Beli ' .$catatanBeli->name .' berhasil di buat!',
                'data' => $catatanBeli
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah catatan Beli!'
            ];
            return response()->json($response,201);
    	}

    	}

    	

    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'name' => 'required',
           'harga' => 'required',
           'jumlah' => 'required'
       ]);

    	
    		$catatanBeli = catatanBeli::findOrFail($id);
    	$catatanBeli->id_user = Auth::User()->id;
    	$catatanBeli->name = $request->input('name');
    	$catatanBeli->harga = $request->input('harga');
    	$catatanBeli->jumlah = $request->input('jumlah');
    	
    	

    	if($catatanBeli->save()){
    		$response = [
                'msg' => 'catatan Beli ' .$catatanBeli->name .' berhasil di perbarui!',
                'data' => $catatanBeli
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui catatan beli!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
    	$catatanBeli = catatanBeli::findOrFail($id);

        if($catatanBeli->status == 'Lunas'){
            $uangKas = uangKas::where('id_pembelian','=',$catatanBeli->id)->first();
            $idUangKas = $uangKas->id;
            $uangKas->delete();
                $updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$idUangKas)->get();
            foreach ($updateAnotherSaldo as $key) {
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;
                $key->saldo = $saldoNow;
                $key->save();
            }   
        }
    	if($catatanBeli->delete()){
    		$response = [
                'msg' => 'Catatan Beli ' .$catatanBeli->name .' berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus catatan beli!'
            ];
            return response()->json($response,201);
    	}
    }

    public function lunasin($id)
    {
    	$catatanBeli = catatanBeli::findOrFail($id);
    	$catatanBeli->status = 'Lunas';
    	if($catatanBeli->save()){
    		$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
    		$lastSaldo = $getLastSaldo->saldo;
    		$jumlahBeli = $catatanBeli->harga * $catatanBeli->jumlah;
    		$saldoNow = $lastSaldo - $jumlahBeli;
    		$kaskeluar = new uangKas ([
    			'id_user' => Auth::User()->id,
    			'id_pembelian' => $catatanBeli->id,
    			'name' => 'Pembelian ' .$catatanBeli->name,
                'kas_masuk' => 0,
    			'kas_keluar' => $jumlahBeli,
    			'saldo' => $saldoNow
    		]);
    		$kaskeluar->save();
    		$response = [
                'msg' => 'Catatan Beli ' .$catatanBeli->name .' berhasil di lunasi!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal melunasi pembelian!'
            ];
            return response()->json($response,201);
    	}
    	
    }
}
