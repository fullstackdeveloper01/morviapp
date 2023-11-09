<?php

namespace App\Http\Controllers;

use App\Publisher;
use App\User;
use App\Book;
use App\BankDetails;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

use App\Notifications\DriverCreated;

use Illuminate\Http\UploadedFile;

use App\Helpers\Helper;

class PublisherController extends Controller
{
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        return view('publisher.index', ['publisherList' =>User::where('role',2)->orderBy('id','desc')->get(), 'active_menu' => 'Publisher']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        if(auth()->user()->hasRole('admin')){
            return view('publisher.create', ['active_menu' => 'Publisher']);
        }else return redirect()->route('publisher.index')->withStatus(__('No Access'));
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
			'email' => 'required|email|unique:users',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'bank_IFSC' => 'required'
        ]);

        $publisher = new User;

        $publisher->name 		= $request->name;
        $publisher->email  	    = $request->email;
        $publisher->phone 	    = $request->phone;
        $publisher->address 	= $request->address;
        $publisher->password 	= Hash::make($request->password);
        $publisher->role 	    = 2;

        $publisher->save();
        if($request->file('publisher_id_proof')){
            $file = $request->file('publisher_id_proof');
            $filename= $publisher->id.rand(1000, 9999).'_publisher.'.$file->getClientOriginalExtension();          
            $file->move(public_path('uploads/id-proof'), $filename);
            $publisher->image = $filename;
            $publisher->save();
        }

        if($publisher){
            $publisherBank = new BankDetails;
            $publisherBank->user_id             = $publisher->id;
            $publisherBank->bank_holder_name    = $request->bank_holder_name;
            $publisherBank->bank_name 			= $request->bank_name;
            $publisherBank->account_number 		= $request->account_number;
            $publisherBank->bank_IFSC 			= $request->bank_IFSC;
            $publisherBank->save();
        }

        $password['email']=$request->email;
        $password['password']=$request->password;
        $password['user_name']=ucfirst($request->name);
        $html = view('mail.registration_successfully',$password);

        Helper::send_email($request->email,'Welcome to National Digital Notes',$html);
        
        return redirect()->route('publisher.index')->withStatus(__('Publisher successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(User $publisher)
    {
        $bookList = Book::where('publisher_id', $publisher->id)->get();
        $bankList = BankDetails::where('user_id', $publisher->id)->first();
        return view('publisher.show', ['publisherDetails' => $publisher, 'bookList' => $bookList, 'bankList' => $bankList, 'active_menu' => 'Publisher']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $Publisher
     * @return \Illuminate\Http\Response
     */

    public function edit(User $publisher)
    {
        $active_menu = 'Publisher';
        $bankList = BankDetails::where('user_id', $publisher->id)->first();
        return view('publisher.edit', compact('publisher','bankList','active_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, User $publisher)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'bank_IFSC' => 'required'
        ]);
        
		$publisher->name 		 = $request->name;
        $publisher->phone 	     = $request->phone;
        $publisher->address 	 = $request->address;
        $publisher->update();
        if($request->file('publisher_id_proof')){
            $path = public_path()."/uploads/id-proof/".$publisher->image;
            if(is_file($path)){
                unlink($path);
            }
            $file = $request->file('publisher_id_proof');
            $filename= $publisher->id.rand(1000, 9999).'_publisher.'.$file->getClientOriginalExtension();          
            $file->move(public_path('uploads/id-proof'), $filename);
            $publisher->image = $filename;
            $publisher->update();
        }

        $publisherBank = BankDetails::where('user_id', $publisher->id)->first();
        $publisherBank->bank_holder_name    = $request->bank_holder_name;
        $publisherBank->bank_name           = $request->bank_name;
        $publisherBank->account_number      = $request->account_number;
        $publisherBank->bank_IFSC           = $request->bank_IFSC;
        $publisherBank->save();

        return redirect()->route('publisher.index')->withStatus(__('Publisher Successfully Updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $publisher)
    {
        if($publisher->active==1){
            $publisher->active=0;
            $publisher->save();
            return redirect()->route('publisher.index')->withStatus(__('Publisher Successfully Inactive.'));
        }else{
            $publisher->active=1;
            $publisher->save();
            return redirect()->route('publisher.index')->withStatus(__('Publisher Successfully Active.'));
        }
    }
}