<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Component\koperasi;
use App\Component\uangKas;
use App\User;

class koperasiController extends Controller
{
    public function index()
    {
    	$data = koperasi::all();

    	$response = [
                'msg' => 'Data Koperasi',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = koperasi::findOrFail($id);
    	$response = [
                'msg' => 'Data Koperasi ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'id_user_pinjam' => 'required',
           'jumlah' => 'required'
       ]);

    		$koperasi = new koperasi ([
    		'id_user' => Auth::User()->id,
    		'id_user_pinjam' => $request->input('id_user_pinjam'),
    		'keterangan' => $request->input('keterangan'),
    		'jumlah' => $request->input('jumlah'),
    		'status' => 'New'
    	]);
    		if($koperasi->save()){
    			$userPinjam = User::findOrFail($request->input('id_user_pinjam'));
    			$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
	    		$lastSaldo = $getLastSaldo->saldo;
	    		$saldoNow = $lastSaldo - $koperasi->jumlah;
	    		$kaskeluar = new uangKas ([
	    			'id_user' => Auth::User()->id,
	    			'id_koperasi' => $koperasi->id,
	    			'name' => 'Peminjaman uang kas dari ' .$userPinjam->name,
	    			'kas_keluar' => $koperasi->jumlah,
	    			'saldo' => $saldoNow
	    		]);
	    		$kaskeluar->save();
    		$response = [
                'msg' => 'Data peminjaman berhasil di buat!',
                'data' => $koperasi
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah peminjaman!'
            ];
            return response()->json($response,201);
    	}    	
    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'id_user_pinjam' => 'required',
           'jumlah' => 'required'
       ]);

    		$koperasi = koperasi::findOrFail($id);
    		$uangKas = uangKas::where('id_koperasi','=',$koperasi->id)->first();

    	$koperasi->id_user = Auth::User()->id;

    	if($koperasi->id_user != $request->input('id_user_pinjam')){
	    	$koperasi->id_user_pinjam = $request->input('id_user_pinjam');
	    	$userPinjam = User::findOrFail($request->input('id_user_pinjam'));
	    	$uangKas->name = 'Peminjaman uang kas dari ' .$userPinjam->name;
    	}
    	if($koperasi->jumlah != $request->input('jumlah')){
    		$koperasi->jumlah = $request->input('jumlah');
    		$idUangKasNow = $uangKas->id;
    		$temp = uangKas::orderBy('id','asc')->first();
    		$allUangKas = uangKas::orderBy('id','asc')->get();
    		// foreach ($allUangKas as $key) {
    		// 	if($allUangKas->id != $idUangKasNow){
    		// 		if($allUangKas->id != $temp){
    		// 			$temp1 = $allUangKas->id;
    		// 		}
    		// 	}
    		// }
    		{{Sampai sini}}
    	}

    	$koperasi->name => $request->input('name');
    	$koperasi->harga => $request->input('harga');
    	$koperasi->jumlah => $request->input('jumlah');
    	
    	

    	if($koperasi->save()){
    		$response = [
                'msg' => 'Koperasi ' .$koperasi->name .' berhasil di perbarui!',
                'data' => $koperasi
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui Koperasi!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
    	$koperasi = koperasi::findOrFail($id);
    	if($koperasi->delete()){
    		$response = [
                'msg' => 'Koperasi ' .$koperasi->name .' berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus Koperasi!'
            ];
            return response()->json($response,201);
    	}
    }

    public function lunasin($id)
    {
    	$koperasi = koperasi::findOrFail($id);
    	$koperasi->status = 'Lunas';
    	if($koperasi->save()){
    		$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
    		$lastSaldo = $getLastSaldo->saldo;
    		$jumlahBeli = $koperasi->harga * $koperasi->jumlah;
    		$saldoNow = $lastSaldo - $jumlahBeli;
    		$kaskeluar = new uangKas ([
    			'id_user' => Auth::User()->id,
    			'id_pembelian' => $koperasi->id,
    			'name' => 'Pembelian ' .$koperasi->name,
    			'kas_keluar' => $jumlahBeli,
    			'saldo' => $saldoNow
    		]);
    		$kaskeluar->save();
    		$response = [
                'msg' => 'Koperasi ' .$koperasi->name .' berhasil di lunasi!'
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
