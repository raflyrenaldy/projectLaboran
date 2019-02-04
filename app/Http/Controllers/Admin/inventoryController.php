<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Component\inventory;
use Auth;
class inventoryController extends Controller
{
    public function index()
    {
    	$data = inventory::all();

    	$response = [
                'msg' => 'Data Inventory',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = inventory::findOrFail($id);
    	$response = [
                'msg' => 'Data Inventory ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'name' => 'required',
           'jumlah' => 'required'
       ]);

    	$inventory = new inventory ([
    		'id_user' => Auth::User()->id,
    		'name' => $request->input('name'),
    		'jumlah' => $request->input('jumlah'),
    		'keterangan' => $request->input('keterangan')
    	]);


    	if($inventory->save()){
    		$response = [
                'msg' => 'Inventory ' .$inventory->name .' berhasil di buat!',
                'data' => $inventory
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah Inventory!'
            ];
            return response()->json($response,201);
    	}

    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'name'        => 'required',
           'jumlah' => 'required'
       ]);

    	$inventory = inventory::findOrFail($id);
    	$inventory->id_user = Auth::User()->id;
    	$inventory->name => $request->input('name');
    	$inventory->jumlah => $request->input('jumlah');
    	$inventory->keterangan => $request->input('keterangan');

    	if($inventory->save()){
    		$response = [
                'msg' => 'Inventory ' .$inventory->name .' berhasil di perbarui!',
                'data' => $inventory
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui Inventory!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
    	$inventory = inventory::findOrFail($id);
    	if($inventory->delete()){
    		$response = [
                'msg' => 'Inventory ' .$inventory->name .' berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus Inventory!'
            ];
            return response()->json($response,201);
    	}
    }
}
