<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Component\catatanBeli;
use App\Component\uangKas;

class catatanBeliController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return catatanBeli::with('get_user')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $catatanBeli = catatanBeli::with('get_user')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($catatanBeli->isEmpty()){
                $catatanBeli = catatanBeli::with('get_user')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('harga','LIKE',"%$search%")
                    ->orWhere('jumlah','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $catatanBeli = catatanBeli::with('get_user')->latest()->paginate(5);
        }
        
        return $catatanBeli;
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
            'harga' => 'required',
            'jumlah' => 'required'
        ]);
        if($request->input('status') == null){
    		$catatanBeli = new catatanBeli ([
    		'id_user' => Auth('api')->User()->id,
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
    		'id_user' => Auth('api')->User()->id,
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
	    			'id_user' => Auth('api')->User()->id,
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
            'name' => 'required',
            'harga' => 'required',
            'jumlah' => 'required'
        ]);

        $catatanBeli = catatanBeli::with('get_user')->findOrFail($id);
    	$catatanBeli->id_user = Auth('api')->User()->id;
        $catatanBeli->name = $request->input('name');
        
        
        if($catatanBeli->status == 'Lunas'){
                $uangKas = uangKas::where('id_pembelian','=',$catatanBeli->id)->first();
                $uangKas->name = 'Pembelian ' .$request->input('name');
            if($catatanBeli->harga != $request->harga || $catatanBeli->jumlah != $request->jumlah ){
                $catatanBeli->harga = $request->input('harga');
                $catatanBeli->jumlah = $request->input('jumlah');   
                $jumlah = $request->input('harga') * $request->input('jumlah'); 
                $uangKas->kas_keluar = $jumlah;
                $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$uangKas->id)->first();
                $lastSaldo = $getLastSaldo->saldo;
                $saldoNow = $lastSaldo - $jumlah;
 
            $uangKas->saldo = $saldoNow;
            
                $updateAnotherSaldo = uangKas::orderBy('id','asc')->where('id','>',$uangKas->id)->get();
                foreach ($updateAnotherSaldo as $key) {
                    $getLastSaldo = uangKas::orderBy('id', 'desc')->where('id','<',$key->id)->first();
                    $lastSaldo = $getLastSaldo->saldo;
                    $saldoNow = $lastSaldo + $key->kas_masuk - $key->kas_keluar;                
                    $key->saldo = $saldoNow;
                    $key->save();
                }   
            }             	 
            $uangKas->save();          
        }else{
            $catatanBeli->harga = $request->input('harga');
            $catatanBeli->jumlah = $request->input('jumlah');  
        }

    	  	

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $catatanBeli = catatanBeli::with('get_user')->findOrFail($id);

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
    	$catatanBeli = catatanBeli::with('get_user')->findOrFail($id);
    	$catatanBeli->status = 'Lunas';
    	if($catatanBeli->save()){
    		$getLastSaldo = uangKas::orderBy('id', 'desc')->first();
    		$lastSaldo = $getLastSaldo->saldo;
    		$jumlahBeli = $catatanBeli->harga * $catatanBeli->jumlah;
    		$saldoNow = $lastSaldo - $jumlahBeli;
    		$kaskeluar = new uangKas ([
    			'id_user' => Auth('api')->User()->id,
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
