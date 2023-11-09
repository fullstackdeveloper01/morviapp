<?php

namespace App\Http\Controllers;
use App\State;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {              
		return view('state.index', ['stateList' => State::orderBy('id','desc')->get(), 'active_menu' => 'state']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
		return view('state.create', ['active_menu' => 'state']);
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
            'state_name'     => 'required'
        ]);

        $state = new State;        
        $state->state_name  = $request->state_name;
        $state->save();

        $lid = $state->id;
        
        return redirect()->route('state.index')->withStatus(__('State successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(State $state)
    {
        return view('state.show', ['stateDetails' => $state, 'active_menu' => 'state']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $advertisement
     * @return \Illuminate\Http\Response
     */

    public function edit(State $state)
    {
        $active_menu = 'state';        
        return view('state.edit', compact('state','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, State $state)
    {
        $this->validate($request, [
            'state_name'     => 'required'
        ]);
        
		$state->state_name 	       = $request->state_name;
        $state->update();        
        return redirect()->route('state.index')->withStatus(__('State Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        if($state->status==1){
            $state->status=0;
            $state->save();
            return redirect()->route('state.index')->withStatus(__('State Successfully Inactive.'));
        }else{
            $state->status=1;
            $state->save();
            return redirect()->route('state.index')->withStatus(__('State Successfully Active.'));
        }
    }
}