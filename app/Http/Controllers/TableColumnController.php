<?php

namespace App\Http\Controllers;

use App\Category;

use App\CategoryTable;

use App\TableColumn;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class TableColumnController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('table-column.index', ['tableColumnList' =>CategoryTable::orderBy('id','desc')->get(), 'active_menu' => 'tableColumn']);
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
            return view('table-column.create', ['parentCategory' => $parentCategory, 'active_menu' => 'tableColumn']);
        }else return redirect()->route('tableColumn.index')->withStatus(__('No Access'));
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

            'table_name' => 'required',

            'column_name' => 'required',
			
            'column_type' => 'required',
            
			'category_id' => 'required',
			
            'sub_category_id' => 'required'

        ]);

        $table_name_id = '';
        $exCategoryTable = CategoryTable::where(['category_id' => $request->category_id, 'sub_category_id' => $request->sub_category_id, 'table_name' => $request->table_name])->first();
        if(!empty($exCategoryTable)){
            $table_name_id = $exCategoryTable->id;
        }
        else{
            $categoryTable = new CategoryTable();
            $categoryTable->category_id = $request->category_id;
            $categoryTable->sub_category_id = $request->sub_category_id;
            $categoryTable->table_name = $request->table_name;
            $categoryTable->save();                        
            $table_name_id = $categoryTable->id;
        }
        $columnName = $request->column_name;
        $columnType = $request->column_type;
        $columnValue = $request->column_value;
        for($i=0; $i<count($columnName); $i++){
            if($columnName[$i] != ''){
                $tableColumn = new TableColumn;
                $tableColumn->category_table_id = $table_name_id;
                $tableColumn->column_name       = $columnName[$i];
                $tableColumn->column_type       = $columnType[$i];
                if($columnValue[$i] == '' || $columnValue[$i] == null){
                    $tableColumn->column_value  = '';
                }
                else{
                    $tableColumn->column_value  = $columnValue[$i];
                }
                $tableColumn->save();
            }
        }

        $lid = $tableColumn->id;
        return redirect()->route('tableColumn.index')->withStatus(__('Table & columns successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $category
     * @return \Illuminate\Http\Response
     */

    public function edit(CategoryTable $tableColumn)
    {
		
        $active_menu = 'tableColumn';
		$parentCategory = Category::where('parent_id', 0)->get();
		$subCategory = Category::where('parent_id', $tableColumn->category_id)->get();
        $tableColumnData = TableColumn::where('category_table_id', $tableColumn->id)->orderBy('id', 'asc')->get();
        return view('table-column.edit', compact('tableColumn','tableColumnData', 'subCategory', 'parentCategory','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, CategoryTable $tableColumn)
    {
        $this->validate($request, [
            'table_name' => 'required',

            'column_name' => 'required',
            
            'column_type' => 'required',
            
            'category_id' => 'required',
            
            'sub_category_id' => 'required'
        ]);
        $tableColumn->category_id = $request->category_id;
        $tableColumn->sub_category_id = $request->sub_category_id;
        $tableColumn->table_name = $request->table_name;
        $affected = $tableColumn->update();             
        
        $removeData = TableColumn::where('category_table_id', $tableColumn->id)->delete();

        $columnName = $request->column_name;
        $columnType = $request->column_type;
        $columnValue = $request->column_value;
        for($i=0; $i<count($columnName); $i++){
            if($columnName[$i] != ''){
                $tableColumnData = new TableColumn;
                $tableColumnData->category_table_id = $tableColumn->id;
                $tableColumnData->column_name       = $columnName[$i];
                $tableColumnData->column_type       = $columnType[$i];
                if($columnValue[$i] == '' || $columnValue[$i] == null){
                    $tableColumnData->column_value  = '';
                }
                else{
                    $tableColumnData->column_value  = $columnValue[$i];
                }
                $tableColumnData->save();
            }
        }		
        return redirect()->route('tableColumn.index')->withStatus(__('Table column successfully updated.'));
    }

    public function destroy(CategoryTable $tableColumn)
    {
        if($tableColumn->status==1){
            $tableColumn->status=0;
            $tableColumn->save();
            return redirect()->route('tableColumn.index')->withStatus(__('Table Column Successfully Inactive.'));
        }else{
            $tableColumn->status=1;
            $tableColumn->save();
            return redirect()->route('tableColumn.index')->withStatus(__('Table Column Successfully Active.'));
        }
    }
}