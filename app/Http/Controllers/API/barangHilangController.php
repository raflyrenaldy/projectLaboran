<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Component\barangHilang;
use App\Component\ruangan;
use Carbon\Carbon;
class barangHilangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return barangHilang::with('get_user','get_ruangan')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $barangHilang = barangHilang::with('get_user','get_ruangan')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($barangHilang->isEmpty()){
                $barangHilang = barangHilang::with('get_user','get_ruangan')->whereHas('get_ruangan', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if($barangHilang->isEmpty()){
                $barangHilang = barangHilang::with('get_user','get_ruangan')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('keterangan','LIKE',"%$search%")
                    ->orWhere('waktu_penemuan','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $barangHilang = barangHilang::with('get_user','get_ruangan')->latest()->paginate(5);
        }
        
        return $barangHilang;
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
            'id_ruangan' => 'required',
            'name' => 'required',
            'waktu_penemuan' => 'required',
        ]);
         
 
         $barangHilang = new barangHilang ([
             'id_user' => Auth('api')->User()->id,
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
           'npm' => 'required',
           'name_mhs' => 'required',
           'phone' => 'required'
       ]);

            $name = time().'.' . explode('/', explode(':', substr($request->foto, 0, strpos($request->foto, ';')))[1])[1];
            \Image::make($request->foto)->save(public_path('img/fotomhs/').$name);

                $barangHilang = barangHilang::findOrFail($id);
		    	$barangHilang->waktu_diambil = now();
		    	$barangHilang->npm = $request->input('npm');
		    	$barangHilang->phone = $request->input('phone');
		    	$barangHilang->status = 'Sudah Diambil';
		    	$barangHilang->name_mhs = $request->input('name_mhs');
		    	$barangHilang->foto = $name;
           


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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return barangHilang::with('get_user','get_ruangan')->findOrFail($id);
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
            'id_ruangan' => 'required',
            'name' => 'required',
            'keterangan' => 'required',
            'waktu_penemuan' => 'required',
        ]);
        $timePenemuan = \Carbon\Carbon::parse($request->waktu_penemuan);
        $waktu_penemuan = $timePenemuan->format('Y-m-d H:i:s');
        $request->merge(['waktu_penemuan' => $waktu_penemuan]);
 
         $barangHilang = barangHilang::findOrFail($id);
 
         if($barangHilang->status != 'New'){
                 $this->validate($request,[
                'waktu_diambil' => 'required',
                'npm' => 'required',
                'name_mhs' => 'required',
                'phone' => 'required',
            ]);
            $timeDiambil = \Carbon\Carbon::parse($request->waktu_penemuan);
            $waktu_pengambilan = $timeDiambil->format('Y-m-d H:i:s');
            $request->merge(['waktu_diambil' => $waktu_pengambilan]);

            $currentPhoto = $barangHilang->foto;
            if($request->foto != $currentPhoto){
               
                $name = time().'.' . explode('/', explode(':', substr($request->foto, 0, strpos($request->foto, ';')))[1])[1];
    
                \Image::make($request->foto)->save(public_path('img/fotomhs/').$name);
    
                $request->merge(['foto' => $name]);
    
                $userPhoto = public_path('img/fotomhs/').$currentPhoto;
    
                if(file_exists($userPhoto)){
                    @unlink($userPhoto);
                }
            }
            $barangHilang->update($request->all());
             
         }else{
            $barangHilang->update($request->all());
         }
 
         return ['message' => ' Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
