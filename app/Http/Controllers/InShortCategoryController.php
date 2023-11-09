<?php

namespace App\Http\Controllers;

use App\InShortCategory;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class InShortCategoryController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('inShortCategory.index', ['inShortCategoryList' =>InShortCategory::orderBy('id','desc')->get(), 'active_menu' => 'inShortCategory']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('inShortCategory.create', ['active_menu' => 'inShortCategory']);
        }else return redirect()->route('inShortCategory.index')->withStatus(__('No Access'));
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

            'category_icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'category_name' => 'required'

        ]);

        $inShortCategory = new InShortCategory;

        $inShortCategory->category_name = $request->category_name;

        $inShortCategory->save();

        $lid = $inShortCategory->id;

		if($request->file('category_icon')){

            $file = $request->file('category_icon');

            $filename= $inShortCategory->id.'_category.'.$file->getClientOriginalExtension();			

            $file->move(public_path('uploads/daily_short_category'), $filename);

            $inShortCategory->category_icon = $filename;

            $inShortCategory->save();
        }
        return redirect()->route('inShortCategory.index')->withStatus(__('inShortCategory successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $inShortCategory
     * @return \Illuminate\Http\Response
     */

    public function edit(InShortCategory $inShortCategory)
    {
        $active_menu = 'inShortCategory';
        return view('inShortCategory.edit', compact('inShortCategory','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, InShortCategory $inShortCategory)
    {
        $this->validate($request, [
            'category_name' => 'required'
        ]);
        $inShortCategory->category_name = $request->category_name;        
        $inShortCategory->update();
		
        if($request->file('category_icon')){
            $path = public_path()."/uploads/daily_short_category/".$inShortCategory->category_icon;
			if(is_file($path))
			{
				unlink($path);
			}
            $file = $request->file('category_icon');
            $filename= $inShortCategory->id.'_category.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/daily_short_category'), $filename);
            $inShortCategory->category_icon = $filename;
            $inShortCategory->update();
        }
        return redirect()->route('inShortCategory.index')->withStatus(__('inShortCategory Successfully Updated.'));
    }

    public function destroy(InShortCategory $inShortCategory)
    {
        if($inShortCategory->status==1){
            $inShortCategory->status=0;
            $inShortCategory->save();
            return redirect()->route('inShortCategory.index')->withStatus(__('Category Successfully Inactive.'));
        }else{
            $inShortCategory->status=1;
            $inShortCategory->save();
            return redirect()->route('inShortCategory.index')->withStatus(__('Category Successfully Active.'));
        }
    }
}