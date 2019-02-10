<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Component\tahunAjaran;

class tahunAjaranController extends Controller
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
        return tahunAjaran::latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $tahunAjaran = tahunAjaran::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                ->orWhere('waktu_mulai','LIKE',"%$search%")
                ->orWhere('waktu_berakhir','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $tahunAjaran = tahunAjaran::latest()->paginate(5);
        }
        return $tahunAjaran;
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
            'waktu_mulai' => 'required',
            'waktu_berakhir' => 'required'
        ]);
        return tahunAjaran::create([
    		'name' => $request->input('name'),
    		'waktu_mulai' => $request->input('waktu_mulai'),
    		'waktu_berakhir' => $request->input('waktu_berakhir'),
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
            'waktu_mulai' => 'sometimes',
            'waktu_berakhir' => 'sometimes'
        ]);
        $thnAjaran = tahunAjaran::findOrFail($id);
        $thnAjaran->update($request->all());

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
        $thnAjaran = tahunAjaran::findOrFail($id);
        $thnAjaran->delete();
        return ['Message' => 'Tahun Ajaran Deleted'];
    }
}
