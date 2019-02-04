<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Component\tahunAjaran;

class tahunAjaranController extends Controller
{
    public function index()
    {
    	$data = tahunAjaran::all();

    	$response = [
                'msg' => 'Data Tahun Ajaran',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($slug)
    {
    	$data = tahunAjaran::where('slug','=',$slug)->first();
    	$response = [
                'msg' => 'Data Tahun Ajaran ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'name'        => 'required',
           'waktu_mulai' => 'required',
           'waktu_berakhir' => 'required'
       ]);

    	$thnAjaran = new tahunAjaran ([
    		'name' => $request->input('name'),
    		'waktu_mulai' => $request->input('waktu_mulai'),
    		'waktu_berakhir' => $request->input('waktu_berakhir'),
    		'slug' => str_replace(" ", "-", strtolower($request->input('name')))
    	]);


    	if($thnAjaran->save()){
    		$response = [
                'msg' => 'Tahun Ajaran ' .$thnAjaran->name .' berhasil di buat!',
                'data' => $thnAjaran
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah tahun ajaran!'
            ];
            return response()->json($response,201);
    	}

    }

    public function update(request $request,$slug)
    {
    	$this->validate($request,[
           'name'        => 'required',
           'waktu_mulai' => 'required',
           'waktu_berakhir' => 'required'
       ]);

    	$thnAJaran = tahunAjaran::where('slug','=',$slug)->first();
    	$thnAjaran->name => $request->input('name');
    	$thnAjaran->waktu_mulai => $request->input('waktu_mulai');
    	$thnAjaran->waktu_berakhir => $request->input('waktu_berakhir');
    	$thnAjaran->slug => str_replace(" ", "-", strtolower($request->input('name')));

    	if($thnAjaran->save()){
    		$response = [
                'msg' => 'Tahun Ajaran ' .$thnAjaran->name .' berhasil di perbarui!',
                'data' => $thnAjaran
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui tahun ajaran!'
            ];
            return response()->json($response,201);
    	}
    }

    public function destroy($id)
    {
    	$thnAjaran = tahunAjaran::findOrFail($id);
    	if($thnAjaran->delete()){
    		$response = [
                'msg' => 'Tahun Ajaran ' .$thnAjaran->name .' berhasil di hapus!'
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus tahun ajaran!'
            ];
            return response()->json($response,201);
    	}
    }
}
