<?php

namespace App\Http\Controllers;

use App\Exam;

use App\User;

use App\Coaching;

use App\BankDetails;

use App\CoachingNote;

use App\CoachingExam;

use App\Advertisement;

use App\CoachingSubject;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

use App\Helpers\Helper;

class CoachingController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('coaching.index', ['coachingList' => User::where('role',3)->orderBy('id','desc')->get(), 'active_menu' => 'coaching']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('coaching.create', ['active_menu' => 'coaching']);
        }else return redirect()->route('coaching.index')->withStatus(__('No Access'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
			'email' => 'required|email|unique:users',
            'name' => 'required',
            'phone' => 'required|unique:users',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'bank_IFSC' => 'required'
        ]);

        $coaching = new User;

        $coaching->name 	        = $request->name;
        $coaching->phone 	        = $request->phone;
        $coaching->description 		= $request->ndneditor;
        $coaching->email 			= $request->email;
        $coaching->password         = Hash::make($request->password);
        $coaching->role             = 3;
        $coaching->save();

        $lid = $coaching->id;
		if($request->file('image')){
            $file = $request->file('image');
            $filename= $coaching->id.rand(100, 999).'_coaching.'.$file->getClientOriginalExtension();			
            $file->move(public_path('uploads/coaching'), $filename);
            $coaching->image = $filename;
            $coaching->save();
        }

        if($coaching){
            $publisherBank = new BankDetails;
            $publisherBank->user_id             = $coaching->id;
            $publisherBank->bank_holder_name    = $request->bank_holder_name;
            $publisherBank->bank_name           = $request->bank_name;
            $publisherBank->account_number      = $request->account_number;
            $publisherBank->bank_IFSC           = $request->bank_IFSC;
            $publisherBank->save();
        }

        $saveData['email']=$request->email;
        $saveData['password']=$request->password;
        $saveData['user_name']=ucfirst($request->name);
        $html= view('mail.registration_successfully',$saveData);

        Helper::send_email($request->email,'Welcome to National Digital Notes',$html);

        return redirect()->route('coaching.index')->withStatus(__('Coaching successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(User $coaching)
    {
        $coachingExam = [];
        $selectedExam = CoachingExam::where('coaching_id', $coaching->id)->pluck('exam_id')->implode(',');
        if($selectedExam){
            $coachingExam = explode(",", $selectedExam);
        }
        $examList = Exam::where('status', 1)->orderBy('id', 'desc')->get();
        $bankList = BankDetails::where('user_id', $coaching->id)->first();
        $notesList = CoachingNote::where(['coaching_id' => $coaching->id])->orderBy('id','desc')->get();
        $advertisementList = Advertisement::where('coaching_id', $coaching->id)->orderBy('id', 'desc')->get();
        $subjectList = CoachingSubject::where('coaching_id', $coaching->id)->orderBy('id', 'desc')->get();
        return view('coaching.show', ['coachingDetails' => $coaching, 'examList' => $examList, 'bankList' => $bankList, 'notesList' => $notesList, 'advertisementList' => $advertisementList, 'subjectList' => $subjectList, 'coachingExam' => $coachingExam, 'selectedExam' => $selectedExam, 'active_menu' => 'coaching']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $coaching
     * @return \Illuminate\Http\Response
     */

    public function edit(User $coaching)
    {
        $active_menu = 'coaching';
        $bankList = BankDetails::where('user_id', $coaching->id)->first();
        return view('coaching.edit', compact('coaching','bankList','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, User $coaching)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'bank_IFSC' => 'required'
        ]);
        
		$coaching->name 	        = $request->name;
        $coaching->phone 	        = $request->phone;
        $coaching->description 		= $request->ndneditor;

        $coaching->update();
        if($request->file('image')){
            $path = public_path()."/uploads/coaching/".$coaching->image;
            if(is_file($path)){
                unlink($path);
            }
            $file = $request->file('image');
            $filename= $coaching->id.rand(100, 999).'_coaching.'.$file->getClientOriginalExtension();			
            $file->move(public_path('uploads/coaching'), $filename);
            $coaching->image = $filename;
            $coaching->update();
        }

        $publisherBank = BankDetails::where('user_id', $coaching->id)->first();
        $publisherBank->bank_holder_name    = $request->bank_holder_name;
        $publisherBank->bank_name           = $request->bank_name;
        $publisherBank->account_number      = $request->account_number;
        $publisherBank->bank_IFSC           = $request->bank_IFSC;
        $publisherBank->save();
        return redirect()->route('coaching.index')->withStatus(__('Coaching Successfully Updated.'));
    }

    /**
     * Add Coaching 
     * 
    */
    public function addSubject(Request $request)
    {
        $this->validate($request, [

            'icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'subject_name' => 'required'

        ]);

        $lid = 0;

        $exams = new CoachingSubject;

        $exams->coaching_id = $request->coaching_id;

        $exams->exam_id = $request->exam_id;

        $exams->subject_name = $request->subject_name;

        if($request->exam_type_book != '')
        {
            $exams->exam_type = implode(',',$request->exam_type_book);
        }
        else
        {
            $exams->exam_type = '';            
        }

        $exams->description = $request->description;

        $exams->save();

        $lid = $exams->id;

        if($request->file('icon')){

            $file = $request->file('icon');

            $filename= $exams->id.'_subject.'.$file->getClientOriginalExtension();            

            $file->move(public_path('uploads/subject'), $filename);

            $exams->icon = $filename;

            $exams->save();
        }
        if($lid > 0)
        {
            return back()->withStatus(__('Subject added successfully.'));
        }
        else{
            return back()->withError(__('Subject not added successfully.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $coaching)
    {
        if($coaching->active==1){
            $coaching->active=0;
            $coaching->save();
            return redirect()->route('coaching.index')->withStatus(__('Coaching Successfully Inactive.'));
        }else{
            $coaching->active=1;
            $coaching->save();
            return redirect()->route('coaching.index')->withStatus(__('Coaching Successfully Active.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function removeSubject($id)
    {
        if($id)
        {
            CoachingSubject::where('id', $id)->delete();
            return back()->withStatus(__('Subject remove successfully.'));
        }
        else{
            return back()->withError(__('Subject not remove successfully.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function coachingExamUpdate($eid, $cid, $status)
    {
        if($status == 1){
            $coachingExam = new CoachingExam;
            $coachingExam->coaching_id  = $cid;
            $coachingExam->exam_id      = $eid;
            $coachingExam->status       = 1;
            $coachingExam->save();
        }
        else{
            CoachingExam::where(['coaching_id' => $cid, 'exam_id' => $eid])->delete();
        }

        return true;
    }
}