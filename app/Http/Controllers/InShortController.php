<?php

namespace App\Http\Controllers;

use App\InShort;

use App\InShortCategory;

use App\CurrentAffairQuesAns;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

class InShortController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        $res = [];
        $data['title'] = 'English Content (25)';
        $data['start'] = '2022-10-02';
        $data['color'] = 'red';
        $data['textColor'] = 'white';
        array_push($res, $data);
        //$res[0] = $data;
        $data['title'] = 'Hindi Content (30)';
        $data['start'] = '2022-10-01';
        $data['color'] = 'red';
        $data['textColor'] = 'white';
        array_push($res, $data);
        $counter =  array(
                        array(
                            'title' => 'English Content (25)',
                            'start' => '2022-10-01',
                            'color' => 'red',
                            'textColor' => 'white'
                        ),
                        array(
                            'title' => 'Hindi Content (13)',
                            'start' => '2022-10-02',
                            'color' => 'blue',
                            'textColor' => 'white'
                        )
                    );
        //dd(json_encode($counter));
        //dd(json_encode($counter));
        //dd(count($res));
        //$totaldata = json_encode($counter);
        $inShortArr  = [];
        $inShortData = InShort::select('in_shorts.calender_date')->where('short_language', 'English')->groupBy('calender_date')->get();
        if($inShortData){
            foreach ($inShortData as $res) {
                $data['title'] = 'English Content ('.$this->contentCount('short_language', 'English', $res->calender_date, 'in_shorts').')';
                $data['start'] = date('Y-m-d', strtotime($res->calender_date));
                $data['color'] = 'red';
                $data['textColor'] = 'white';
                $inShortArr[] = $data;
            }
        }
        $inShortDataHindi = InShort::select('in_shorts.calender_date')->where('short_language', 'English')->groupBy('calender_date')->get();
        if($inShortDataHindi){
            foreach ($inShortDataHindi as $res) {
                $data['title'] = 'Hindi Content ('.$this->contentCount('short_language', 'Hindi', $res->calender_date, 'in_shorts').')';
                $data['start'] = date('Y-m-d', strtotime($res->calender_date));
                $data['color'] = 'blue';
                $data['textColor'] = 'white';
                $inShortArr[] = $data;
            }
        }
        $englishTest = CurrentAffairQuesAns::select('current_affairs_question_answers.calender_date')->where('question_language', 'English')->groupBy('calender_date')->get();
        if($englishTest){
            foreach ($englishTest as $res) {
                $data['title'] = 'English Test ('.$this->contentCount('question_language', 'English', $res->calender_date, 'current_affairs_question_answers').')';
                $data['start'] = date('Y-m-d', strtotime($res->calender_date));
                $data['color'] = 'green';
                $data['textColor'] = 'white';
                $inShortArr[] = $data;
            }
        }
        $hindiTest = CurrentAffairQuesAns::select('current_affairs_question_answers.calender_date')->where('question_language', 'English')->groupBy('calender_date')->get();
        if($hindiTest){
            foreach ($hindiTest as $res) {
                $data['title'] = 'Hindi Test ('.$this->contentCount('question_language', 'Hindi', $res->calender_date, 'current_affairs_question_answers').')';
                $data['start'] = date('Y-m-d', strtotime($res->calender_date));
                $data['color'] = 'orange';
                $data['textColor'] = 'white';
                $inShortArr[] = $data;
            }
        }
        $totaldata = json_encode($inShortData);
        return view('inShort.index', ['inShortList' => InShort::orderBy('id','desc')->get(), 'totaldata' => $totaldata, 'tasks' => $inShortArr, 'active_menu' => 'inShort']);
    }

    /**
     * @Function: table counter 
     **/
    public function contentCount($fieldname, $type, $date, $table){
        $rowCount = DB::table($table)->where([$fieldname => $type, 'calender_date' => $date])->count();
        return $rowCount;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('inShort.create', ['active_menu' => 'inShort']);
        }else return redirect()->route('inShort.index')->withStatus(__('No Access'));
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

            'inShort_title' => 'required'

        ]);

        $inShort = new InShort;

        $inShort->calender_date = $request->calender_date;

        $inShort->short_language = $request->short_language;
        
        $inShort->inShort_title = $request->inShort_title;
		
		$inShort->inShort_link = $request->inShort_link; 
		
		$inShort->description = $request->ndneditor;

        $inShort->save();

        $lid = $inShort->id;

		if($request->file('image')){

            $file = $request->file('image');

            $filename= $inShort->id.'_inShort.'.$file->getClientOriginalExtension();			

            $file->move(public_path('uploads/inShort'), $filename);

            $inShort->image = $filename;

            $inShort->save();
        }
        return redirect()->route('inShort.index')->withStatus(__('InShort successfully created.'));
    }

    public function updateEnglishContent(Request $request)
    {
        $this->validate($request, [

            'image' => 'image|mimes:jpeg,png,jpg|max:2048',

            'inShort_title' => 'required'

        ]);

        $id = $request->short_id;

        $inShort = InShort::where('id', $id)->first();
        
        $inShort->inShort_title = $request->inShort_title;
        
        $inShort->inShort_link = $request->inShort_link; 
        
        $inShort->description = $request->ndneditor;

        $inShort->save();

        $lid = $inShort->id;

        if($request->file('image')){

            $file = $request->file('image');

            $filename= $id.'_inShort.'.$file->getClientOriginalExtension();            

            $file->move(public_path('uploads/inShort'), $filename);

            $inShort->image = $filename;

            $inShort->save();
        }
        return redirect()->route('inShort.index')->withStatus(__('InShort successfully updated.'));
    }
    
    /**
     *  @function: destroyEnglishContent 
     */
    public function destroyEnglishContent($id){
        $response = InShort::where('id', $id)->delete();
        if ($response) {
            return back()->withStatus(__('English content remove successfully.'));
        }
        return back()->withError(__('English content not remove successfully.'));
    }

    /**
     * @Function: Create daily short
     */
    public function editHindiContent($id)
    {
        $active_menu = 'inShort';
        $activetab = 'details-hindi';
        $editContent = InShort::where('id', $id)->first();
        $dailyShortEnglishList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'English'])->get();
        $dailyShortHindiList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'Hindi'])->get();
        $quesAnsEnglishList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'English'])->get();
        $quesAnsHindiList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'Hindi'])->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $editContent->calender_date;
        return view('inShort.editHindiContent', compact('activetab', 'editContent', 'selectedData', 'dailyShortEnglishList', 'dailyShortHindiList', 'quesAnsHindiList', 'quesAnsEnglishList',  'categoryList','active_menu'));
    }

    /**
     *  updateHindiContent
     */
    public function updateHindiContent(Request $request)
    {
        $this->validate($request, [

            'image' => 'image|mimes:jpeg,png,jpg|max:2048',

            'inShort_title' => 'required'

        ]);

        $id = $request->short_id;

        $inShort = InShort::where('id', $id)->first();
        
        $inShort->inShort_title = $request->inShort_title;
        
        $inShort->inShort_link = $request->inShort_link; 
        
        $inShort->description = $request->ndneditor2;

        $inShort->save();

        $lid = $inShort->id;

        if($request->file('image')){

            $file = $request->file('image');

            $filename= $id.'_inShort.'.$file->getClientOriginalExtension();            

            $file->move(public_path('uploads/inShort'), $filename);

            $inShort->image = $filename;

            $inShort->save();
        }
        return back()->withStatus(__('Hindi content successfully updated.'));
    }

    /**
     *  @function: destroyHindiContent 
     */
    public function destroyHindiContent($id){
        $response = InShort::where('id', $id)->delete();
        if ($response) {
            return back()->withStatus(__('Hindi content remove successfully.'));
        }
        return back()->withError(__('Hindi content not remove successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $InShort
     * @return \Illuminate\Http\Response
     */

    public function edit(InShort $inShort)
    {
        $active_menu = 'inShort';
        return view('inShort.edit', compact('inShort','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, InShort $inShort)
    {
        $this->validate($request, [
            'inShort_title' => 'required'
        ]);
        $inShort->short_language = $request->short_language;  

        $inShort->inShort_title = $request->inShort_title;  

        $inShort->inShort_link = $request->inShort_link;  

		$inShort->description = $request->ndneditor;		
        
		$inShort->update();
		
        if($request->file('image')){
            $path = public_path()."/uploads/inShort/".$inShort->image;
			if(is_file($path))
			{
				unlink($path);
			}
            $file = $request->file('image');
            $filename= $inShort->id.'_inShort.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/inShort'), $filename);
            $inShort->image = $filename;
            $inShort->update();
        }
        return redirect()->route('InShort.index')->withStatus(__('InShort Successfully Updated.'));
    }

    public function destroy(InShort $inShort)
    {
        if($inShort->status==1){
            $inShort->status=0;
            $inShort->save();
            return redirect()->route('inShort.index')->withStatus(__('InShort Successfully Inactive.'));
        }else{
            $inShort->status=1;
            $inShort->save();
            return redirect()->route('inShort.index')->withStatus(__('InShort Successfully Active.'));
        }
    }

    /**
     * @Function: Create daily short
     */
    public function createShort($id)
    {
        $active_menu = 'inShort';
        $activetab = 'details-english';
        $dailyShortList = InShort::where('id', $id)->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $id;
        return view('inShort.create', compact('activetab', 'selectedData', 'dailyShortList','categoryList','active_menu'));
    }

    /**
     * @Function: Create daily short
     */
    public function currentAffairs($id)
    {
        $active_menu = 'inShort';
        $activetab = 'english-content';
        $dailyShortEnglishList = InShort::where(['calender_date'=> $id, 'short_language' => 'English'])->get();
        $dailyShortHindiList = InShort::where(['calender_date'=> $id, 'short_language' => 'Hindi'])->get();
        $quesAnsEnglishList = CurrentAffairQuesAns::where(['calender_date'=> $id, 'question_language' => 'English'])->get();
        $quesAnsHindiList = CurrentAffairQuesAns::where(['calender_date'=> $id, 'question_language' => 'Hindi'])->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $id;
        return view('inShort.currentAffairs', compact('activetab', 'selectedData', 'dailyShortEnglishList', 'dailyShortHindiList', 'quesAnsHindiList', 'quesAnsEnglishList',  'categoryList','active_menu'));
    }

    /**
     * @Function: Create daily short
     */
    public function editEnglishContent($id)
    {
        $active_menu = 'inShort';
        $activetab = 'details-english';
        $editContent = InShort::where('id', $id)->first();
        $dailyShortEnglishList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'English'])->get();
        $dailyShortHindiList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'Hindi'])->get();
        $quesAnsEnglishList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'English'])->get();
        $quesAnsHindiList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'Hindi'])->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $editContent->calender_date;
        return view('inShort.editEnglishContent', compact('activetab', 'editContent', 'selectedData', 'dailyShortEnglishList', 'dailyShortHindiList', 'quesAnsHindiList', 'quesAnsEnglishList',  'categoryList','active_menu'));
    }

    /**
     *  @function:  Store content
     */
    public function storeShort(Request $request)
    {
  /*      
        $this->validate($request, [

            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            'inShort_title' => 'required'

        ]);
        $inShort = new InShort;

        $inShort->calender_date = $request->calender_date;

        $inShort->short_language = $request->short_language;
        
        $inShort->inShort_title = $request->inShort_title;
        
        $inShort->inShort_link = $request->inShort_link; 
        
        $inShort->description = $request->ndneditor;

        $inShort->save();
*/
        $insertData['calender_date']          = $request->calender_date;
        $insertData['category_id']            = $request->category_id;
        $insertData['short_language']         = $request->short_language;
        $insertData['inShort_title']          = $request->inShort_title;
        $insertData['inShort_link']           = $request->inShort_link;
        if($request->short_language == "English"){
            $insertData['description']            = $request->ndneditor;
        }
        else
        {
            $insertData['description']            = $request->ndneditor2;            
        }
        $response = InShort::create($insertData);
        if ($response->id != '') {

            if($request->file('image')){

                $file = $request->file('image');

                $filename= $response->id.'_inShort.'.$file->getClientOriginalExtension();            

                $file->move(public_path('uploads/inShort'), $filename);

                $response->image = $filename;

                $response->save();
            }
            return back()->withStatus(__('Current affair successfully created.'));
        }
        return back()->withStatus(__('Current affair not created successfully.'));
    }

    /**
     * @function: Question Answer
     */
    public function storeQuestionAnswer(Request $request){

        $insertData['question']          = $request->question;
        $insertData['answer']            = $request->answer;
        $insertData['option_1']          = $request->option_1;
        $insertData['option_2']          = $request->option_2;
        $insertData['option_3']          = $request->option_3;
        $insertData['option_4']          = $request->option_4;
        $insertData['calender_date']     = $request->calender_date;
        $insertData['question_language'] = $request->question_language;
        
        $response = CurrentAffairQuesAns::create($insertData);
        if ($response->id != '') {
            return back()->withStatus(__('Question & answer added successfully.'));
        }
        return back()->withError(__('Question & answer not added successfully.'));
    }

    /**
     * @Function: English Test
     */
    public function editEnglishTest($id)
    {
        $active_menu = 'inShort';
        $activetab = 'test-english';
        $editContent = CurrentAffairQuesAns::where('id', $id)->first();
        $dailyShortEnglishList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'English'])->get();
        $dailyShortHindiList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'Hindi'])->get();
        $quesAnsEnglishList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'English'])->get();
        $quesAnsHindiList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'Hindi'])->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $editContent->calender_date;
        return view('inShort.editEnglishTest', compact('activetab', 'editContent', 'selectedData', 'dailyShortEnglishList', 'dailyShortHindiList', 'quesAnsHindiList', 'quesAnsEnglishList',  'categoryList','active_menu'));
    }

    /**
     *  updateEnglishTest
     */
    public function updateEnglishTest(Request $request)
    {
        $id = $request->test_id;

        $insertData['question']          = $request->question;
        $insertData['answer']            = $request->answer;
        $insertData['option_1']          = $request->option_1;
        $insertData['option_2']          = $request->option_2;
        $insertData['option_3']          = $request->option_3;
        $insertData['option_4']          = $request->option_4;
        
        $response = CurrentAffairQuesAns::where('id', $id)->update($insertData);
        if ($response != '') {
            return back()->withStatus(__('Question & answer updated successfully.'));
        }
        return back()->withError(__('Question & answer not updated successfully.'));
    }

    /**
     *  @function: destroyEnglishTest 
     */
    public function destroyEnglishTest($id){
        $response = CurrentAffairQuesAns::where('id', $id)->delete();
        if ($response) {
            return back()->withStatus(__('English test remove successfully.'));
        }
        return back()->withError(__('English test not remove successfully.'));
    }

    /**
     * @Function: Hindi Test
     */
    public function editHindiTest($id)
    {
        $active_menu = 'inShort';
        $activetab = 'test-hindi';
        $editContent = CurrentAffairQuesAns::where('id', $id)->first();
        $dailyShortEnglishList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'English'])->get();
        $dailyShortHindiList = InShort::where(['calender_date'=> $editContent->calender_date, 'short_language' => 'Hindi'])->get();
        $quesAnsEnglishList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'English'])->get();
        $quesAnsHindiList = CurrentAffairQuesAns::where(['calender_date'=> $editContent->calender_date, 'question_language' => 'Hindi'])->get();
        $categoryList = InShortCategory::where('status', 1)->get();
        $selectedData = $editContent->calender_date;
        return view('inShort.editHindiTest', compact('activetab', 'editContent', 'selectedData', 'dailyShortEnglishList', 'dailyShortHindiList', 'quesAnsHindiList', 'quesAnsEnglishList',  'categoryList','active_menu'));
    }

    /**
     *  updateHindiTest
     */
    public function updateHindiTest(Request $request)
    {
        $id = $request->test_id;

        $insertData['question']          = $request->question;
        $insertData['answer']            = $request->answer;
        $insertData['option_1']          = $request->option_1;
        $insertData['option_2']          = $request->option_2;
        $insertData['option_3']          = $request->option_3;
        $insertData['option_4']          = $request->option_4;
        
        $response = CurrentAffairQuesAns::where('id', $id)->update($insertData);
        if ($response != '') {
            return back()->withStatus(__('Question & answer updated successfully.'));
        }
        return back()->withError(__('Question & answer not updated successfully.'));
    }

    /**
     *  @function: destroyHindiTest 
     */
    public function destroyHindiTest($id){
        $response = CurrentAffairQuesAns::where('id', $id)->delete();
        if ($response) {
            return back()->withStatus(__('Hindi test remove successfully.'));
        }
        return back()->withError(__('Hindi test not remove successfully.'));
    }
}

