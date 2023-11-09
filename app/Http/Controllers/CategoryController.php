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

class CategoryController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('category.index', ['categoryList' =>Category::where('parent_id', 0)->orderBy('id','desc')->get(), 'active_menu' => 'category']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('category.create', ['active_menu' => 'category']);
        }else return redirect()->route('category.index')->withStatus(__('No Access'));
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

        $category = new Category;

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
        return redirect()->route('category.index')->withStatus(__('category successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $category
     * @return \Illuminate\Http\Response
     */

    public function edit(Category $category)
    {
        $active_menu = 'category';
        return view('category.edit', compact('category','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'category_name' => 'required'
        ]);
        $category->category_name = $request->category_name;        
        $category->update();
		
        if($request->file('category_icon')){
            $path = public_path()."/uploads/category/".$category->category_icon;
			if(is_file($path))
			{
				unlink($path);
			}
            $file = $request->file('category_icon');
            $filename= $category->id.'_category.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/category'), $filename);
            $category->category_icon = $filename;
            $category->update();
        }
        return redirect()->route('category.index')->withStatus(__('category Successfully Updated.'));
    }

    public function destroy(Category $category)
    {
        if($category->status==1){
            $category->status=0;
            $category->save();
            return redirect()->route('category.index')->withStatus(__('Category Successfully Inactive.'));
        }else{
            $category->status=1;
            $category->save();
            return redirect()->route('category.index')->withStatus(__('Category Successfully Active.'));
        }
    }

    /**
    *   @function: Select Exam Type
    */
    public function getSubCategory($id)
    {
        $resp = '';
        $i = 1;
        if($id)
        {
            $categoryData = Category::where('parent_id', $id)->get();
            if(!empty($categoryData))
            {
                $i++;
                $resp .= '<option value="">Select Sub Category</option>';
                foreach($categoryData as $cat){
                    $resp .= '<option value="'.$cat->id.'">'.$cat->category_name.'</option>';
                }
            }
        }
        if($i == 1)
        {
            $resp = '';
        }
        return $resp;
    }
}