<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Component\ruangan;

class ruanganController extends Controller
{
     public function index()
    {
    	$data = ruangan::all();

    	$response = [
                'msg' => 'Data Ruangan',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($slug)
    {
    	$data = ruangan::where('slug','=',$slug)->first();
    	$response = [
                'msg' => 'Data Ruangan ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'name'        => 'required',
           'jumlah_kursi' => 'required'
       ]);

    	$ruangan = new ruangan ([
    		'name' => $request->input('name'),
    		'jumlah_kursi' => $request->input('jumlah_kursi'),
    		'slug' => str_replace(" ", "-", strtolower($request->input('name')))
    	]);


    	if($ruangan->save()){
    		$response = [
                'msg' => 'Ruangan ' .$ruangan->name .' berhasil di buat!',
                'data' => $ruangan
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah Ruangan!'
            ];
            return response()->json($response,201);
    	}

    }

    public function update(request $request,$slug)
    {
    	$this->validate($request,[
           'name'        => 'required',
           'jumlah_kursi' => 'required'
       ]);

    	$ruangan = ruangan::where('slug','=',$slug)->first();
    	$ruangan->name => $request->input('name');
    	$ruangan->jumlah_kursi => $request->input('jumlah_kursi');
    	$ruangan->slug => str_replace(" ", "-", strtolower($request->input('name')));

    	if($ruangan->save()){
    		$response = [
                'msg' => 'Ruangan ' .$ruangan->name .' berhasil di perbarui!',
                'data' => $ruangan
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui Ruangan!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
    	$ruangan = ruangan::findOrFail($id);
    	if($ruangan->delete()){
    		$response = [
                'msg' => 'Ruangan ' .$ruangan->name .' berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus Ruangan!'
            ];
            return response()->json($response,201);
    	}
    }
}
