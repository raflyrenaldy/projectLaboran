<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\User;
use App\Component\ruangan;
use App\Component\masalahLab;
use Auth;

class masalahLabController extends Controller
{
    public function index()
    {
    	$data = masalahLab::all();

    	$response = [
                'msg' => 'Data Masalah Lab',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = masalahLab::findOrFail($id);
    	$response = [
                'msg' => 'Data Masalah Lab ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'id_thnajaran' => 'required',
           'name' => 'required',
           'ruangan' => 'required'
       ]);
    	$ruangan = explode(",",$request->input('ruangan'));
    	foreach ($ruangan as $key) {
    		if($key == 'All'){
    			$ruangan = 'Semua Lab';
    		}else{
    			$rgn = ruangan::findOrFail($key);
    			$ruangan[] = $rgn->name;
    		}
    	}

    	$masalahLab = new masalahLab ([
    		'id_user' => Auth::User()->id,
    		'id_thnajaran' => $request->input('id_thnajaran'),
    		'name' => $request->input('name'),
    		'keterangan' => $request->input('keterangan'),
    		'ruangan' => $ruangan,
    		'status' => 'New',
    		'slug' => str_replace(" ", "-", strtolower($request->input('name')))
    	]);


    	if($masalahLab->save()){
    		$response = [
                'msg' => 'masalahLab ' .$masalahLab->name .' berhasil di buat!',
                'data' => $masalahLab
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah data masalah lab!'
            ];
            return response()->json($response,201);
    	}

    }

    public function start($id)
    {

    	$masalahLab = masalahLab::findOrFail($id);
    	$masalahLab->id_user = Auth::User()->id;		
    	$masalahLab->waktu_mulai = now();
    	$masalahLab->status = 'Proses';
    	$masalahLab->save();
    	$response = [
                'msg' => 'masalahLab berhasil dimulai!',
                'data' => $masalahLab
            ];
            return response()->json($response,201);
    }

    public function finish(request $request,$id)
    {
    	$this->validate($request,[
           'waktu_selesai' => 'required',
           'yang_bertugas' => 'required',
           'solusi_solved' => 'required'
       ]);

    	$masalahLab = masalahLab::findOrFail($id);
    	if($request->input('keterangan') != null){    
    	$masalahLab->id_user = Auth::User()->id;		
    	$masalahLab->keterangan = $request->input('keterangan');
    	}
    	$masalahLab->waktu_selesai = now();
    	$masalahLab->status = 'Selesai';
    	$masalahLab->solusi_solved = $request->input('solusi_solved');
    	$yang_bertugas = explode(",",$request->input('yang_bertugas'));
    	foreach ($yang_bertugas as $key) {
    		if($key == 'All'){
    			$yang_bertugas = 'Team Laboran';
    		}else{
    			$user = User::findOrFail($key);
    			$yang_bertugas[] = $user->name;
    		}
    	}
    	$masalahLab->yang_bertugas = $yang_bertugas;
    	if($masalahLab->save()){
    		$response = [
                'msg' => 'masalahLab ' .$masalahLab->name .' berhasil selesai!',
                'data' => $masalahLab
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui data masalah lab!'
            ];
            return response()->json($response,201);
    	}
    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'id_thnajaran' => 'required',
           'name' => 'required',
           'ruangan' => 'required'
       ]);

    	$masalahLab = masalahLab::findOrFail($id);
    	$ruangan = explode(",",$request->input('ruangan'));
    	foreach ($ruangan as $key) {
    		if($key == 'All'){
    			$ruangan = 'Semua Lab';
    		}else{
    			$rgn = ruangan::findOrFail($key);
    			$ruangan[] = $rgn->name;
    		}
    	}

    	if($masalahLab->status == 'New'){
    		$masalahLab->id_user = Auth::User()->id;	
    		$masalahLab->id_thnajaran = $request->input('id_thnajaran');	
    		$masalahLab->name = $request->input('name');
    		$masalahLab->ruangan = $ruangan;
    		$masalahLab->keterangan = $request->input('keterangan');
    		$masalahLab->slug = str_replace(" ", "-", strtolower($request->input('name')));
    	}else if($masalahLab->status == 'Proses'){
    		$this->validate($request,[
           'waktu_mulai' => 'required'
       ]);
    		$masalahLab->id_user = Auth::User()->id;	
    		$masalahLab->id_thnajaran = $request->input('id_thnajaran');	
    		$masalahLab->name = $request->input('name');
    		$masalahLab->waktu_mulai = $request->input('waktu_mulai');
    		$masalahLab->ruangan = $ruangan;
    		$masalahLab->keterangan = $request->input('keterangan');
    		$masalahLab->slug = str_replace(" ", "-", strtolower($request->input('name')));
    	}else{
    		$this->validate($request,[
    	   'waktu_mulai' => 'required',
           'waktu_selesai' => 'required',
           'yang_bertugas' => 'required',
           'solusi_solved' => 'required'
       ]);
    		$yang_bertugas = explode(",",$request->input('yang_bertugas'));
    	foreach ($yang_bertugas as $key) {
    		if($key == 'All'){
    			$yang_bertugas = 'Team Laboran';
    		}else{
    			$user = User::findOrFail($key);
    			$yang_bertugas[] = $user->name;
    		}
    	}
    		$masalahLab->id_user = Auth::User()->id;	
    		$masalahLab->id_thnajaran = $request->input('id_thnajaran');	
    		$masalahLab->name = $request->input('name');
    		$masalahLab->waktu_mulai = $request->input('waktu_mulai');
    		$masalahLab->waktu_selesai = $request->input('waktu_selesai');
    		$masalahLab->yang_bertugas = $yang_bertugas;
    		$masalahLab->solusi_solved = $request->input('solusi_solved');
    		$masalahLab->ruangan = $ruangan;
    		$masalahLab->keterangan = $request->input('keterangan');
    		$masalahLab->slug = str_replace(" ", "-", strtolower($request->input('name')));
    	}

    	if($masalahLab->save()){
    		$response = [
                'msg' => 'masalahLab ' .$masalahLab->name .' berhasil diperbarui!',
                'data' => $masalahLab
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
    	$masalahLab = masalahLab::findOrFail($id);
    	if($masalahLab->delete()){
    		$response = [
                'msg' => 'masalahLab ' .$masalahLab->name .' berhasil dihapus!',
                'data' => $masalahLab
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
    	$data = masalahLab::where('ruangan','like','%' . $kelas .'%')->get();

    	$response = [
                'msg' => 'Data Masalah Lab' .$kelas,
                'data' => $data
            ];

        return response()->json($response,201);
    }

}
