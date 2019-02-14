<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Component\uangKas;
use App\Component\catatanBeli;
use App\Component\koperasi;

class uangKasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return uangKas::with('get_user','get_koperasi','get_pembelian')->latest()->paginate(5);
    }

    public function lastSaldo()
    {
        return uangKas::latest()->select('saldo')->first();
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $uangKas = uangKas::with('get_user','get_koperasi','get_pembelian')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($uangKas->isEmpty()){
                $uangKas = uangKas::with('get_user','get_koperasi','get_pembelian')->whereHas('get_koperasi', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if ($uangKas->isEmpty()){
                $uangKas = uangKas::with('get_user','get_koperasi','get_pembelian')->whereHas('get_pembelian', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                  })->paginate(20);
              }
              if($uangKas->isEmpty()){
                $uangKas = uangKas::with('get_user','get_koperasi','get_pembelian')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('kas_masuk','LIKE',"%$search%")
                    ->orWhere('kas_keluar','LIKE',"%$search%")
                    ->orWhere('saldo','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $uangKas = uangKas::with('get_user','get_koperasi','get_pembelian')->latest()->paginate(5);
        }
        
        return $uangKas;
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
            'name' => 'required',
        ]);
        $checkFirstKas = uangKas::all();
        if($checkFirstKas->isEmpty()){
            return uangKas::create([
                'id_user' => Auth('api')->User()->id,
                'name' => $request->input('name'),
                'kas_masuk' => $request->input('kas_masuk'),
                'kas_keluar' => 0,
                'saldo' => $request->input('kas_masuk')
            ]);
        }
         $getLastSaldo = uangKas::orderBy('id', 'desc')->first();

         $lastSaldo = $getLastSaldo->saldo;
         if($request->input('kas_masuk') != null){
             $saldoNow = $lastSaldo + $request->input('kas_masuk');
             $uangKas = new uangKas ([
                 'id_user' => Auth('api')->User()->id,
                 'name' => $request->input('name'),
                 'kas_masuk' => $request->input('kas_masuk'),
                 'kas_keluar' => 0,
                 'saldo' => $saldoNow
             ]);
         }else{
             $saldoNow = $lastSaldo - $request->input('kas_keluar');    
             $uangKas = new uangKas ([
                 'id_user' => Auth('api')->User()->id,
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
            'name' => 'required'
        ]); 
        
         $uangKas = uangKas::findOrFail($id);
         $uangKas->id_user = Auth('api')->User()->id; 
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $uangKas = uangKas::findOrFail($id);
            $idUangKas = $uangKas->id;
        
        if($uangKas->id_pembelian != null){
            $catatanBeli = catatanBeli::findOrFail($uangKas->id_pembelian);
            $catatanBeli->delete();
        }
        if($uangKas->id_koperasi != null){
            $koperasi = koperasi::findOrFail($uangKas->id_koperasi);
            $koperasi->delete();
        }
               
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
