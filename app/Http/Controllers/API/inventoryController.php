<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Component\inventory;
use Auth;

class inventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inventory::with('get_user')->latest()->paginate(5);
    }

    public function search()
    {
        if ($search = \Request::get('q')) {
            $inventory = inventory::with('get_user')
                ->whereHas('get_user', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
              })->paginate(20);
              if($inventory->isEmpty()){
                $inventory = inventory::with('get_user')->where(function($query) use ($search){
                    $query->where('name','LIKE',"%$search%")
                    ->orWhere('jumlah','LIKE',"%$search%")
                    ->orWhere('keterangan','LIKE',"%$search%");
                })->paginate(20);
              }
        }else{
            $inventory = inventory::with('get_user')->latest()->paginate(5);
        }
        return $inventory;
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
            'name' => 'required',
            'jumlah' => 'required'
        ]);
            
        return inventory::create([
    		'id_user' => Auth('api')->User()->id,
    		'name' => $request->input('name'),
    		'jumlah' => $request->input('jumlah'),
    		'keterangan' => $request->input('keterangan')
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
            'jumlah' => 'required'
        ]);
        $inventory = inventory::findOrFail($id);
    	$inventory->id_user = Auth('api')->User()->id;
    	$inventory->name = $request->input('name');
    	$inventory->jumlah = $request->input('jumlah');
        $inventory->keterangan = $request->input('keterangan');
        $inventory->save();
        return ['message' => 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventory = inventory::findOrFail($id);
        $inventory->delete();
        return ['message' => 'Success'];
    }
}
