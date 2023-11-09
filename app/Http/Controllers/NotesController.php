<?php

namespace App\Http\Controllers;
use App\Book;
use App\User;
use App\Exam;
use App\Settings;
use App\CoachingExam;
use App\CoachingNote;
use App\CoachingSubject;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\DriverCreated;
use Illuminate\Http\UploadedFile;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notesList = '';
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $notesList = CoachingNote::where(['coaching_id' => $coachingId])->orderBy('id','desc')->get();
        }
        elseif(auth()->user()->role == 0){
            $notesList = CoachingNote::orderBy('id','desc')->get();
        }
        return view('notes.index', ['notesList' => $notesList, 'active_menu' => 'notes']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $coachingExam = [];
        $examList = Exam::where('status', 1)->get();
        $coachingId = '';
        $subjectList = [];
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $coachingList = User::where(['active' => 1, 'role' => 3, 'id' => $coachingId])->get();
            $subjectList = CoachingSubject::where('coaching_id', $coachingId)->get();
            $selectedExam = CoachingExam::where('coaching_id', $coachingId)->pluck('exam_id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        else{
            $coachingList = User::where(['active' => 1, 'role' => 3])->get();
            $selectedExam = Exam::where('status', 1)->pluck('id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        return view('notes.create', ['active_menu' => 'notes', 'examList' => $examList, 'coachingExam' => $coachingExam, 'coachingList' => $coachingList, 'subjectList' => $subjectList, 'coachingId' => $coachingId]);
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
            'title_of_notes' => 'required',
            'subject_id' => 'required',
            'exam_id' => 'required',
            'heading' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'sample_note' => 'required|mimes:PDF,pdf',
            'main_note' => 'required|mimes:PDF,pdf',
            'selling_price' => 'required'
        ]);

        $note = new CoachingNote;

        $note->coaching_id     = $request->coaching_id;
        $note->subject_id      = $request->subject_id;
        $note->title_of_notes  = $request->title_of_notes;
        $note->note_type  = $request->note_type;
        $note->heading 		   = $request->heading;
        $note->description 	   = $request->description;
        $note->selling_price   = $request->selling_price;
        $note->exam_id         = $request->exam_id;
        if($request->exam_type_book != '')
        {
            $note->exam_type = implode(', ',$request->exam_type_book);
        }
        else
        {
            $note->exam_type = ''; 
        }

        $note->save();

        $lid = $note->id;
		if($request->file('image')){
            $file = $request->file('image');
            $filename= $lid.'_note_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename);
            $note->image = $filename;
            $note->save();
        }
		if($request->file('sample_note')){
            $file = $request->file('sample_note');
            $filename1 = $lid.'_sample_note.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename1);
            $note->sample_note = $filename1;
            $note->save();
        }
		if($request->file('main_note')){
            $file = $request->file('main_note');
            $filename2 = $lid.'_main_note.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename2);
            $note->main_note = $filename2;
            $note->save();
        }
        /*if($request->file('sample_epub_book')){
            $file = $request->file('sample_epub_book');
            $filename3 = $lid.'_sample_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename3);
            $note->sample_epub_book = $filename3;
            $note->save();
        }   if($request->file('main_epub_book')){
            $file = $request->file('main_epub_book');
            $filename4 = $lid.'_main_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename4);
            $note->main_epub_book = $filename4;
            $note->save();
        }*/
        return redirect()->route('notes.index')->withStatus(__('Note successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(CoachingNote $note)
    {
        return view('notes.show', ['notesDetails' => $note, 'active_menu' => 'notes']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $note
     * @return \Illuminate\Http\Response
     */

    public function edit(CoachingNote $note)
    {
        $active_menu = 'notes';
        $coachingId = '';
        $subjectList = [];
        $coachingExam = [];
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $coachingList = User::where(['active' => 1, 'role' => 3, 'id' => $coachingId])->get();
            $subjectList = CoachingSubject::where('coaching_id', $coachingId)->get();
            $selectedExam = CoachingExam::where('coaching_id', $coachingId)->pluck('exam_id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        elseif(auth()->user()->role == 0){
            $coachingList = User::where(['active' => 1, 'role' => 3])->get();
            $selectedExam = Exam::where('status', 1)->pluck('id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        /*$coachingList = User::where(['active' => 1, 'role' => 2])->get();*/
        $examList = Exam::where('status', 1)->get();
        return view('notes.edit', compact('note','active_menu', 'examList', 'coachingList', 'coachingExam', 'coachingId', 'subjectList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, CoachingNote $note)
    {
        $this->validate($request, [
            'title_of_notes' => 'required',
            'subject_id' => 'required',
            'exam_id' => 'required',
            'heading' => 'required',
            'image' => 'mimes:jpeg,png,jpg|max:2048',
            'sample_note' => 'mimes:PDF,pdf',
            'main_note' => 'mimes:PDF,pdf',
            'selling_price' => 'required'
        ]);
        
		$note->title_of_notes  = $request->title_of_notes;
        $note->note_type       = $request->note_type;
        $note->exam_id         = $request->exam_id;
        $note->subject_id      = $request->subject_id;
        $note->coaching_id     = $request->coaching_id;
        $note->selling_price   = $request->selling_price;
        $note->heading 		   = $request->heading;
        $note->description 	   = $request->description;
        if($request->exam_type_book != '')
        {
            $note->exam_type = implode(', ',$request->exam_type_book);
        }
        else
        {
            $note->exam_type = ''; 
        }        

        $note->update();
        if($request->file('image')){
            $path1 = public_path()."/uploads/notes/".$note->image;
            if(is_file($path1))
            {
                unlink($path1);
            }
            $file = $request->file('image');
            $filename= $note->id.'_note_cover.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename);
            $note->image = $filename;
            $note->save();
        }
		if($request->file('sample_note')){
            $path2 = public_path()."/uploads/notes/".$note->sample_note;
            if(is_file($path2))
            {
                unlink($path2);
            }
            $file = $request->file('sample_note');
            $filename1 = $note->id.'_sample_note.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename1);
            $note->sample_note = $filename1;
            $note->save();
        }
		if($request->file('main_note')){
            $path3 = public_path()."/uploads/notes/".$note->main_note;
            if(is_file($path3))
            {
                unlink($path3);
            }
            $file = $request->file('main_note');
            $filename2 = $note->id.'_main_note.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename2);
            $note->main_note = $filename2;
            $note->save();
        }
        /*if($request->file('sample_epub_book')){
            $path4 = public_path()."/uploads/notes/".$book->sample_epub_book;
            if(is_file($path4))
            {
                unlink($path4);
            }
            $file = $request->file('sample_epub_book');
            $filename3 = $note->id.'_sample_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename3);
            $note->sample_epub_book = $filename3;
            $note->save();
        }
        if($request->file('main_epub_book')){
            $path5 = public_path()."/uploads/notes/".$book->main_epub_book;
            if(is_file($path5))
            {
                unlink($path5);
            }
            $file = $request->file('main_epub_book');
            $filename4 = $note->id.'_main_epub_book.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/notes'), $filename4);
            $note->main_epub_book = $filename4;
            $note->save();
        }*/
        return redirect()->route('notes.index')->withStatus(__('Note Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoachingNote $note)
    {
        if($note->status==1){
            $note->status=0;
            $note->save();
            return redirect()->route('notes.index')->withStatus(__('Note Successfully Inactive.'));
        }else{
            $note->status=1;
            $note->save();
            return redirect()->route('notes.index')->withStatus(__('Note Successfully Active.'));
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
                    $resp .= '<label class="form-control-label" for="exam_type">Exam Type</label><br>';
                    $resp .= '<div class="row">';
                    if(in_array('Pre', $examTypeArray)){ $i++;
                        $resp .= '<div class="col-md-4 d-flex align-items-center">';
                        $resp .= '<label class="form-control-label mr-3" for="exam_type">Pre</label>';
                        $resp .= '<label class="custom-toggle">';
                        $resp .= '<input type="checkbox" id="exam_type_pre" name="exam_type[]" value="Pre">';
                        $resp .= '<span class="custom-toggle-slider rounded-circle"></span>';
                        $resp .= '</label>';
                        $resp .= '</div>';
                    }
                    if(in_array('Main', $examTypeArray)){ $i++;
                        $resp .= '<div class="col-md-4 d-flex align-items-center">';
                        $resp .= '<label class="form-control-label mr-3" for="exam_type">Main</label>';
                        $resp .= '<label class="custom-toggle">';
                        $resp .= '<input type="checkbox" id="exam_type_main" name="exam_type[]" value="Main">';
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