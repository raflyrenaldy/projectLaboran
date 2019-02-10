<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Component\ruangan;

class ruanganController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ruangan::latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $ruangan = ruangan::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                ->orWhere('jumlah_kursi','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $ruangan = ruangan::latest()->paginate(5);
        }
        return $ruangan;
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
            'name'        => 'required',
            'jumlah_kursi' => 'required'
        ]);
 
         return ruangan::create([
             'name' => $request->input('name'),
             'jumlah_kursi' => $request->input('jumlah_kursi'),
             'slug' => str_replace(" ", "-", strtolower($request->input('name')))
         ]);
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
            'name'        => 'required',
            'jumlah_kursi' => 'required'
        ]);

        $ruangan = ruangan::findOrFail($id);
    	$ruangan->name = $request->input('name');
    	$ruangan->jumlah_kursi = $request->input('jumlah_kursi');
        $ruangan->slug = str_replace(" ", "-", strtolower($request->input('name')));
        $ruangan->save();
        return ['message' => 'success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ruangan = ruangan::findOrFail($id);
        $ruangan->delete();
        return ['Message' => 'Ruangan Deleted'];
    }
}
