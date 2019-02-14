<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Component\peminjamanInventory;
use App\Component\inventory;

class peminjamanInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $peminjamanInventory = peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($peminjamanInventory->isEmpty()){
                $peminjamanInventory = peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')->whereHas('get_user_pinjam', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if ($peminjamanInventory->isEmpty()){
                $peminjamanInventory = peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')->whereHas('get_inventory', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                  })->paginate(20);
              }
              if($peminjamanInventory->isEmpty()){
                $peminjamanInventory = peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')->where(function($query) use ($search){
                    $query->where('keterangan','LIKE',"%$search%")
                    ->orWhere('jumlah','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $peminjamanInventory = peminjamanInventory::with('get_user','get_user_pinjam','get_inventory')->latest()->paginate(5);
        }
        
        return $peminjamanInventory;
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
            'id_inventory' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required',
            'status' => 'required'
        ]);
         if($request->input('id_user_pinjam') == Auth('api')->User()->id){
           
             $response = [
                 'msg' => 'Gagal meminjam inventory!'
             ];
             return response()->json($response,201);
         
         }
 
         $peminjamanInventory = new peminjamanInventory ([
             'id_user' => Auth('api')->User()->id,
             'id_user_pinjam' => $request->input('id_user_pinjam'),
             'id_inventory' => $request->input('id_inventory'),
             'keterangan' => $request->input('keterangan'),
             'jumlah' => $request->input('jumlah'),
             'status' => $request->input('status')
         ]);
 
 
         if($peminjamanInventory->save()){
             $inventory = inventory::findOrFail($request->input('id_inventory'));
             $user = User::findOrFail($request->input('id_user_pinjam'));
             $inventory->jumlah = $inventory->jumlah - $request->input('jumlah');
             if($peminjamanInventory->status == 'Diminta'){                 
             $inventory->keterangan = 'Diminta oleh ' .$user->name .' dengan jumlah ' .$peminjamanInventory->jumlah;
            }else{
             $inventory->keterangan = 'Dipinjam oleh ' .$user->name .' dengan jumlah ' .$peminjamanInventory->jumlah;
            }
             $inventory->save();
             $response = [
                 'msg' => 'peminjamanInventory berhasil di buat!',
                 'data' => $peminjamanInventory
             ];
             return response()->json($response,201);
         }else{
             $response = [
                 'msg' => 'Gagal tambah data masalah lab!'
             ];
             return response()->json($response,201);
         }
    }

     public function dipulangkeun($id)
    {
    	$peminjamanInventory = peminjamanInventory::findOrFail($id);
    	if($peminjamanInventory->id_user_pinjam == Auth('api')->User()->id){
          
            $response = [
                'msg' => 'Gagal mengembalikan inventory!'
            ];
            return response()->json($response,201);        
        }
        $inventory = inventory::findOrFail($peminjamanInventory->id_inventory);
        $inventory->jumlah = $inventory->jumlah + $peminjamanInventory->jumlah;
        $inventory->keterangan = '';
        $inventory->save();

    	$peminjamanInventory->id_user = Auth('api')->User()->id;		
    	$peminjamanInventory->status = 'Sudah Dikembalikan';
    	$peminjamanInventory->save();
    	$response = [
                'msg' => 'peminjamanInventory berhasil dimulai!',
                'data' => $peminjamanInventory
            ];
            return response()->json($response,201);
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
            'id_inventory' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required'
        ]);
 
         $peminjamanInventory = peminjamanInventory::findOrFail($id);
         $inventory = inventory::findOrFail($peminjamanInventory->id_inventory);
         $user = User::findOrFail($peminjamanInventory->id_user_pinjam);
         if($peminjamanInventory->id_user_pinjam != $request->input('id_user_pinjam'))
         {
             if(Auth('api')->User()->id == $request->input('id_user_pinjam')){
                 $response = [
                     'msg' => 'Gagal memperbarui data ini!'
                 ];
             return response()->json($response,201);
             }else{
             $peminjamanInventory->id_user_pinjam = $request->input('id_user_pinjam');
             $user = User::findOrFail($request->input('id_user_pinjam'));
             }
         }
 
         if($peminjamanInventory->id_inventory != $request->input('id_inventory')){
             $peminjamanInventory->id_inventory = $request->input('id_inventory');
 
             if($peminjamanInventory->status != 'Sudah Dikembalikan'){
                 $inventory->jumlah = $inventory->jumlah + $peminjamanInventory->jumlah;
                 $inventory->keterangan = '';
             }
             $newInventory = inventory::findOrFail($request->input('id_inventory'));
             $newInventory->jumlah = $newInventory->jumlah - $request->input('jumlah');
             if($peminjamanInventory->status != 'Sudah Dikembalikan'){
                  $newInventory->keterangan = 'Dipinjam oleh ' .$user->name .' dengan jumlah ' .$request->input('jumlah');
             }
            
             $newInventory->save();
         }else{
              if($peminjamanInventory->status != 'Sudah Dikembalikan'){
                  $inventory->keterangan = 'Dipinjam oleh ' .$user->name .' dengan jumlah ' .$request->input('jumlah');
             }
         }
 
         $peminjamanInventory->jumlah = $request->input('jumlah');
         $peminjamanInventory->keterangan = $request->input('keterangan');
         $inventory->save();
 
         if($peminjamanInventory->save()){
             $response = [
                 'msg' => 'peminjamanInventory berhasil diperbarui!',
                 'data' => $peminjamanInventory
             ];
             return response()->json($response,201);
         }else{
             $response = [
                 'msg' => 'Gagal perbarui data masalah lab!'
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
        $peminjamanInventory = peminjamanInventory::findOrFail($id);
        $inventory = inventory::findOrFail($peminjamanInventory->id_inventory);
        $user = User::findOrFail($peminjamanInventory->id_user_pinjam);
        if($peminjamanInventory->status != 'Sudah Dikembalikan'){
            $inventory->jumlah = $inventory->jumlah + $peminjamanInventory->jumlah;
            $inventory->keterangan = 'Pernah dipinjam ' .$user->name .' dan datanya dihapus oleh ' .Auth('api')->User()->name;
            $inventory->save();
        }
    	if($peminjamanInventory->delete()){
    		$response = [
                'msg' => 'peminjamanInventory  berhasil dihapus!',
                'data' => $peminjamanInventory
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus data masalah lab!'
            ];
            return response()->json($response,201);
    	}
    }

    public function allInventory()
    {
        return inventory::orderBy('name','asc')->get();
    }
}
