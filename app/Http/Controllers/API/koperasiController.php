<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Component\koperasi;
use App\Component\uangKas;
use App\User;

class koperasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return koperasi::with('get_user','get_user_pinjam')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $koperasi = koperasi::with('get_user','get_user_pinjam')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($koperasi->isEmpty()){
                $koperasi = koperasi::with('get_user','get_user_pinjam')->whereHas('get_user_pinjam', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if($koperasi->isEmpty()){
                $koperasi = koperasi::with('get_user','get_user_pinjam')->where(function($query) use ($search){
                    $query->where('keterangan','LIKE',"%$search%")
                    ->orWhere('jumlah','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $koperasi = koperasi::with('get_user','get_user_pinjam')->latest()->paginate(5);
        }
        
        return $koperasi;
    } 
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'id_user_pinjam' => 'required',
            'jumlah' => 'required'
        ]);
         if(Auth('api')->User()->id == $request->input('id_user_pinjam')){
             $response = [
                 'msg' => 'Data peminjaman ditolak! tidak bisa meminjam dengan akun yang sama!'
             ];
             return response()->json($response,201);
         }
             $koperasi = new koperasi ([
             'id_user' => Auth('api')->User()->id,
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
                     'id_user' => Auth('api')->User()->id,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'id_user_pinjam' => 'required',
            'jumlah' => 'required'
        ]);
 
             $koperasi = koperasi::with('get_user','get_user_pinjam')->findOrFail($id);
             $uangKas = uangKas::where('id_koperasi','=',$koperasi->id)->first();
       
         $koperasi->id_user = Auth('api')->User()->id;
 
         if($koperasi->id_user != $request->input('id_user_pinjam')){
             if(Auth('api')->User()->id == $request->input('id_user_pinjam')){
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
             if($koperasi->status == 'Sudah Di Bayar'){
                $uangKas->kas_masuk = $request->input('jumlah');
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$uangKas->id)->first();
                 $lastSaldo = $getLastSaldo->saldo;
                 $saldoNow = $lastSaldo + $request->input('jumlah');
 
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
             }else{
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $koperasi = koperasi::with('get_user','get_user_pinjam')->findOrFail($id);
        if($koperasi->id_user_pinjam == Auth('api')->User()->id){
          
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
    	$koperasi = koperasi::with('get_user','get_user_pinjam')->findOrFail($id);
        if($koperasi->id_user_pinjam == Auth('api')->User()->id){
          
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
            $uangKas->name = $uangKas->name .' [Lunas]';
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
