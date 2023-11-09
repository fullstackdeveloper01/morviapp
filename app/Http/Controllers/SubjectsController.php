<?php

namespace App\Http\Controllers;
use App\User;
use App\Exam;
use App\Coaching;
use App\CoachingExam;
use App\CoachingSubject;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\DriverCreated;
use Illuminate\Http\UploadedFile;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjectList = '';
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $subjectList = CoachingSubject::where(['coaching_id' => $coachingId])->orderBy('id','desc')->get();            
        }
        else{
            $subjectList = CoachingSubject::orderBy('id','desc')->get();
        }
        return view('subjects.index', ['subjectList' => $subjectList, 'active_menu' => 'subjects']);
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
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $coachingList = User::where(['role' => 3, 'id' => $coachingId])->get();
            $selectedExam = CoachingExam::where('coaching_id', $coachingId)->pluck('exam_id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        else{
            $coachingList = User::where(['role' => 3])->get();
            $selectedExam = Exam::where('status', 1)->pluck('id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        return view('subjects.create', ['active_menu' => 'subjects', 'examList' => $examList, 'coachingExam' => $coachingExam, 'coachingList' => $coachingList, 'coachingId' => $coachingId]);
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
            'subject_name' => 'required',
            'exam_id' => 'required',
            'icon' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $subject = new CoachingSubject;

        $subject->subject_name 	   = $request->subject_name;
        $subject->exam_id         = $request->exam_id;
        $subject->coaching_id     = $request->coaching_id;
        $subject->description 	   = $request->description;
        if($request->exam_type_book != '')
        {
            $subject->exam_type = implode(', ',$request->exam_type_book);
        }
        else
        {
            $subject->exam_type = ''; 
        }

        $subject->save();

        $lid = $subject->id;
		if($request->file('icon')){
            $file = $request->file('icon');
            $filename= $lid.'_subject.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/subject'), $filename);
            $subject->icon = $filename;
            $subject->save();
        }
        return redirect()->route('subjects.index')->withStatus(__('Subject successfully added.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(CoachingSubject $subject)
    {
        return view('subjects.show', ['subjectDetails' => $subject, 'active_menu' => 'subjects']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $subject
     * @return \Illuminate\Http\Response
     */

    public function edit(CoachingSubject $subject)
    {
        $active_menu = 'subjects';
        $coachingId = '';
        $coachingExam = [];
        if(auth()->user()->role == 3){
            $coachingId = auth()->user()->id;
            $coachingList = User::where(['role' => 3, 'id' => $coachingId])->get();
            $selectedExam = CoachingExam::where('coaching_id', $coachingId)->pluck('exam_id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        else{
            $coachingList = User::where(['role' => 3])->get();
            $selectedExam = Exam::where('status', 1)->pluck('id')->implode(',');
            if($selectedExam){
                $coachingExam = explode(",", $selectedExam);
            }
        }
        $examList = Exam::where('status', 1)->get();
        return view('subjects.edit', compact('subject', 'active_menu', 'examList', 'coachingExam', 'coachingList', 'coachingId'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, CoachingSubject $subject)
    {
        $this->validate($request, [
            'subject_name' => 'required',
            'exam_id' => 'required',
            'icon' => 'mimes:jpeg,png,jpg|max:2048',
        ]);
        
		$subject->subject_name 	   = $request->subject_name;
        $subject->exam_id         = $request->exam_id;
        $subject->coaching_id    = $request->coaching_id;
        $subject->description 	   = $request->description;
        if($request->exam_type_book != '')
        {
            $subject->exam_type = implode(', ',$request->exam_type_book);
        }
        else
        {
            $subject->exam_type = ''; 
        }        

        $subject->update();
        if($request->file('icon')){
            $path1 = public_path()."/uploads/subject/".$subject->icon;
            if(is_file($path1))
            {
                unlink($path1);
            }
            $file = $request->file('icon');
            $filename= $subject->id.'_subject.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/subject'), $filename);
            $subject->icon = $filename;
            $subject->save();
        }
        return redirect()->route('subjects.index')->withStatus(__('Subject Successfully Updated.'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoachingSubject $subject)
    {
        if($subject->status==1){
            $subject->status=0;
            $subject->save();
            return redirect()->route('subjects.index')->withStatus(__('Subject Successfully Inactive.'));
        }else{
            $subject->status=1;
            $subject->save();
            return redirect()->route('subjects.index')->withStatus(__('Subject Successfully Active.'));
        }
    }
}