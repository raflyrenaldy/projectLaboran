<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App\Component\permintaanAplikasi;
use App\Component\tahunAjaran;

class permintaanAplikasiController extends Controller
{
    public function index()
    {
        $thnAjaran = tahunAjaran::where('waktu_mulai','<', date('Y-m-d'))
                                ->where('waktu_berakhir','>', date('Y-m-d'))
                                ->first();
    	$data = permintaanAplikasi::where('id_thnajaran','=',$thnAjaran->id)->get();;

    	$response = [
                'msg' => 'Data permintaan aplikasi',
                'thn Ajaran' => $thnAjaran,
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = permintaanAplikasi::findOrFail($id);
    	$response = [
                'msg' => 'Data permintaan aplikasi ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'id_thnajaran' => 'required',
           'id_ruangan' => 'required',
           'name' => 'required',
           'ruangan' => 'required',
           'name_dosen' => 'required',
           'deadline' => 'required'
       ]);
    	$ruangan = explode(",",$request->input('id_ruangan'));
    	foreach ($ruangan as $key) {
    		$permintaanAplikasi = new permintaanAplikasi ([
    		'id_user' => Auth::User()->id,
    		'id_ruangan' => $key,
    		'id_thnajaran' => $request->input('id_thnajaran'),
    		'name' => $request->input('name'),
    		'name_dosen' => $request->input('name_dosen'),
    		'status' => 'New',
    		'deadline' => $request->input('deadline')
    	]);
    		$permintaanAplikasi->save();
    	}

    		$response = [
                'msg' => 'Permintaan Aplikasi berhasil di buat!',
                'data' => $permintaanAplikasi
            ];
            return response()->json($response,201);
    	

    }

    public function finish($id)
    {

    	$permintaanAplikasi = permintaanAplikasi::findOrFail($id);
    	$permintaanAplikasi->id_user = Auth::User()->id;	
    	$permintaanAplikasi->status = 'Selesai';
    	$permintaanAplikasi->save();
    	$response = [
                'msg' => 'permintaanAplikasi berhasil diselesaikan!',
                'data' => $permintaanAplikasi
            ];
            return response()->json($response,201);
    }

   

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'id_thnajaran' => 'required',
           'name' => 'required',
           'ruangan' => 'required',
           'name_dosen' => 'required',
           'deadline' => 'required'
       ]);
    	$permintaanAplikasi = permintaanAplikasi::findOrFail($id);
    		
    		$permintaanAplikasi->id_user = Auth::User()->id;
    		$permintaanAplikasi->id_thnajaran = $request->input('id_thnajaran');
    		$permintaanAplikasi->name = $request->input('name');
    		$permintaanAplikasi->name_dosen = $request->input('name_dosen');
    		$permintaanAplikasi->deadline = $request->input('deadline');

    	if($permintaanAplikasi->save()){
    		$response = [
                'msg' => 'permintaanAplikasi ' .$permintaanAplikasi->name .' berhasil diperbarui!',
                'data' => $permintaanAplikasi
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
    	$permintaanAplikasi = permintaanAplikasi::findOrFail($id);
    	if($permintaanAplikasi->delete()){
    		$response = [
                'msg' => 'permintaanAplikasi ' .$permintaanAplikasi->name .' berhasil dihapus!',
                'data' => $permintaanAplikasi
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus data masalah lab!'
            ];
            return response()->json($response,201);
    	}
    }

    public function filter($kelas)
    {
    	$data = permintaanAplikasi::where('id_ruangan','=',$kelas)->get();

    	$response = [
                'msg' => 'Data permintaanAplikasi Lab ' .$kelas,
                'data' => $data
            ];

        return response()->json($response,201);
    }

}
