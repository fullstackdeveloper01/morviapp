<?php

namespace App\Http\Controllers;

use App\City;
use App\User;
use App\Pages;
use App\State;
use App\Category;
use App\UserMedia;
use App\TableColumn;
use App\UserCategory;
use App\ProductImage;
use App\CategoryTable;
use App\Advertisement;
use App\UserSellProduc;
use App\UserAdvertisment;
use App\UserProfessional;
use App\ProfessionalTableData;

use App\Http\Requests\UserRequest;
use App\Notifications\CloseAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        return view('users.index', ['users' => User::where('role', '>', 0)->orderBy('id', 'desc')->get()]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request->merge(['password' => Hash::make($request->get('password'))])->all());

        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update(
            $request->merge(['password' => Hash::make($request->get('password'))])
                ->except([$request->get('password') ? '' : 'password']
        ));

        return redirect()->route('user.index')->withStatus(__('User successfully updated.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $driver
     * @return \Illuminate\Http\Response
     */

    public function show(User $user)
    {
        $userData = User::where('id', $user->id)->first();
        if($user->role == 1){
            return view('users.normal', ['userData' => $userData]);
        }
        else{            
            $professionalList = UserProfessional::where('user_professionals.user_id', $userData->id)
                                ->join('categoriess','categoriess.id','=', 'user_professionals.category_id')
                                ->get();
            return view('users.photographer', ['userData' => $userData, 'professionalList' => $professionalList]);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        if($user->active==1){
            $user->active=0;
            $user->save();
            return redirect()->route('user.index')->withStatus(__('User inactive successfully!'));
        }else{
            $user->active=1;
            $user->save();
            return redirect()->route('user.index')->withStatus(__('User active successfully!'));
        }
    }

    public function checkPushNotificationId(UserRequest $request)
    {
        return response()->json([
            'userId' => $request->userId,
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function stripeReAuth(Request $request)
    {
        dd($request->all());
    }

    public function stripeOnNoardResponse(Request $request)
    {
        dd($request->all());
    }

    /**
     * Function@ sendEmailToUser
     */
    public function sendEmailToUser(Request $request){
        $email      =   $request->emailsend_to;
        $subject    =   $request->emailsend_subject;
        $message    =   $request->emailsend_message;
        if($email != '' && $subject != '' && $message != ''){
            Helper::send_email($email, $subject, $message);
            return redirect()->route('user.index')->withStatus(__('Mail send successfully!'));
        }
        else{
            return redirect()->route('user.index')->withError(__('Email not send successfully, Please try again!'));
        }
        
    }
}
