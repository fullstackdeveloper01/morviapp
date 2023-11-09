<?php

namespace App\Http\Controllers;

use App\Category;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class SubCategoryController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('sub-category.index', ['categoryList' =>Category::where('parent_id', '>', 0)->orderBy('id','desc')->get(), 'active_menu' => 'subCategory']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            $parentCategory = Category::where('parent_id', 0)->get();
            return view('sub-category.create', ['parentCategory' => $parentCategory, 'active_menu' => 'subCategory']);
        }else return redirect()->route('subCategory.index')->withStatus(__('No Access'));
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

            'parent_id' => 'required',
			
            'category_name' => 'required'

        ]);

        $category = new Category;

        $category->parent_id = $request->parent_id;
        $category->category_name = $request->category_name;

        $category->save();

        $lid = $category->id;

		if($request->file('category_icon')){

            $file = $request->file('category_icon');

            $filename= $category->id.'_category.'.$file->getClientOriginalExtension();			

            $file->move(public_path('uploads/category'), $filename);

            $category->category_icon = $filename;

            $category->save();
        }
        return redirect()->route('subCategory.index')->withStatus(__('Sub category successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $category
     * @return \Illuminate\Http\Response
     */

    public function edit(Category $subCategory)
    {
		
        $active_menu = 'category';
		$parentCategory = Category::where('parent_id', 0)->get();
        return view('sub-category.edit', compact('subCategory','parentCategory','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Category $subCategory)
    {
        $this->validate($request, [
            'parent_id' => 'required',
            'category_name' => 'required'
        ]);
		$subCategory->parent_id = $request->parent_id;
        $subCategory->category_name = $request->category_name;        
        $subCategory->update();
		
        if($request->file('category_icon')){
            $path = public_path()."/uploads/category/".$subCategory->category_icon;
			if(is_file($path))
			{
				unlink($path);
			}
            $file = $request->file('category_icon');
            $filename= $subCategory->id.'_category.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/category'), $filename);
            $subCategory->category_icon = $filename;
            $subCategory->update();
        }
        return redirect()->route('subCategory.index')->withStatus(__('Sub category Successfully Updated.'));
    }

    public function destroy(Category $subCategory)
    {
        if($subCategory->status==1){
            $subCategory->status=0;
            $subCategory->save();
            return redirect()->route('subCategory.index')->withStatus(__('Sub Category Successfully Inactive.'));
        }else{
            $subCategory->status=1;
            $subCategory->save();
            return redirect()->route('subCategory.index')->withStatus(__('Sub Category Successfully Active.'));
        }
    }
}