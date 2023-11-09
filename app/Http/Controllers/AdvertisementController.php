<?php

namespace App\Http\Controllers;
use App\User;
use App\UserAdvertisment;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\DriverCreated;
use Illuminate\Http\UploadedFile;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('advertisement.index', ['advertisementList' => UserAdvertisment::where('user_id', 1)->orderBy('id','desc')->get(), 'active_menu' => 'advertisement']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('advertisement.create', ['active_menu' => 'advertisement']);
        }else return redirect()->route('advertisement.index')->withStatus(__('No Access'));
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

            'advertisement' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',

            'advertisement_type' => 'required'

        ]);

        $advertisement = new UserAdvertisment;

        $advertisement->advertisement_type = $request->advertisement_type;
        $advertisement->user_id = 1;
        $advertisement->status = 1;
        $advertisement->valid_till = strtotime('+1 year');;

        $advertisement->save();

        $lid = $advertisement->id;

        if($request->file('advertisement')){

            $file = $request->file('advertisement');

            $filename= $advertisement->id.'_advertisement.'.$file->getClientOriginalExtension();           

            $file->move(public_path('uploads/advertisement'), $filename);

            $advertisement->advertisement = $filename;

            $advertisement->save();
        }
        return redirect()->route('advertisement.index')->withStatus(__('Advertisement successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $advertisement
     * @return \Illuminate\Http\Response
     */

    public function edit(UserAdvertisment $advertisement)
    {
        $active_menu = 'advertisement';
        return view('advertisement.edit', compact('advertisement','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, UserAdvertisment $advertisement)
    {
        $this->validate($request, [
            'advertisement_type' => 'required'
        ]);
        $advertisement->advertisement_type = $request->advertisement_type;        
        $advertisement->update();
        
        if($request->file('advertisement')){
            $path = public_path()."/uploads/advertisement/".$advertisement->advertisement;
            if(is_file($path))
            {
                unlink($path);
            }
            $file = $request->file('advertisement');
            $filename= $advertisement->id.'_advertisement.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/advertisement'), $filename);
            $advertisement->advertisement = $filename;
            $advertisement->update();
        }
        return redirect()->route('advertisement.index')->withStatus(__('Advertisement Successfully Updated.'));
    }

    public function destroy(UserAdvertisment $advertisement)
    {
        if($advertisement->status==1){
            $advertisement->status=0;
            $advertisement->save();
            return redirect()->route('advertisement.index')->withStatus(__('Advertisement Successfully Inactive.'));
        }else{
            $advertisement->status=1;
            $advertisement->save();
            return redirect()->route('advertisement.index')->withStatus(__('Advertisement Successfully Active.'));
        }
    }
}