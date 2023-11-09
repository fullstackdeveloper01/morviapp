<?php

namespace App\Http\Controllers;
use App\Advertisement;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\DriverCreated;
use Illuminate\Http\UploadedFile;

class MobileSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('mobile.index', ['advertisementList' =>Advertisement::orderBy('id','desc')->get(), 'active_menu' => 'mobile']);
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
            'title'     => 'required',
            'position'  => 'required'
        ]);

        $advertisement = new Advertisement;

        $advertisement->title 	   = $request->title;
        $advertisement->position   = $request->position;

        $advertisement->save();

        $lid = $advertisement->id;
        /*
		if($request->file('image')){
            $file = $request->file('image');
            $filename= $lid.'_book_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/advertisement'), $filename);
            $advertisement->image = $filename;
            $advertisement->advertisement_id = $lid;
            $advertisement->save();
        }
        */
        $fileArray = [];
        if ($request->hasFile('image')) {

            $files = $request->file('image');

            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();

                $filename = str_replace(' ','_',$filename);
                $file_name = time().rand(100,999).$filename;
                
                $file->move(public_path('uploads/advertisement'), $file_name);   
                $fileArray[] = $file_name;            

            }
            if(!empty($fileArray)){
                $advertisement->image = implode(',', $fileArray);
                $advertisement->save();                
            }
        }
        return redirect()->route('advertisement.index')->withStatus(__('Advertisement successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(Advertisement $advertisement)
    {
        return view('advertisement.show', ['advertisementDetails' => $advertisement, 'active_menu' => 'advertisement']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $advertisement
     * @return \Illuminate\Http\Response
     */

    public function edit(Advertisement $advertisement)
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

    public function update(Request $request, Advertisement $advertisement)
    {
        $this->validate($request, [
            'title'     => 'required',
            'position'  => 'required'
        ]);
        
		$advertisement->title 	    = $request->title;
        $advertisement->position    = $request->position;

        $advertisement->update();
        /*if($request->file('image')){
            $path1 = public_path()."/uploads/advertisement/".$advertisement->image;
            if(is_file($path1))
            {
                unlink($path1);
            }
            $file = $request->file('image');
            $filename= $advertisement->id.'_book_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/advertisement'), $filename);
            $advertisement->image = $filename;
            $advertisement->save();
        }*/
        $fileArray = [];
        //dd($selectedImg);
        if(!empty($request->selected_img))
        {
            $selectedImg = $request->selected_img;
            if(count($selectedImg) > 0){
                foreach($selectedImg as $rrr){
                    $fileArray[] = $rrr; 
                }
            }            
        }
        if ($request->hasFile('image')) {

            $files = $request->file('image');

            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();

                $filename = str_replace(' ','_',$filename);
                $file_name = time().rand(100,999).$filename;
                
                $file->move(public_path('uploads/advertisement'), $file_name);   
                $fileArray[] = $file_name;            

            }
        }
        if(!empty($fileArray)){
            $advertisement->image = implode(',', $fileArray);
            $advertisement->save();                
        }
        return redirect()->route('advertisement.index')->withStatus(__('Advertisement Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advertisement $advertisement)
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