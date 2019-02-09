<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Component\peminjamanInventory;
use App\Component\inventory;
class peminjamanInventoryController extends Controller
{
    public function index()
    {
    	$data = peminjamanInventory::all();
    	$inventory = inventory::all();
    	$response = [
                'msg' => 'Data Peminjaman Inventory',
                'data inventory' => $inventory,
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = peminjamanInventory::findOrFail($id);
    	if(Auth::User()->id == $data->id_user_pinjam){
            $response = [
                'msg' => 'Gagal memperbarui data ini!'
            ];
            return response()->json($response,201);
        }
    	$inventory = inventory::all();
    	$response = [
                'msg' => 'Data Peminjaman Inventory ' .$data->name,
                'data inventory' => $inventory,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'id_user_pinjam' => 'required',
           'id_inventory' => 'required',
           'keterangan' => 'required',
           'jumlah' => 'required',
           'status' => 'required'
       ]);
    	if($request->input('id_user_pinjam') == Auth::User()->id){
          
            $response = [
                'msg' => 'Gagal meminjam inventory!'
            ];
            return response()->json($response,201);
        
        }

    	$peminjamanInventory = new peminjamanInventory ([
    		'id_user' => Auth::User()->id,
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
    		$inventory->keterangan = 'Dipinjam oleh ' .$user->name .' dengan jumlah ' .$peminjamanInventory->jumlah;
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
    	if($peminjamanInventory->id_user_pinjam == Auth::User()->id){
          
            $response = [
                'msg' => 'Gagal mengembalikan inventory!'
            ];
            return response()->json($response,201);        
        }
        $inventory = inventory::findOrFail($peminjamanInventory->id_inventory);
        $inventory->jumlah = $inventory->jumlah + $peminjamanInventory->jumlah;
        $inventory->keterangan = '';
        $inventory->save();

    	$peminjamanInventory->id_user = Auth::User()->id;		
    	$peminjamanInventory->status = 'Sudah Dikembalikan';
    	$peminjamanInventory->save();
    	$response = [
                'msg' => 'peminjamanInventory berhasil dimulai!',
                'data' => $peminjamanInventory
            ];
            return response()->json($response,201);
    }      

    public function update(request $request,$id)
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
            if(Auth::User()->id == $request->input('id_user_pinjam')){
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

    public function destroy($id)
    {
    	$peminjamanInventory = peminjamanInventory::findOrFail($id);
        $inventory = inventory::findOrFail($peminjamanInventory->id_inventory);
        $user = User::findOrFail($peminjamanInventory->id_user_pinjam);
        if($peminjamanInventory->status != 'Sudah Dikembalikan'){
            $inventory->jumlah = $inventory->jumlah + $peminjamanInventory->jumlah;
            $inventory->keterangan = 'Pernah dipinjam ' .$user->name .' dan datanya dihapus oleh ' .Auth::User()->name;
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

}
