<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Component\permintaanAplikasi;
use App\Component\tahunAjaran;
use App\Component\ruangan;

class permintaanAplikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $permintaanAplikasi = permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($permintaanAplikasi->isEmpty()){
                $permintaanAplikasi = permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')->whereHas('get_thnajaran', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if ($permintaanAplikasi->isEmpty()){
                $permintaanAplikasi = permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')->whereHas('get_ruangan', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                  })->paginate(20);
              }
              if($permintaanAplikasi->isEmpty()){
                $permintaanAplikasi = permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('name_dosen','LIKE',"%$search%")
                    ->orWhere('deadline','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $permintaanAplikasi = permintaanAplikasi::with('get_user','get_thnajaran','get_ruangan')->latest()->paginate(5);
        }
        
        return $permintaanAplikasi;
    }   

    public function ruangan()
    {
        return ruangan::all();
    }

    public function thnAjaran()
    {
        return tahunAjaran::all();
    }

    public function finish($id)
    {
    	$permintaanAplikasi = permintaanAplikasi::findOrFail($id);
    	$permintaanAplikasi->id_user = Auth('api')->User()->id;	
    	$permintaanAplikasi->status = 'Selesai';
        $permintaanAplikasi->save();
        
        return ['message' => 'success'];
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
            'id_thnajaran' => 'required',
            'id_ruangan' => 'required',
            'name' => 'required',
            'name_dosen' => 'required',
            'deadline' => 'required'
        ]);
    	foreach ($request->id_ruangan as $key) {
    		$permintaanAplikasi = new permintaanAplikasi ([
    		'id_user' => Auth('api')->User()->id,
    		'id_ruangan' => $key,
    		'id_thnajaran' => $request->input('id_thnajaran'),
    		'name' => $request->input('name'),
    		'name_dosen' => $request->input('name_dosen'),
    		'status' => 'New',
    		'deadline' => $request->input('deadline')
    	]);
    		$permintaanAplikasi->save();
        }
        return ['message' => 'success'];
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
            'id_thnajaran' => 'required',
            'name' => 'required',
            'id_ruangan' => 'sometimes',
            'name_dosen' => 'required',
            'deadline' => 'sometimes'
        ]);

        $permintaanAplikasi = permintaanAplikasi::findOrFail($id);
    	if($request->id_ruangan != $permintaanAplikasi->id_ruangan){
            $checkPermintaanAplikasi = permintaanAplikasi::where([
                                                    ['id_ruangan','=',$request->id_ruangan],
                                                    ['name','=',$permintaanAplikasi->name]
                                                        ])->get();
                if($checkPermintaanAplikasi->isEmpty()){                    
                    $permintaanAplikasi->update($request->all());
                    return ['message' => 'success'];
                }else{
                    return ['message' => 'Failure'];
                }
        }else{
            $permintaanAplikasi->update($request->all());
            return ['message' => 'success'];
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
        $permintaanAplikasi = permintaanAplikasi::findOrFail($id);
        $permintaanAplikasi->delete();
        return ['message' => 'success'];
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
