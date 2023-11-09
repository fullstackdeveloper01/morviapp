<?php

namespace App\Http\Controllers;

use App\Brand;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class BrandsController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('brands.index', ['brandsList' => Brand::orderBy('id','desc')->get(), 'active_menu' => 'brands']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('brands.create', ['active_menu' => 'brands']);
        }else return redirect()->route('brands.index')->withStatus(__('No Access'));
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

            //'brand_icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'brand_name' => 'required'

        ]);

        $brands = new Brand;

        $brands->brand_name = $request->brand_name;

        $brands->save();

        $lid = $brands->id;

		if($request->file('brand_icon')){

            $file = $request->file('brand_icon');

            $filename= $brands->id.'_brand.'.$file->getClientOriginalExtension();			

            $file->move(public_path('uploads/brand'), $filename);

            $brands->brand_icon = $filename;

            $brands->save();
        }
        return redirect()->route('brands.index')->withStatus(__('Brand successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $brands
     * @return \Illuminate\Http\Response
     */

    public function edit(Brand $brand)
    {
        $active_menu = 'brands';
        return view('brands.edit', compact('brand','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Brand $brand)
    {
        $this->validate($request, [
            'brand_name' => 'required'
        ]);
        $brand->brand_name = $request->brand_name;        
        $brand->update();
		
        if($request->file('brand_icon')){
            $path = public_path()."/uploads/brand/".$brand->brand_icon;
			if(is_file($path))
			{
				unlink($path);
			}
            $file = $request->file('brand_icon');
            $filename= $brand->id.'_brand.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/brand'), $filename);
            $brand->brand_icon = $filename;
            $brand->update();
        }
        return redirect()->route('brands.index')->withStatus(__('Brand Successfully Updated.'));
    }

    public function destroy(Brand $brand)
    {
        if($brand->status==1){
            $brand->status=0;
            $brand->save();
            return redirect()->route('brands.index')->withStatus(__('Brand Successfully Inactive.'));
        }else{
            $brand->status=1;
            $brand->save();
            return redirect()->route('brands.index')->withStatus(__('Brand Successfully Active.'));
        }
    }
}