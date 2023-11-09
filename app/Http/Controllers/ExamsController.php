<?php

namespace App\Http\Controllers;

use App\User;
use App\Exam;
use App\Book;
use App\Coaching;
use App\ExamDate;
use App\CoachingNote;
use App\ExamCoaching;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class ExamsController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('exams.index', ['examslist' =>Exam::orderBy('id','desc')->get(), 'active_menu' => 'exams']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('exams.create', ['active_menu' => 'exams']);
        }else return redirect()->route('exams.index')->withStatus(__('No Access'));
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

            'icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'name' => 'required'

        ]);

        $exams = new Exam;

        $exams->name = $request->name;

        if($request->exam_type != '')
        {
            $exams->exam_type = implode(',',$request->exam_type);
        }
        else
        {
            $exams->exam_type = '';            
        }

        $exams->description = $request->ndneditor;

        $exams->save();

        $lid = $exams->id;

		if($request->file('icon')){

            $file = $request->file('icon');

            $filename= $exams->id.'_exams.'.$file->getClientOriginalExtension();			

            $file->move(public_path('uploads/exams'), $filename);

            $exams->icon = $filename;

            $exams->save();
        }
        return redirect()->route('exams.index')->withStatus(__('Exams successfully created.'));
    }

    /**

     * Display the specified resource.

     *

     * @param  \App\Driver  $driver

     * @return \Illuminate\Http\Response

     */

    public function show(Exam $exam)
    {
        $bid = [];
        $cid = [];
        $examCoaching = ExamCoaching::where('exam_id', $exam->id)->get();
        if(count($examCoaching) > 0)
        {
            foreach($examCoaching as $ex)
            {
                array_push($cid, $ex->coaching_id);
            }
        }
        $coachingList = User::whereNotIn('id', $cid)->where('role', 3)->get();
        $selectedCoaching = ExamCoaching::select('users.*', 'exams_coaching.id as eid')
                            ->join('users', 'users.id', '=', 'exams_coaching.coaching_id')
                            ->where('exams_coaching.exam_id', $exam->id)
                            ->get()->toarray();
        $bookList = Book::where('exam_id', $exam->id)->get();
        $notesList = CoachingNote::where('exam_id', $exam->id)->get();
        $examDateList = ExamDate::where('exam_id', $exam->id)->get();
        $activetab = 'basic-details';
        return view('exams.show', ['examDetails' => $exam, 'coachingList' => $coachingList, 'selectedCoaching' => $selectedCoaching, 'bookList' => $bookList, 'notesList' => $notesList, 'examDateList' => $examDateList, 'active_menu' => 'exams', 'activetab' => $activetab]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $exams
     * @return \Illuminate\Http\Response
     */

    public function edit(Exam $exam)
    {
        $active_menu = 'exams';
        return view('exams.edit', compact('exam','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Exam $exam)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $exam->name = $request->name;
        if($request->exam_type != '')
        {
            $exam->exam_type = implode(', ',$request->exam_type);
        }
        else
        {
            $exam->exam_type = '';            
        }
        $exam->description = $request->ndneditor;
        $exam->update();
        if($request->file('icon')){
            $path = public_path()."/uploads/exams/".$exam->icon;
            unlink($path);
            $file = $request->file('icon');
            $filename= $exam->id.'_exams.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/exams'), $filename);
            $exam->icon = $filename;
            $exam->update();
        }
        return redirect()->route('exams.index')->withStatus(__('Exams Successfully Updated.'));
    }

    /**
     * Add Coaching 
     * 
    */
    public function addCoaching (Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required',
            'coaching_id' => 'required'
        ]);
        $exam_id = $request->exam_id;
        $coachingIds = $request->coaching_id;
        $lid = 1;
        if($exam_id != '')
        {
            foreach($coachingIds as $rrr){
                if($rrr != '')
                {
                    $examCoaching = new ExamCoaching;
                    $examCoaching->exam_id = $request->exam_id;
                    $examCoaching->coaching_id = $rrr;
                    $examCoaching->save();
                    $lid++;
                }
            }
        }

        if($lid > 1)
        {
            return back()->withStatus(__('Coaching added successfully.'));
        }
        else{
            return back()->withError(__('Coaching not added successfully.'));
        }
    }
    /**
     * Add Exam date 
     * 
    */
    public function addExamDate (Request $request)
    {
        $this->validate($request, [
            'exam_id' => 'required',
            'exam_title' => 'required',
            'exam_date' => 'required'
        ]);
        $exam_id = $request->exam_id;
        $exam_date = date('Y-m-d', strtotime($request->exam_date));
        $lid = 1;
        if($exam_id != '')
        {
            $exExamDate = ExamDate::where(['exam_id' => $exam_id, 'exam_date' => $exam_date])->count();
            if($exExamDate == 0)
            {
                $examDate = new ExamDate;
                $examDate->exam_id = $request->exam_id;
                $examDate->exam_title = $request->exam_title;
                $examDate->exam_date = $exam_date;
                $examDate->save();
                return back()->withStatus(__('Exam Date added successfully.'));
            }
            else
            {
                return back()->withError(__('Exam Date is already added for this exam.'));
            }
        }
        else{
            return back()->withError(__('Exam Date not added successfully.'));        
        }
    }
    
    /**
     * Add Overview 
     * 
    */
    public function addOverview (Request $request)
    {
        $examOverview = Exam::where('id', $request->exam_id)->first();
        $lid = 1;
        if(!empty($examOverview))
        {
            $examOverview->overview = $request->ndneditor;
            $examOverview->save();
            $lid++;
        }
        if($lid > 1)
        {
            return back()->withStatus(__('Overview added successfully.'));
        }
        else{
            return back()->withError(__('Overview not added successfully.'));
        }
    }

    /**
     * Add Syllabus 
     * 
    */
    public function addSyllabus (Request $request)
    {
        $examSyllabus = Exam::where('id', $request->exam_id)->first();

        $lid = 1;
        if(!empty($examSyllabus))
        {
            $examSyllabus->syllabus = $request->ndneditor2;
            $examSyllabus->save();
            $lid++;
        }
        if($lid > 1)
        {
            return back()->withStatus(__('Syllabus added successfully.'));
        }
        else{
            return back()->withError(__('Syllabus not added successfully.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        if($exam->status==1){
            $exam->status=0;
            $exam->save();
            return redirect()->route('exams.index')->withStatus(__('Exams Successfully Inactive.'));
        }else{
            $exam->status=1;
            $exam->save();
            return redirect()->route('exams.index')->withStatus(__('Exams Successfully Active.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function removeCoaching($id)
    {
        if($id)
        {
            ExamCoaching::where('id', $id)->delete();
            return back()->withStatus(__('Coaching remove successfully.'));
        }
        else{
            return back()->withError(__('Coaching not remove successfully.'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function removeExamDate($id)
    {
        if($id)
        {
            ExamDate::where('id', $id)->delete();
            return back()->withStatus(__('Exam date remove successfully.'));
        }
        else{
            return back()->withError(__('Exam date not remove successfully.'));
        }
    }
}