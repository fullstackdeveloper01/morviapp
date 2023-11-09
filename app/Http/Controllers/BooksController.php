<?php

namespace App\Http\Controllers;
use App\Book;
use App\User;
use App\Exam;
use App\Publisher;
use App\Settings;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\DriverCreated;
use Illuminate\Http\UploadedFile;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booksList = '';
        if(auth()->user()->role == 2){
            $publisherId = auth()->user()->id;
            $booksList = Book::where(['publisher_id' => $publisherId])->orderBy('id','desc')->get();
        }
        else{
            $booksList = Book::orderBy('id','desc')->get();
        }
        return view('books.index', ['booksList' => $booksList, 'active_menu' => 'books']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $settingData = Settings::where('id', 1)->first('delivery');
        $adminCommission = $settingData->delivery;
        $examList = Exam::where('status', 1)->get();
        $publisherId = '';
        if(auth()->user()->role == 2){
            $publisherId = auth()->user()->id;
            $publisherList = User::where(['active' => 1, 'role' => 2, 'id' => $publisherId])->get();
        }
        else{
            $publisherList = User::where(['active' => 1, 'role' => 2])->get();
        }
        return view('books.create', ['active_menu' => 'books', 'examList' => $examList, 'publisherList' => $publisherList, 'adminCommission' => $adminCommission, 'publisherId' => $publisherId]);
        /*if(auth()->user()->hasRole('admin')){
        }else return redirect()->route('books.index')->withStatus(__('No Access'));*/
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
            'book_name' => 'required',
            'book_type' => 'required',
            'exam_id' => 'required',
            'heading' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'sample_book_upload' => 'required|mimes:PDF,pdf',
            'main_book_upload' => 'required|mimes:PDF,pdf',
            'selling_price' => 'required'
        ]);

        $book = new Book;

        $book->book_name 	   = $request->book_name;
        $book->book_type       = $request->book_type;
        $book->exam_id         = $request->exam_id;
        $book->publisher_id         = $request->publisher_id;
        $book->selling_price   = $request->selling_price;
        $book->heading 		   = $request->heading;
        $book->description 	   = $request->ndneditor;
        if($request->exam_type_book != '')
        {
            $book->exam_type_book = implode(', ',$request->exam_type_book);
        }
        else
        {
            $book->exam_type_book = ''; 
        }

        $book->save();

        $lid = $book->id;
		if($request->file('image')){
            $file = $request->file('image');
            $filename= $lid.'_book_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename);
            $book->image = $filename;
            $book->save();
        }
		if($request->file('sample_book_upload')){
            $file = $request->file('sample_book_upload');
            $filename1 = $lid.'_sample_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename1);
            $book->sample_book_upload = $filename1;
            $book->save();
        }
		if($request->file('main_book_upload')){
            $file = $request->file('main_book_upload');
            $filename2 = $lid.'_main_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename2);
            $book->main_book_upload = $filename2;
            $book->save();
        }
        if($request->file('sample_epub_book')){
            $file = $request->file('sample_epub_book');
            $filename3 = $lid.'_sample_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename3);
            $book->sample_epub_book = $filename3;
            $book->save();
        }   if($request->file('main_epub_book')){
            $file = $request->file('main_epub_book');
            $filename4 = $lid.'_main_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename4);
            $book->main_epub_book = $filename4;
            $book->save();
        }
        return redirect()->route('books.index')->withStatus(__('Book successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(Book $book)
    {
        return view('books.show', ['booksDetails' => $book, 'active_menu' => 'books']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $book
     * @return \Illuminate\Http\Response
     */

    public function edit(Book $book)
    {
        $active_menu = 'books';
        $settingData = Settings::where('id', 1)->first('delivery');
        $adminCommission = $settingData->delivery;
        $publisherId = '';
        if(auth()->user()->role == 2){
            $publisherId = auth()->user()->id;
            $publisherList = User::where(['active' => 1, 'role' => 2, 'id' => $publisherId])->get();
        }
        else{
            $publisherList = User::where(['active' => 1, 'role' => 2])->get();
        }
        /*$publisherList = User::where(['active' => 1, 'role' => 2])->get();*/
        $examList = Exam::where('status', 1)->get();
        return view('books.edit', compact('book','active_menu', 'examList', 'adminCommission', 'publisherList', 'publisherId'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Book $book)
    {
        $this->validate($request, [
            'book_name' => 'required',
            'book_type' => 'required',
            'exam_id' => 'required',
            'heading' => 'required',
            'image' => 'mimes:jpeg,png,jpg|max:2048',
            'sample_book_upload' => 'mimes:PDF,pdf',
            'main_book_upload' => 'mimes:PDF,pdf',
            'selling_price' => 'required'
        ]);
        
		$book->book_name 	   = $request->book_name;
        $book->book_type       = $request->book_type;
        $book->exam_id         = $request->exam_id;
        $book->publisher_id    = $request->publisher_id;
        $book->selling_price   = $request->selling_price;
        $book->heading 		   = $request->heading;
        $book->description 	   = $request->ndneditor;
        if($request->exam_type_book != '')
        {
            $book->exam_type_book = implode(', ',$request->exam_type_book);
        }
        else
        {
            $book->exam_type_book = ''; 
        }        

        $book->update();
        if($request->file('image')){
            $path1 = public_path()."/uploads/books/".$book->image;
            if(is_file($path1))
            {
                unlink($path1);
            }
            $file = $request->file('image');
            $filename= $book->id.'_book_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename);
            $book->image = $filename;
            $book->save();
        }
		if($request->file('sample_book_upload')){
            $path2 = public_path()."/uploads/books/".$book->sample_book_upload;
            if(is_file($path2))
            {
                unlink($path2);
            }
            $file = $request->file('sample_book_upload');
            $filename1 = $book->id.'_sample_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename1);
            $book->sample_book_upload = $filename1;
            $book->save();
        }
		if($request->file('main_book_upload')){
            $path3 = public_path()."/uploads/books/".$book->main_book_upload;
            if(is_file($path3))
            {
                unlink($path3);
            }
            $file = $request->file('main_book_upload');
            $filename2 = $book->id.'_main_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename2);
            $book->main_book_upload = $filename2;
            $book->save();
        }
        if($request->file('sample_epub_book')){
            $path4 = public_path()."/uploads/books/".$book->sample_epub_book;
            if(is_file($path4))
            {
                unlink($path4);
            }
            $file = $request->file('sample_epub_book');
            $filename3 = $book->id.'_sample_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename3);
            $book->sample_epub_book = $filename3;
            $book->save();
        }
        if($request->file('main_epub_book')){
            $path5 = public_path()."/uploads/books/".$book->main_epub_book;
            if(is_file($path5))
            {
                unlink($path5);
            }
            $file = $request->file('main_epub_book');
            $filename4 = $book->id.'_main_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/books'), $filename4);
            $book->main_epub_book = $filename4;
            $book->save();
        }
        return redirect()->route('books.index')->withStatus(__('Book Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        if($book->status==1){
            $book->status=0;
            $book->save();
            return redirect()->route('books.index')->withStatus(__('Book Successfully Inactive.'));
        }else{
            $book->status=1;
            $book->save();
            return redirect()->route('books.index')->withStatus(__('Book Successfully Active.'));
        }
    }

    /**
    *   @function: Select Exam Type
    */
    public function selectExamType($id)
    {
        $resp = '';
        $i = 1;
        if($id)
        {
            $examData = Exam::where('id', $id)->first('exam_type');
            if(!empty($examData))
            {
                $examTypeArray = explode(', ', $examData->exam_type);
                if(count($examTypeArray) > 0)
                {
                    $resp .= '<label class="form-control-label" for="exam_type_book">Exam Type</label><br>';
                    $resp .= '<div class="row">';
                    if(in_array('Pre', $examTypeArray)){ $i++;
                        $resp .= '<div class="col-md-4 d-flex align-items-center">';
                        $resp .= '<label class="form-control-label mr-3" for="exam_type_book">Pre</label>';
                        $resp .= '<label class="custom-toggle">';
                        $resp .= '<input type="checkbox" id="exam_type_pre" name="exam_type_book[]" value="Pre">';
                        $resp .= '<span class="custom-toggle-slider rounded-circle"></span>';
                        $resp .= '</label>';
                        $resp .= '</div>';
                    }
                    if(in_array('Main', $examTypeArray)){ $i++;
                        $resp .= '<div class="col-md-4 d-flex align-items-center">';
                        $resp .= '<label class="form-control-label mr-3" for="exam_type_book">Main</label>';
                        $resp .= '<label class="custom-toggle">';
                        $resp .= '<input type="checkbox" id="exam_type_main" name="exam_type_book[]" value="Main">';
                        $resp .= '<span class="custom-toggle-slider rounded-circle"></span>';
                        $resp .= '</label>';
                        $resp .= '</div>';
                    }
                    $resp .= '</div>';
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