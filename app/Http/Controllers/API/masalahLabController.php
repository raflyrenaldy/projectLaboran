<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Component\ruangan;
use App\Component\masalahLab;
use Auth;

class masalahLabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return masalahLab::with('get_user','get_thnajaran')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $masalahLab = masalahLab::with('get_user','get_thnajaran')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($masalahLab->isEmpty()){
                $masalahLab = masalahLab::with('get_user','get_thnajaran')->whereHas('get_thnajaran', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              }
              if($masalahLab->isEmpty()){
                $masalahLab = masalahLab::with('get_user','get_thnajaran')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('keterangan','LIKE',"%$search%")
                    ->orWhere('waktu_mulai','LIKE',"%$search%")
                    ->orWhere('waktu_selesai','LIKE',"%$search%")
                    ->orWhere('yang_bertugas','LIKE',"%$search%")
                    ->orWhere('solusi_solved','LIKE',"%$search%")
                    ->orWhere('ruangan','LIKE',"%$search%")
                    ->orWhere('status','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $masalahLab = masalahLab::with('get_user','get_thnajaran')->latest()->paginate(5);
        }
        
        return $masalahLab;
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
            'name' => 'required',
            'ruangan' => 'required'
        ]);

        foreach ($request->ruangan as $key) {
    			$rgn = ruangan::findOrFail($key);
                $ruangans[] = $rgn->name;
        }
        $makeString = implode(", ",$ruangans);  
        $makeArray = (explode(", Lab ",$makeString));
        $ruangan = (implode(", ",$makeArray)); 

    	return masalahLab::create([
    		'id_user' => Auth('api')->User()->id,
    		'id_thnajaran' => $request->input('id_thnajaran'),
    		'name' => $request->input('name'),
    		'keterangan' => $request->input('keterangan'),
    		'ruangan' => $ruangan,
    		'status' => 'New',
    		'slug' => str_replace(" ", "-", strtolower($request->input('name')))
        ]);
        
        
    }

    public function start($id)
    {
    	$masalahLab = masalahLab::findOrFail($id);
    	$masalahLab->id_user = Auth('api')->User()->id;		
    	$masalahLab->waktu_mulai = now();
    	$masalahLab->status = 'Proses';
    	$masalahLab->save();
    	$response = [
                'msg' => 'masalahLab berhasil dimulai!',
                'data' => $masalahLab
            ];
            return response()->json($response,201);
    }

    public function alluser()
    {
        return User::all();
    }
    public function finish(request $request,$id)
    {
    	$this->validate($request,[
           'solusi_solved' => 'required'
       ]);

    	$masalahLab = masalahLab::findOrFail($id);
    	if($request->input('keterangan') != null){    
    	$masalahLab->id_user = Auth('api')->User()->id;		
    	$masalahLab->keterangan = $request->input('keterangan');
    	}
    	$masalahLab->waktu_selesai = now();
    	$masalahLab->status = 'Selesai';
    	$masalahLab->solusi_solved = $request->input('solusi_solved');
    	
    	$masalahLab->yang_bertugas = 'Team Laboran';
        $masalahLab->save();
        
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
            'keterangan' => 'sometimes'
        ]);
 
         $slug = str_replace(" ", "-", strtolower($request->input('name')));
         $request->merge(['slug' => $slug]);
         $request->merge(['id_user' => Auth('api')->User()->id]);
         $masalahLab = masalahLab::findOrFail($id);
         if($masalahLab->status == 'New'){
           $masalahLab->update($request->all());
         }else if($masalahLab->status == 'Proses'){
             $this->validate($request,[
            'waktu_mulai' => 'sometimes'
        ]);
            $masalahLab->update($request->all());
         }else{
             $this->validate($request,[
            'waktu_mulai' => 'sometimes',
            'solusi_solved' => 'required'
        ]);
             $yang_bertugas = 'Team Laboran';
         
            $request->merge(['yang_bertugas' => $yang_bertugas]);
            $masalahLab->update($request->all());
         }
         return [
             'message' => 'success'
         ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
}
