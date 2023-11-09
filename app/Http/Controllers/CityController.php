<?php

namespace App\Http\Controllers;
use App\City;
use App\State;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {              
		return view('city.index', ['cityList' => City::orderBy('id','desc')->get(), 'active_menu' => 'city']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $stateList = State::where('status', 1)->get();
		return view('city.create', ['stateList' => $stateList, 'active_menu' => 'city']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'city_name'     => 'required',
            'state_id'     => 'required'
        ]);

        $city = new City;        
        $city->state_id  = $request->state_id;
        $city->city_name  = $request->city_name;
        $city->save();

        $lid = $city->id;
        
        return redirect()->route('city.index')->withStatus(__('City successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(City $city)
    {
        return view('city.show', ['stateDetails' => $city, 'active_menu' => 'city']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $advertisement
     * @return \Illuminate\Http\Response
     */

    public function edit(City $city)
    {
        $active_menu = 'city';        
        $stateList = State::where('status', 1)->get();
        return view('city.edit', compact('city','active_menu', 'stateList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, City $city)
    {
        $this->validate($request, [
            'city_name'     => 'required',
            'state_id'     => 'required'
        ]);
        
		$city->state_id 	       = $request->state_id;
        $city->city_name           = $request->city_name;
        $city->update();        
        return redirect()->route('city.index')->withStatus(__('City Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if($city->status==1){
            $city->status=0;
            $city->save();
            return redirect()->route('city.index')->withStatus(__('City Successfully Inactive.'));
        }else{
            $city->status=1;
            $city->save();
            return redirect()->route('city.index')->withStatus(__('City Successfully Active.'));
        }
    }
}