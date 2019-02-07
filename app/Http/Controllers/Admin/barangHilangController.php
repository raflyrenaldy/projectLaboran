<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Component\barangHilang;
use App\Component\ruangan;

class barangHilangController extends Controller
{
    public function index()
    {
    	$data = barangHilang::all();

    	$response = [
                'msg' => 'Data Barang Hilang',
                'data' => $data
            ];

        return response()->json($response,201);
    }

    public function edit($id)
    {
    	$data = barangHilang::findOrFail($id);
    	$response = [
                'msg' => 'Data Barang Hilang ' .$data->name,
                'data' => $data
            ];

        return response()->json($response,201);

    }

    public function store(request $request)
    {
    	$this->validate($request,[
           'id_ruangan' => 'required',
           'name' => 'required',
           'keterangan' => 'required',
           'waktu_penemuan' => 'required',
       ]);
    	

    	$barangHilang = new barangHilang ([
    		'id_user' => Auth::User()->id,
    		'id_ruangan' => $request->input('id_ruangan'),
    		'name' => $request->input('name'),
    		'keterangan' => $request->input('keterangan'),
    		'waktu_penemuan' => $request->input('waktu_penemuan'),
    		'status' => 'New'
    	]);


    	if($barangHilang->save()){
    		$response = [
                'msg' => 'barangHilang ' .$barangHilang->name .' berhasil di buat!',
                'data' => $barangHilang
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal tambah data Barang Hilang!'
            ];
            return response()->json($response,201);
    	}

    }

    public function ditemukan(request $request,$id)
    {
    	$this->validate($request,[
           'waktu_diambil' => 'required',
           'npm' => 'required',
           'name_mhs' => 'required',
           'phone' => 'required',
           'foto' =>  'required'
       ]);
    	if ($request->hasFile('foto')){

                $filename = $request->foto->getClientOriginalName();
                $name_only = pathinfo($filename, PATHINFO_FILENAME);
                $ext_only =  $request->foto->getClientOriginalExtension();

                $name = $name_only.'-'.date('His').'.'.$ext_only;
                    // dd($name);
                $request->foto->storeAs('public/fotomhs', $name);         
                $barangHilang = barangHilang::findOrFail($id);
		    	$barangHilang->waktu_diambil = $request->input('waktu_diambil');
		    	$barangHilang->npm = $request->input('npm');
		    	$barangHilang->phone = $request->input('phone');
		    	$barangHilang->status = 'Sudah Diambil';
		    	$barangHilang->name_mhs = $request->input('name_mhs');
		    	$barangHilang->foto = $name;
            }else{
	            	$response = [
	                'msg' => 'Gagal memperbarui data!!'
	           		 ];
	            return response()->json($response,201);
            }


    	if($barangHilang->save()){
    		$response = [
                'msg' => 'barangHilang ' .$barangHilang->name .' berhasil selesai!',
                'data' => $barangHilang
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui data Barang Hilang!'
            ];
            return response()->json($response,201);
    	}
    }

    public function update(request $request,$id)
    {
    	$this->validate($request,[
           'id_ruangan' => 'required',
           'name' => 'required',
           'keterangan' => 'required',
           'waktu_penemuan' => 'required',
       ]);

    	$barangHilang = barangHilang::findOrFail($id);
    	$barangHilang->id_ruangan = $request->input('id_ruangan');
    	$barangHilang->name = $request->input('name');
    	$barangHilang->keterangan = $request->input('keterangan');
    	$barangHilang->waktu_penemuan = $request->input('waktu_penemuan');

    	if($barangHilang->status != 'New'){
	    		$this->validate($request,[
	           'waktu_diambil' => 'required',
	           'npm' => 'required',
	           'name_mhs' => 'required',
	           'phone' => 'required',
	       ]);
	    		if ($request->hasFile('foto')){
	    		 unlink(storage_path('app/public/fotomhs') .$barangHilang->foto);
                $filename = $request->foto->getClientOriginalName();
                $name_only = pathinfo($filename, PATHINFO_FILENAME);
                $ext_only =  $request->foto->getClientOriginalExtension();

                $name = $name_only.'-'.date('His').'.'.$ext_only;
                    // dd($name);
                $request->foto->storeAs('public/fotomhs', $name);         
                $barangHilang = barangHilang::findOrFail($id);
		    	$barangHilang->waktu_diambil = $request->input('waktu_diambil');
		    	$barangHilang->npm = $request->input('npm');
		    	$barangHilang->phone = $request->input('phone');
		    	$barangHilang->name_mhs = $request->input('name_mhs');
		    	$barangHilang->foto = $name;
            }else{
	           	$barangHilang = barangHilang::findOrFail($id);
		    	$barangHilang->waktu_diambil = $request->input('waktu_diambil');
		    	$barangHilang->npm = $request->input('npm');
		    	$barangHilang->phone = $request->input('phone');
		    	$barangHilang->name_mhs = $request->input('name_mhs');
            }
    	}

    	if($barangHilang->save()){
    		$response = [
                'msg' => 'barangHilang ' .$barangHilang->name .' berhasil diperbarui!',
                'data' => $barangHilang
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal perbarui data Barang Hilang!'
            ];
            return response()->json($response,201);
    	}


    }

    public function destroy($id)
    {
    	$barangHilang = barangHilang::findOrFail($id);
    	if($barangHilang->status != 'New'){    		
    	unlink(storage_path('app/public/fotomhs') .$barangHilang->foto);
    	}
    	if($barangHilang->delete()){
    		$response = [
                'msg' => 'barangHilang ' .$barangHilang->name .' berhasil dihapus!',
                'data' => $barangHilang
            ];
            return response()->json($response,201);
    	}else{
    		$response = [
                'msg' => 'Gagal hapus data Barang Hilang!'
            ];
            return response()->json($response,201);
    	}
    }

    

}
