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
          if(Auth::User()->id == $data->id_user_pinjam){
            $response = [
                'msg' => 'Gagal memperbarui data ini!'
            ];
            return response()->json($response,201);
        }
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
        if(Auth::User()->id == $request->input('id_user_pinjam')){
            $response = [
                'msg' => 'Data peminjaman ditolak! tidak bisa meminjam dengan akun yang sama!'
            ];
            return response()->json($response,201);
        }
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
                    'kas_masuk' => 0,
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
            if(Auth::User()->id == $request->input('id_user_pinjam')){
            $response = [
                'msg' => 'Data peminjaman ditolak! tidak bisa meminjam dengan akun yang sama!'
            ];
            return response()->json($response,201);
        }
	    	$koperasi->id_user_pinjam = $request->input('id_user_pinjam');
	    	$userPinjam = User::findOrFail($request->input('id_user_pinjam'));
	    	$uangKas->name = 'Peminjaman uang kas dari ' .$userPinjam->name;
    	}
    	if($koperasi->jumlah != $request->input('jumlah')){
    		$koperasi->jumlah = $request->input('jumlah');
            $uangKas->kas_keluar = $request->input('jumlah');

                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$uangKas->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo - $request->input('jumlah');

            $uangKas->saldo = $saldoNow;
            $uangKas->save();
                $updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$uangKas->id)->get();
            foreach ($updateAnotherSaldo as $key) {
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;                
                $key->saldo = $saldoNow;
                $key->save();
            }    		
    	}   	

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
        if($koperasi->id_user_pinjam == Auth::User()->id){
          
            $response = [
                'msg' => 'Gagal hapus karena id peminjam sama dengan akun yang login!'
            ];
            return response()->json($response,201);
        
        }
            $uangKas = uangKas::where('id_koperasi','=',$koperasi->id)->first();
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
    	if($koperasi->delete()){
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

    public function bayar($id)
    {
    	$koperasi = koperasi::findOrFail($id);
        if($koperasi->id_user_pinjam == Auth::User()->id){
          
            $response = [
                'msg' => 'Gagal membayar karena id peminjam sama dengan akun yang login!'
            ];
            return response()->json($response,201);
        
        }
    	$koperasi->status = 'Sudah Di Bayar';
    	if($koperasi->save()){
    		$uangKas = uangKas::where('id_koperasi','=',$koperasi->id)->first();
            $uangKas->kas_masuk = $koperasi->jumlah;
            $uangKas->kas_keluar = 0;

                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$uangKas->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo + $koperasi->jumlah;

            $uangKas->saldo = $saldoNow;
            $uangKas->save();

            $updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$uangKas->id)->get();
            foreach ($updateAnotherSaldo as $key) {
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;                
                $key->saldo = $saldoNow;
                $key->save();
            }           

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
