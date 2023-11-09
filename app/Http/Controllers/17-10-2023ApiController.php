<?php

namespace App\Http\Controllers;

use JWTAuth;
use Image;

use App\City;
use App\User;
use App\Brand;
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
use App\Post;
use App\FollowUnFollow;
use App\Comment;
use App\CommentLikeDislike;
use Carbon\Carbon;
use Illuminate\Support\Str;
use auth;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Exceptions\JWTException;

use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use App\Helpers\Helper;


class ApiController extends Controller
{
    protected $user;
    public function __construct()
    {   
        date_default_timezone_set('Asia/Kolkata');
    }
    public function getDateInFormat($data){
        $dt = '';
        $date = Carbon::parse($data['post_created_at']);
        if ($date->isToday())
         $dt = 'Today';
        elseif($date->isYesterday())
          $dt =  'Yesterday';
        else
            $dt = $date->diffForHumans();
        return $dt;
    }
    public function isFav($user_id, $follower_id, $post_id,$type){
     try{
        if($type == 0){
           $data = DB::table('follow_unfollows')->where(['follower_id' => $follower_id,'user_id' => $user_id , 'type' => $type])->count();  
        }
        else{
              $data = DB::table('follow_unfollows')->where(['follower_id' => $follower_id , 'user_id' => $user_id, 'post_id' => $post_id,'type' => $type,])->count();
        }
     
      return $data;

     }catch(Exception $ex){
        return $ex->getMessage();
     }
    }
    public function getUserLocation($id){
        $location = '--';
        try{
           $locationData = DB::table('master_cities')->select('city_name')->where('id',$id)->first();
           if(!is_null($locationData))
           $location = $locationData->city_name;
        }catch(Exception $ex){
          // return $ex->getMessage();  
        }
         return $location;
    }
    /**Registration api */
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'phone', 'password','place', 'role','device_id');
        $validator = Validator::make($data, [
            'name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:40',
            'email' => 'required|email|unique:users|max:60',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'place' => 'required',
            'phone' => 'required|unique:users',
            'device_id' => 'required'

        ],[
            'email.unique' => "This email is already registered",
            'phone.unique' => "This phone is already registered"
        ]);
        /*if($request->password != $request->re_password)
        {
            return response()->json(['success'=>false,'message' => 'Password and re password should be same'], 200);
        }*/
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        
        $otp = rand(1000, 9999);
        //$otp = 1234;
        $insertData['name']=$request->name;
        $insertData['email']=$request->email;
        $insertData['password']=Hash::make($request->password);
        $insertData['bc_id']=md5($request->password);
        $insertData['role']= $request->role;
        $insertData['phone']=$request->phone;
        $insertData['place']=$request->place;
        $insertData['otp'] = $otp;
        $insertData['device_id'] = $request->device_id;
        $user = User::create($insertData);
        $this->smsSendSignUp($otp, $request->phone);
        if($user)
        {
            $resp['id'] = $user->id;
            $resp['name'] = $user->name;
            $resp['email'] = $user->email;
            $resp['phone'] = $user->phone;
            $resp['place'] = $user->place;
            $resp['role'] = $user->role;
            $resp['otp'] = $user->otp;
            return response()->json([
                    'success' => true,
                    'message' => 'Registration successfully',
                    'data' => $resp
                ], Response::HTTP_OK);
        }
        else
        {
            return response()->json([
                    'success' => false,
                    'message' => 'Data are not stored',
                ], 400);
        }
    }

    public function register_old(Request $request)
    {
        if($request->login_type == 'apple' || $request->login_type == 'google')
        {
            $data = $request->only('login_type','id');
            $validator = Validator::make($data, [
                'id' => 'required',
                'login_type' => 'required|string'
            ]);
        }
        else
        {
            $data = $request->only('name', 'email', 'phone', 'password', 're_password','login_type');
            $validator = Validator::make($data, [
                'name' => 'required|string|min:3|max:40',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'login_type' => 'required',
                're_password' => 'required',
                'phone' => 'required|unique:users'
            ],[
                'email.unique' => "This email is already registered",
                'phone.unique' => "This phone is already registered"
            ]);
            if($request->password != $request->re_password)
            {
                return response()->json(['status'=>false,'message' => 'Password and re password should be same'], 200);
            }
        }
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
        }
        $message = 'User Created Successfully';
        if ($data['login_type']=="app") {            
            $insertData['name']=$request->name;
            $insertData['email']=$request->email;
            $insertData['password']=Hash::make($request->password);
            $insertData['role']=1;
            $insertData['phone']=$request->phone;
            $user = User::create($insertData);
            if($user)
            {
                $resp['id'] = $user->id;
                $resp['name'] = $user->name;
                $resp['email'] = $user->email;
                $resp['phone'] = $user->phone;
                return response()->json([
                        'success' => true,
                        'message' => 'Registration successfully',
                        'data' => $resp
                    ], Response::HTTP_OK);
            }
            else
            {
                return response()->json([
                        'success' => false,
                        'message' => 'Data are not stored',
                    ], 500);
            }
        }elseif ($data['login_type']=="google") {
            $validator = Validator::make($data, [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
            }

            $previousData = DB::table('users_deleted')->orderBy('id','desc')->where('google_id', $request->id)->first();
            if(!empty($previousData))
            {
                if($previousData->name != '' && $previousData->email != '')
                {
                    $insertData['name'] = $previousData->name;
                    $insertData['email'] = $previousData->email;
                }
            }
            else
            {
                $insertData['name']=$request->name;
                $insertData['email']=$request->email;
            }

            $insertData['google_id'] = $request->id;
            $insertData['password'] = Hash::make('immersive1');
            $insertData['role']=1;
            $error_msg = 'Registration are not created successfully';
            $exUser = User::where(['google_id' => $request->id])->first();
            //$exUser = User::where(['email'=> $insertData['email'], 'google_id' => $request->id])->first();
            if(!empty($exUser))
            {
                $user = $exUser;
                $message = 'User Login Successfully';
                $error_msg = 'User Login Successfully';
            }
            else
            {
                $exUser_ = User::where('email', $insertData['email'])->count();
                if($exUser_ > 0)
                {
                     return response()->json([
        
                            'success' => false,
        
                            'message' => "This email is already registered",
        
                        ], 500);
                }
                else
                {
                    $user = User::create($insertData);
                    $error_msg = 'Registration successfully';
                }
            }
            if($user)
            {
                $resp['id'] = $user->id;
                $resp['name'] = $user->name;
                $resp['email'] = $user->email;
                $resp['phone'] = $user->phone;
                return response()->json([
                        'success' => true,
                        'message' => $error_msg,
                        'data' => $resp
                    ], Response::HTTP_OK);
            }
            else
            {
                return response()->json([
                        'success' => false,
                        'message' => $error_msg,
                    ], 500);
            }
        }elseif ($data['login_type']=="apple") {
            $validator = Validator::make($data, [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
            }

            $previousData = DB::table('users_deleted')->orderBy('id','desc')->where('apple_id', $request->id)->first();
            if(!empty($previousData))
            {
                if($previousData->name != '' && $previousData->email != '')
                {
                    $insertData['name'] = $previousData->name;
                    $insertData['email'] = $previousData->email;
                }
            }
            else
            {
                $nameArray = explode('@', $request->email);
                if($request->name == '')
                {
                    $insertData['name'] = $nameArray[0];
                }
                else
                {
                    $insertData['name']=$request->name;
                }
                $insertData['email']=$request->email;
            }

            $insertData['apple_id'] = $request->id;
            $insertData['password'] = Hash::make('immersive2');
            $insertData['role']=1;

            $exUser = User::where(['apple_id' => $request->id])->first();
            //$exUser = User::where(['email' => $insertData['email'], 'apple_id' => $request->id])->first();
            if(!empty($exUser))
            {
                $user = $exUser;
                $message = 'User Login Successfully';
            }
            else
            {
                $exUser = User::where('email', $insertData['email'])->count();
                if($exUser > 0)
                {
                     return response()->json([
        
                            'success' => false,
        
                            'message' => "This email is already registered",
        
                        ], 500);
                }
                else
                {
                    $user = User::create($insertData);
                }
            }
            if($user)
            {
                $resp['id'] = $user->id;
                $resp['name'] = $user->name;
                $resp['email'] = $user->email;
                $resp['phone'] = $user->phone;
                return response()->json([
                        'success' => true,
                        'message' => 'Registration successfully',
                        'data' => $resp
                    ], Response::HTTP_OK);
            }
            else
            {
                return response()->json([
                        'success' => false,
                        'message' => 'Data are not stored',
                    ], 500);
            }
        }
    }

    /** Login Api */    
    public function login(Request $request)
    {
        $data = $request->only('email', 'password','device_id');
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
            'device_id' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $password = md5($request->password);
        $response = User::where(['email' => $request->email, 'bc_id' => $password])->first();
        if(!empty($response)){
            if($response->active == 1){
                $message = 'Login successfully';
                $resp['id'] = $response->id;
                $resp['name'] = $response->name;
                $resp['email'] = $response->email;
                $resp['phone'] = $response->phone;
                $resp['place'] = $response->place;
                $resp['role'] = $response->role;
                $resp['device_id'] = $request->device_id;
                $profile_img = 'uploads/user.png';
                $path1 = public_path()."/uploads/profile/".$response->image;
                if(is_file($path1)){
                    $profile_img = "uploads/profile/".$response->image;
                }
                $resp['image'] = $profile_img;
                $response->device_id = $request->device_id;
                $response->save();
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data'  => $resp
                ], 200);
            }
            else{
                $message = 'Account is inactive.';
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data'  => array()
                ], 400);
            }
        }
        else{
            $message = 'Login credentials are invalid.';
            return response()->json([
                'success' => false,
                'message' => $message,
                'data'  => array()
            ], 400);
        }
    }

    /**
     * @function: userProfile 
    **/
    public function userProfile(Request $request){
        $validator = Validator::make($request->only('user_id','document_type','attached_document'), [
            'user_id' => 'required',
            'attached_document'=>'mimes:jpeg,png,jpg,pdf',
            'document_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {
            $document = 'uploads/no-image.png';
            if($request->file('attached_document')){
                if($result->attached_document != ''){
                    $path = public_path()."/uploads/document/".$result->attached_document;
                    unlink($path);
                }
                $file = $request->file('attached_document');
                $filename= $result->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                $file->move(public_path('uploads/document'), $filename);
                $result->attached_document = $filename;
                $result->update();
                $document = 'uploads/document/'.$filename;
            }

            $result->document_type = $request->document_type;
            $result->save();

            $response['id'] = $result->id;
            $response['name'] = $result->name;
            $response['email'] = $result->email;
            $response['phone'] = $result->phone;
            $response['place'] = $result->city->city_name;
            $response['document_type'] = $result->document_type;
            $response['document'] = $document;
            return response()->json([
                'success'  => true,
                'message'  => 'Profile update successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: userProfile 
    **/
    public function getUserProfile(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 1])->first();
        if (!empty($result)) {
            $document = 'uploads/no-image.png';
            $path = public_path()."/uploads/document/".$result->attached_document;
            if(is_file($path)){
                $document = "uploads/document/".$result->attached_document;
            }
            $profile_img = 'uploads/user.png';
            $path1 = public_path()."/uploads/profile/".$result->image;
            if(is_file($path1)){
                $profile_img = "uploads/profile/".$result->image;
            }

            $response['id'] = $result->id;
            $response['name'] = $result->name;
            $response['email'] = $result->email;
            $response['phone'] = $result->phone;
            $response['place'] = $result->city->city_name;
            $response['document_type'] = $result->document_type;
            $response['facebook_url'] = (is_null($result->facebook_url)?"":$result->facebook_url);
            $response['youtube_url'] = (is_null($result->youtube_url)?"":$result->youtube_url);
            $response['instgram_url'] = (is_null($result->instgram_url)?"":$result->instgram_url);
            $response['twitter_url'] = (is_null($result->twitter_url)?"":$result->twitter_url);
            $response['linkedin_url'] = (is_null($result->linkedin_url)?"":$result->linkedin_url); 
            $response['document'] = $document;
            $response['image'] = $profile_img;
            return response()->json([
                'success'  => true,
                'message'  => 'User profile!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: userProfile 
    **/
    public function getPhotographerProfile(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 2])->first();
        if (!empty($result)) {
            $document = 'uploads/no-image.png';
            $path = public_path()."/uploads/document/".$result->attached_document;
            if(is_file($path)){
                $document = "uploads/document/".$result->attached_document;
            }
            $profile_img = 'uploads/user.png';
            $path1 = public_path()."/uploads/profile/".$result->image;
            if(is_file($path1)){
                $profile_img = "uploads/profile/".$result->image;
            }

            $response['id'] = $result->id;
            $response['name'] = $result->name;
            $response['email'] = $result->email;
            $response['phone'] = $result->phone;
            $response['place'] = $result->city->city_name;
            $response['address'] = $result->address;
            $response['business_name'] = $result->business_name;
            $response['about_business'] = $result->about_business;
            $response['experience'] = $result->experience;
            $response['document_type'] = $result->document_type;
            $response['facebook_url'] = (is_null($result->facebook_url)?"":$result->facebook_url);
            $response['youtube_url'] = (is_null($result->youtube_url)?"":$result->youtube_url);
            $response['instgram_url'] = (is_null($result->instgram_url)?"":$result->instgram_url);
            $response['twitter_url'] = (is_null($result->twitter_url)?"":$result->twitter_url);
            $response['linkedin_url'] = (is_null($result->linkedin_url)?"":$result->linkedin_url);            
            $response['document'] = $document;
            $response['image'] = $profile_img;
            $response['follower_count'] = $this->getCountOfFollowerAndFollowing($type = 1 , $result->id);
            $response['following_count'] = $this->getCountOfFollowerAndFollowing($type = 2 , $result->id);
            $catresp = [];
            $categoryList = UserCategory::select('id', 'category_name')->where('user_id', $request->user_id)->get();
            if(count($categoryList) > 0){
                $catresp = $categoryList->toarray();
            }
            return response()->json([
                'success'           => true,
                'message'           => 'Photographer profile!',
                'data'              =>  $response,
                'category_list'     =>  $catresp,
                'status'            => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Photographer profile 
    **/
    public function photographerProfile(Request $request){
        $validator = Validator::make($request->only('user_id','document_type','attached_document', 'business_name', 'about_business', 'experience', 'address'), [
            'user_id' => 'required',
            'business_name' => 'required',
            'experience' => 'required',
            'attached_document'=>'mimes:jpeg,png,jpg,pdf',
            'document_type' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {
            $document = 'uploads/no-image.png';
            if($request->file('attached_document')){
                if($result->attached_document != ''){
                    $path = public_path()."/uploads/document/".$result->attached_document;
                    unlink($path);
                }
                $file = $request->file('attached_document');
                $filename= $result->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                $file->move(public_path('uploads/document'), $filename);
                $result->attached_document = $filename;
                $result->update();
                $document = 'uploads/document/'.$filename;
            }

            $result->business_name = $request->business_name;
            $result->about_business = $request->about_business;
            $result->document_type = $request->document_type;
            $result->experience = $request->experience;
            $result->address = $request->address;
            $result->save();

            $response['id'] = $result->id;
            $response['name'] = $result->name;
            $response['email'] = $result->email;
            $response['phone'] = $result->phone;
            $response['place'] = $result->city->city_name;
            $response['address'] = $result->address;
            $response['document_type'] = $result->document_type;
            $response['document'] = $document;
            return response()->json([
                'success'  => true,
                'message'  => 'Profile update successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @Function: Resend otp
     **/
    public function resendOTP(Request $request){
        $data = $request->only('user_id');
        $validator = Validator::make($data, [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $otp = rand(1000, 9999);
        //$otp = 1234;
        $userDetails = User::where(['id' => $request->user_id])->first();
        if(!empty($userDetails)){
            $this->smsSend($otp, $userDetails->phone);
            $userDetails->otp = $otp;
            $userDetails->save();

            $resp['user_id'] = $userDetails->id;
            $resp['opt']     = $otp;
            return response()->json([
                'data'  => $resp,
                'success' => true,
                'message' => 'Resend otp successfully!'
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 400);
        } 
    }

    /**
     * @Function: matchOTP
     **/
    public function matchOTP(Request $request){
        $data = $request->only('otp', 'user_id');
        $validator = Validator::make($data, [
            'otp' => 'required',
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $userDetails = User::where(['id' => $request->user_id, 'otp' => $request->otp])->first();
        if(!empty($userDetails)){
            $userDetails->otp = '';
            $userDetails->save();

            $resp['user_id'] = $userDetails->id;
            return response()->json([
                'data'  => $resp,
                'success' => true,
                'message' => 'OTP match successfully'
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'OTP not match',
            ], 400);
        } 
    }

    /**User remove Api */
    public function removeUser(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        try {
                $user = JWTAuth::authenticate($request->token);
                $userData = User::select('id','name','google_id','apple_id','email','email_verified_at','password as pas','token','phone','login_type','remember_token as rt','role','active','dob','package_id','package_limit','updated_at','created_at','otp','otp_generation_time','otp_verification','business_info','image','device_id','bc_id','is_deleted')->where('id', $user->id)->first()->toarray();
                $postData = array(
                        'id' => $userData['id'],
                        'name' => $userData['name'],
                        'google_id' => $userData['google_id'],
                        'apple_id' => $userData['apple_id'],
                        'email' => $userData['email'],
                        'email_verified_at' => $userData['email_verified_at'],
                        'password' => $userData['pas'],
                        'token' => $userData['token'],
                        'phone' => $userData['phone'],
                        'login_type' => $userData['login_type'],
                        'remember_token' => $userData['rt'],
                        'role' => $userData['role'],
                        'active' => $userData['active'],
                        'dob' => $userData['dob'],
                        'package_id' => $userData['package_id'],
                        'package_limit' => $userData['package_limit'],
                        'updated_at' => $userData['updated_at'],
                        'created_at' => $userData['created_at'],
                        'otp' => $userData['otp'],
                        'otp_generation_time' => $userData['otp_generation_time'],
                        'otp_verification' => $userData['otp_verification'],
                        'business_info' => $userData['business_info'],
                        'image' => $userData['image'],
                        'device_id' => $userData['device_id'],
                        'bc_id' => $userData['bc_id'],
                        'is_deleted' => $userData['is_deleted']
                    );
                DB::table('users_deleted')->insert($postData);
                User::where('id', $user->id)->delete();
                JWTAuth::invalidate($request->token); 
                return response()->json([
                    'success' => true,
                    'message' => 'User remove successfully!'
                ], 200);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be deleted'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**forgot password Api */
    public function forgotpassword(Request $request)
    {
        $data = $request->only('phone');

        //valid credential
        $validator = Validator::make($data, [
            'phone' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }

        //Crean token
        $otp = rand(1000, 9999);
        //$otp = 1234;
        $userDetails = User::where(['phone' => $request->phone])->first();
        if(!is_null($userDetails)){
            $this->smsSend($otp, $userDetails->phone);
            $userDetails->otp = $otp;
            $userDetails->save();

            $resp['user_id'] = $userDetails->id;
            $resp['opt']     = $otp;
            return response()->json([
                'data'  => $resp,
                'success' => true,
                'message' => 'OTP send successfully!'
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 400);
        } 
    }

    /** Reset password */
    public function resetPassword(Request $request)
    {
        $data = $request->only('user_id', 'password', 'conf_password');

        //valid credential
        $validator = Validator::make($data, [
            'user_id' => 'required',
            'password' => 'required|min:6',
            'conf_password' => 'required'
        ]);

        if ($request->password != $request->conf_password) {
            return response()->json(['success'=>false,'message' => 'Password and confirm password should be same'], 200);
        }
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }

        $userDetails = User::where(['id' => $request->user_id])->first();
        if(!empty($userDetails)){
            $userDetails->password = Hash::make($request->password);
            $userDetails->bc_id = md5($request->password);
            $userDetails->save();

            $resp['user_id'] = $userDetails->id;
            return response()->json([
                'data'  => $resp,
                'success' => true,
                'message' => 'Password reset successfully!'
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 400);
        } 
    }

    /**Logout Api */
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }

        //Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out',
                'status'=>200
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**About us Page */
    public function about_us(Request $request)
    {   
        $about_us = Pages::where(['title'=>'About Morvi'])->first()->toarray();
        $result = [];
        if (!empty($about_us)) {

            $result['id'] =  $about_us['id'];
            $result['title'] =  $about_us['title'];
            $result['content'] =  strip_tags($about_us['content']);

            return response()->json([

                'data' =>$result,

                'success' => true,

                'message' => 'Get about us page successfully.',

                'status'=>200

            ]);

        }else{

            return response()->json([

                'success' => false,

                'message' => 'About Us Page Not Found'

            ]);
        }
    }

    /**Term Condition Page */
    public function term_condition(Request $request)
    {   
        $term_condition = Pages::where(['title'=>'Terms and conditions'])->first()->toarray();

        $result = [];
        if (!empty($term_condition)) {
            $result['id'] =  $term_condition['id'];
            $result['title'] =  $term_condition['title'];
            $result['content'] =  strip_tags($term_condition['content']);

            return response()->json([

                'data' =>$result,

                'success' => true,

                'message' => 'Get term & condition page successfully.',

                'status'=>200

            ]);

        }else{

            return response()->json([

                'success' => false,

                'message' => 'Term & condition page Not Found.',

             ]);
        }                
    }

    /**Privacy Policy Page */
    public function policy(Request $request)
    {   
        $policy = Pages::where(['title'=>'Data Policy'])->first()->toarray();

        $result = [];
        if (!empty($policy)) {

            $result['id'] =  $policy['id'];
            $result['title'] =  $policy['title'];
            $result['content'] =  strip_tags($policy['content']);

            return response()->json([

                'data' =>$result,

                'success' => true,

                'message' => 'Get Privacy policy page successfully.',

                'status'=>200

            ]);

        }else{

            return response()->json([

                'success' => false,

                'message' => 'Get Privacy policy page Not Found.',

            ]);

        }              
    }

    /** Social media link */
    public function socialMediaLink(Request $request)
    {   
        $socialId = array('8','9','10','11');
        $result = Pages::whereIn('id', $socialId)->get();
        $response = [];
        if(count($result) > 0){
            foreach($result as $res){
                $data['id'] = $res->id;
                $data['title'] = $res->title;
                $data['url'] = $res->content;
                $response[] = $data;
            }
            return response()->json([

                'data'    => $response,

                'success' => true,

                'message' => 'Social media link',

                'status'  => 200

            ]);
        }
        else{
            return response()->json([

                'success' => false,

                'message' => 'Record not found',

            ]);
        }             
    }

    /**
     * Slider
     */
    public function slider(Request $request)
    {
        $validator = Validator::make($request->only('slider_type'), [
            'slider_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = [];
        $response = Advertisement::where(['position'=>'Main Slider', 'status' => 1])->get();
        if($response){
            foreach ($response as $res) {
                $imageArr = explode(',', $res->image);
                if($imageArr){
                    foreach($imageArr as $r){
                        $data['img'] = 'uploads/advertisement/'.$r;
                        $result[] = $data;
                    }
                }
            }
        }
        if (!empty($result)) {

            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Main slider list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Category
     */
    public function categoryList(Request $request)
    {
        $result = [];
        $response = Category::where(['status' => 1, 'parent_id' => 0])->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['title'] = $res->category_name;
                $data['icon'] = 'uploads/category/'.$res->category_icon;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Category list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Category
     */
    public function brandList(Request $request)
    {
        $result = [];
        $response = Brand::where(['status' => 1])->get()->toarray();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res['id'];
                $data['title'] = $res['brand_name'];
                $data['icon'] = 'uploads/brand/'.$res['brand_icon'];
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Brand list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Category
     */
    public function subCategoryList(Request $request)
    {
        $validator = Validator::make($request->only('category_id'), [
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = [];
        $response = Category::where(['status' => 1, 'parent_id' => $request->category_id])->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['parent_id'] = $res->parent_id;
                $data['title'] = $res->category_name;
                $data['icon'] = 'uploads/category/'.$res->category_icon;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Sub category list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * All sub category
     */
    public function allSubCategoryList(Request $request)
    {
        $result = [];
        $response = Category::where('status', 1)->where('parent_id', '>', 0)->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['parent_id'] = $res->parent_id;
                $data['title'] = $res->category_name;
                $category_tables = CategoryTable::where('sub_category_id', $res->id)->first('id');
                $data['category_tables_id'] = @$category_tables->id;
                $data['icon'] = 'uploads/category/'.$res->category_icon;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Sub category list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * State list
     */
    public function stateList(Request $request)
    {
        $validator = Validator::make($request->only('country_id'), [
            'country_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = [];
        $response = State::where(['status' => 1, 'country_id' => $request->country_id])->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['state'] = $res->state_name;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'State list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * City list
     */
    public function cityList(Request $request)
    {
        $validator = Validator::make($request->only('state_id'), [
            'state_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = [];
        $response = City::where(['status' => 1, 'state_id' => $request->state_id])->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['city'] = $res->city_name;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'City list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * All City list
     */
    public function allCityList(Request $request)
    {
        $result = [];
        $cityname = $request->search_city;
        $response = [];
        if($cityname != ''){
            if(strlen($cityname) > 3){
                $response = City::where('status', 1)->where('city_name', 'like', $cityname. '%')->get();
            }/*
            else{
                $response = City::where(['status' => 1])->take(3)->get();
            }*/
        }/*
        else{
            $response = City::where(['status' => 1])->take(3)->get();
        }*/
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['city'] = $res->city_name;
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'City list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Name 
    **/
    public function updatePhotographerName(Request $request){
        $validator = Validator::make($request->only('user_id','name'), [
            'user_id' => 'required',
            'name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:40'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {

            $result->name = $request->name;
            $result->save();

            $response['id'] = $result->id;
            $response['name'] = $result->name;
            return response()->json([
                'success'  => true,
                'message'  => 'Name updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Place 
    **/
    public function updatePhotographerPlace(Request $request){
        $validator = Validator::make($request->only('user_id','place'), [
            'user_id' => 'required',
            'place' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {

            $result->place = $request->place;
            $result->save();

            $response['id'] = $result->id;
            $response['place'] = $result->city->city_name;
            return response()->json([
                'success'  => true,
                'message'  => 'Place updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Password 
    **/
    public function updatePhotographerPassword(Request $request){
        $validator = Validator::make($request->only('user_id','old_password', 'new_password'), [
            'user_id' => 'required',
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $new_password = Hash::make($request->new_password);
        $old_password = md5($request->old_password);
        $new_password_bcid = md5($request->new_password);
        $bc_id = md5($request->new_password);
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {
            if($result->bc_id != $old_password){
                return response()->json(['success'=>false,'message' => 'Old password is not match'], 200);
            }else{
                $result->password = $new_password;
                $result->bc_id = $new_password_bcid;
                $result->save();

                $response['id'] = $result->id;
                return response()->json([
                    'success'  => true,
                    'message'  => 'Password changed successfully!',
                    'data'     =>  $response,
                    'status'   => 200
                ]);                
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Photographer Image Update
    **/
    public function updatePhotographerImage(Request $request){
        $validator = Validator::make($request->only('user_id','image'), [
            'user_id' => 'required',
            'image'=>'mimes:jpeg,png,jpg,pdf'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {
            $profile_img = 'uploads/user.png';
            if($request->file('image')){
                if($result->image != ''){
                    $path = public_path()."/uploads/profile/".$result->image;
                    unlink($path);
                }
                $file = $request->file('image');
                $filename= $result->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                $file->move(public_path('uploads/profile'), $filename);
                $result->image = $filename;
                $result->update();
                $profile_img = 'uploads/profile/'.$filename;
            }

            $response['id'] = $result->id;
            $response['image'] = $profile_img;
            return response()->json([
                'success'  => true,
                'message'  => 'Profile image updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Remove Photographer Image
    **/
    public function removePhotographerImage(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id])->first();
        if (!empty($result)) {
            $profile_img = 'uploads/user.png';
            if($result->image != ''){
                $path = public_path()."/uploads/profile/".$result->image;
                if(is_file($path)){
                    unlink($path);
                }
                $result->image = '';
                $result->save();
            }

            $response['id'] = $result->id;
            $response['image'] = $profile_img;
            return response()->json([
                'success'  => true,
                'message'  => 'Image remove successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Business name 
    **/
    public function updatePhotographerBusinessName(Request $request){
        $validator = Validator::make($request->only('user_id','business_name'), [
            'user_id' => 'required',
            'business_name' => 'required|min:3|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 2])->first();
        if (!empty($result)) {

            $result->business_name = $request->business_name;
            $result->save();

            $response['id'] = $result->id;
            $response['business_name'] = $result->business_name;
            return response()->json([
                'success'  => true,
                'message'  => 'Business name updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer About Business 
    **/
    public function updatePhotographerAboutBusiness(Request $request){
        $validator = Validator::make($request->only('user_id','about_business'), [
            'user_id' => 'required',
            'about_business' => 'max:150'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 2])->first();
        if (!empty($result)) {

            $result->about_business = $request->about_business;
            $result->save();

            $response['id'] = $result->id;
            $response['about_business'] = $result->about_business;
            return response()->json([
                'success'  => true,
                'message'  => 'About business details updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Address
    **/
    public function updatePhotographerAddress(Request $request){
        $validator = Validator::make($request->only('user_id','address'), [
            'user_id' => 'required',
            'address' => 'required|min:10|max:150'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 2])->first();
        if (!empty($result)) {

            $result->address = $request->address;
            $result->save();

            $response['id'] = $result->id;
            $response['address'] = $result->address;
            return response()->json([
                'success'  => true,
                'message'  => 'Address updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Update Photographer Address
    **/
    public function updateSocialLink(Request $request){
        $validator = Validator::make($request->only('user_id','social_link', 'social_type'), [
            'user_id' => 'required',
            'social_link' => 'required',
            'social_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = User::where(['id' => $request->user_id, 'role' => 2])->first();
        if (!empty($result)) {
            if($request->social_type == 'facebook'){
                $result->facebook_url = $request->social_link;
            }
            elseif($request->social_type == 'linkedin'){
                $result->linkedin_url = $request->social_link;
            }
            elseif($request->social_type == 'youtube'){
                $result->youtube_url = $request->social_link;
            }
            elseif($request->social_type == 'instgram'){
                $result->instgram_url = $request->social_link;
            }
            elseif($request->social_type == 'twitter'){
                $result->twitter_url = $request->social_link;
            }
            $result->save();
            return response()->json([
                'success'  => true,
                'message'  => ucfirst($request->social_type).' link updated successfully!',
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @Function: Professional 
     */
    public function userProfessional(Request $request){
        $validator = Validator::make($request->only('user_id','category_id'), [
            'user_id' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $categoryArr = explode(',',$request->category_id);
        if(count($categoryArr) > 5){
            return response()->json(['success'=>false,'message' => 'Select only 5 professionals'], 200);
        }
        $lid = 1;
        UserProfessional::where('user_id', $request->user_id)->delete();
        foreach($categoryArr as $res){
            $existRecord = UserProfessional::where(['user_id' => $request->user_id, 'category_id' => $res])->count();
            if($existRecord == 0){
                $professional = new UserProfessional;
                $professional->user_id = $request->user_id;
                $professional->category_id = $res;
                $professional->save();
                $lid++;                
            }
        }
        if($lid > 1){
            return response()->json([
                'success'  => true,
                'message'  => 'Record are stored successfully!',
                'status'   => 200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not store successfully',
            ]);
        }
    }

    /**
     *@function: Professional List
     */
    public function userProfessionalList(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $professionalData = UserProfessional::where('user_id', $request->user_id)->get();
        $result = [];
        if(count($professionalData) > 0){
            foreach ($professionalData as $res) {
                $data['id'] = $res->id;
                $data['professional_id'] = @$res->category->id;
                $data['title'] = @$res->category->category_name;
                $data['status'] = $res->status;
                $data['icon'] = 'uploads/category/'.@$res->category->category_icon;
                $result[] = $data;
            }
            if (!empty($result)) {
                return response()->json([
                    'data' =>$result,
                    'success' => true,
                    'message' => 'Professional list.',
                    'status'=>200
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Found.',
                ]);
            }
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @Function: User complete profile 
     */
    public function compleleProfile(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $sn = 5;
        $response = [];
        $postdata['id'] = $request->user_id;
        $postdata['title'] = 'Your Advertisement';
        $postdata['category_tables_id'] = 0;
        $postdata['status'] = UserAdvertisment::where(['user_id' => $request->user_id])->count();
        $postdata['count'] = 1;
        $response[] = $postdata;

        $postdata['id'] = $request->user_id;
        $postdata['title'] = 'Sell Products';
        $postdata['category_tables_id'] = 0;
        $postdata['status'] = UserSellProduc::where(['user_id' => $request->user_id])->count();
        $postdata['count'] = 2;
        $response[] = $postdata;
/*      
        $postdata['id'] = $request->user_id;
        $postdata['title'] = 'Category';
        $postdata['category_tables_id'] = 0;
        $postdata['status'] = UserCategory::where('user_id', $request->user_id)->count();
        $postdata['count'] = 3;
        $response[] = $postdata;
*/
        $postdata['id'] = $request->user_id;
        $postdata['title'] = 'Media';
        $postdata['category_tables_id'] = 0;
        $postdata['status'] = UserMedia::where('user_id', $request->user_id)->count();
        $postdata['count'] = 4;
        $response[] = $postdata;

        $professionalData = UserProfessional::where(['user_id' => $request->user_id, 'status' => 1])->get();
        if(count($professionalData) > 0){
            foreach($professionalData as $res){
                $categoryTable = CategoryTable::where('category_id', $res->category_id)->get();
                if($categoryTable){
                    foreach($categoryTable as $rrr){
                        $data['id'] = $res->id;
                        $data['title'] = $rrr->table_name;
                        $data['category_tables_id'] = $rrr->id;
                        $tableRecord = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $rrr->id])->groupBy('row_number')->count();
                        if($tableRecord == 0){
                            $data['status'] = 0;
                        }
                        else{
                            $data['status'] = 1;                        
                        }
                        $data['count'] = $sn++;
                        $response[] = $data; 
                    }
                }
            }
            if($response){
                return response()->json([
                    'data'      =>  $response,
                    'success'   =>  true,
                    'message'   =>  'Complele profile',
                    'status'    =>  200
                ]);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Found.',
                ]);
            }
        }
        else{
            if($response){
                return response()->json([
                    'data'      =>  $response,
                    'success'   =>  true,
                    'message'   =>  'Complele profile',
                    'status'    =>  200
                ]);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Found.',
                ]);
            }
        }
    }

    /**
     *Function @ Sub Category User Data 
     **/
    public function subCategoryUserData(Request $request){
        $validator = Validator::make($request->only('user_id', 'category_id'), [
            'user_id' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $categoryTable = CategoryTable::where(['category_id' => $request->category_id])->get();
        if($categoryTable){
            foreach($categoryTable as $rrr){
                $tableRecord = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $rrr->id])->groupBy('row_number')->count();
                if($tableRecord > 0){
                    $data['id'] = $rrr->id;                
                    $data['title'] = $rrr->table_name;
                    $data['category_tables_id'] = $rrr->id;
                    $response[] = $data; 
                }
            }
        }
        $professionalData = UserProfessional::where(['user_id' => $request->user_id, 'status' => 1])->get();
        if(count($categoryTable) > 0){
            if($response){
                return response()->json([
                    'success'   =>  true,
                    'data'      =>  $response,
                    'message'   =>  'Sub Category List'
                ], 200);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Found.',
                ]);
            }
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: Change Professional Status
    **/
    public function changeProfessionalStatus(Request $request){
        $validator = Validator::make($request->only('user_id','professional_id', 'status'), [
            'user_id' => 'required',
            'professional_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $result = UserProfessional::where(['user_id' => $request->user_id, 'id' => $request->professional_id])->first();
        if (!empty($result)) {
            $result->status = $request->status;
            $result->save();
            $response['id']                =  $result->id;
            $response['professional_id']   =  $result->category->id;
            $response['title']             =  $result->category->category_name;
            $response['status']            =  $result->status;
            $response['icon']              =  'uploads/category/'.$result->category->category_icon;
            return response()->json([
                'success'  => true,
                'message'  => 'Status updated successfully!',
                'data'     =>  $response,
                'status'   => 200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @Function: remove professional details 
     */
    public function removeUserProfessional(Request $request){
         $validator = Validator::make($request->only('user_id','professional_id'), [
            'user_id' => 'required',
            'professional_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $affectedRow = UserProfessional::where(['user_id' => $request->user_id, 'id' => $request->professional_id])->delete();
        if($affectedRow){
            return response()->json([
                'success'  => true,
                'message'  => 'Professional remove successfully!',
                'status'   => 200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Category
     */
    public function getAdvertisement(Request $request)
    {
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = [];
        $response = UserAdvertisment::where(['user_id' => $request->user_id])->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['advertisement_type'] = $res->advertisement_type;
                if($res->advertisement_type == 'Text'){
                    $data['advertisement'] = $res->advertisement;
                }
                else{
                    $data['advertisement'] = 'uploads/advertisement/'.$res->advertisement;
                }
                $data['location_id'] = $res->location_id;
                $data['location'] = $this->locationName($res->location_id);
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Advertisement list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Photographer in focus
     */
    public function photographerInFocus(Request $request)
    {
        /*
        $validator = Validator::make($request->only('location'), [
            'location' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }*/
        $result = [];
        if(!empty($request->location)){
            $response = UserAdvertisment::where('valid_till', '>', time())->where('user_id', '>', 1)
                                            ->whereRaw('FIND_IN_SET(?, location_id)', [$request->location])
                                            ->get();
        }
        else{
            $response = UserAdvertisment::where('valid_till', '>', time())->where('user_id', '>', 1)->get();
        }
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['advertisement_type'] = $res->advertisement_type;
                if($res->advertisement_type == 'Text'){
                    $data['advertisement'] = $res->advertisement;
                }
                else{
                    $data['advertisement'] = 'uploads/advertisement/'.$res->advertisement;
                }
                $data['location'] = $this->locationName($res->location_id);
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Advertisement list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * Showcase Admin advertisement list
     */
    public function showcaseList(Request $request)
    {
        $result = [];
        $response = UserAdvertisment::where('user_id', '=', 1)->where('status',1)->get();
        if($response){
            foreach ($response as $res) {
                $data['id'] = $res->id;
                $data['advertisement_type'] = $res->advertisement_type;
                if($res->advertisement_type == 'Text'){
                    $data['advertisement'] = $res->advertisement;
                }
                else{
                    $data['advertisement'] = 'uploads/advertisement/'.$res->advertisement;
                }
                $data['location'] = $this->locationName($res->location_id);
                $result[] = $data;
            }
        }
        if (!empty($result)) {
            return response()->json([
                'data' =>$result,
                'success' => true,
                'message' => 'Advertisement list.',
                'status'=>200
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @Function: Location Name
     */
    public function locationName($id){
        $location = '';
        if(!empty($id)){
            $ids = explode(',', $id);
            $locationArr = City::whereIn('id', $ids)->get();
            if($locationArr){
                $resp = [];
                foreach($locationArr as $res){
                    array_push($resp, $res->city_name);
                }
                $location = implode(',', $resp);
            }
        }
        return $location;
    }

    /* Add Advertisement */
    public function addAdvertisement(Request $request){
        $validator = Validator::make($request->only('user_id', 'advertisement_type', 'advertisement', 'location'), [
            'user_id' => 'required',
            'advertisement_type' => 'required',
            'advertisement' => 'required',
            'location' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $strtotime = date('d, M Y H:s', strtotime('+1 year'));
        $validtill = strtotime('+1 year');
        $exAdvertisement = UserAdvertisment::where(['user_id' => $request->user_id])->first();
        if(!empty($exAdvertisement)){
            if($exAdvertisement->advertisement_type == "Photo" || $exAdvertisement->advertisement_type == "Video"){
                $path = public_path()."/uploads/advertisement/".$exAdvertisement->advertisement;
                if(is_file($path)){
                    unlink($path);
                }
            }
            UserAdvertisment::where('user_id', $request->user_id)->delete();
            $advertisement = new UserAdvertisment;
            if($request->advertisement_type == 'Text'){
                $advertisement->user_id = $request->user_id;
                $advertisement->advertisement_type = $request->advertisement_type;
                $advertisement->advertisement = $request->advertisement;
                $advertisement->location_id = $request->location;
                $advertisement->valid_till = $validtill;
                $advertisement->status = 1;
                $advertisement->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisement added successfully!',
                    'status'=>200
                ]);
            }
            else{
                $advertisement->user_id = $request->user_id;
                $advertisement->advertisement_type = $request->advertisement_type;
                $advertisement->advertisement = $request->advertisement;
                $advertisement->location_id = $request->location;
                $advertisement->valid_till = $validtill;
                $advertisement->status = 1;
                $advertisement->save();
                if($request->file('advertisement')){
                    $file = $request->file('advertisement');
                    $filename= $advertisement->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                    $file->move(public_path('uploads/advertisement'), $filename);
                    $advertisement->advertisement = $filename;
                    $advertisement->update();
                }      
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisement added successfully!',
                    'status'=>200
                ]);          
            }
        }
        else{
            $advertisement = new UserAdvertisment;
            if($request->advertisement_type == 'Text'){
                $advertisement->user_id = $request->user_id;
                $advertisement->advertisement_type = $request->advertisement_type;
                $advertisement->advertisement = $request->advertisement;
                $advertisement->location_id = $request->location;
                $advertisement->valid_till = $validtill;
                $advertisement->status = 1;
                $advertisement->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisement added successfully!',
                    'status'=>200
                ]);
            }
            else{
                $advertisement->user_id = $request->user_id;
                $advertisement->advertisement_type = $request->advertisement_type;
                $advertisement->advertisement = $request->advertisement;
                $advertisement->location_id = $request->location;
                $advertisement->valid_till = $validtill;
                $advertisement->status = 1;
                $advertisement->save();
                if($request->file('advertisement')){
                    $file = $request->file('advertisement');
                    $filename= $advertisement->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                    $file->move(public_path('uploads/advertisement'), $filename);
                    $advertisement->advertisement = $filename;
                    $advertisement->update();
                }      
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisement added successfully!',
                    'status'=>200
                ]);          
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Advertisement not added successfully',
        ]);
    }

    /**
     * @Function: Remove advertisement
     */
    public function removeAdvertisment(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $exAdvertisement = UserAdvertisment::where(['user_id' => $request->user_id])->first();
        if(!empty($exAdvertisement)){
            if($exAdvertisement->advertisement_type == "Photo" || $exAdvertisement->advertisement_type == "Video"){
                $path = public_path()."/uploads/advertisement/".$exAdvertisement->advertisement;
                if(is_file($path)){
                    unlink($path);
                }
            }
            $affectedRow = UserAdvertisment::where('user_id', $request->user_id)->delete();
            if ($affectedRow) {
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisement remove successfully.',
                    'status'=>200
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Found.',
                ]);
            }            
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record Not Found.',
            ]);
        }
    }

    /**
     * @function: User Sell Product
    **/
    public function addProduct(Request $request){
        $validator = Validator::make($request->only('user_id','product_name', 'model_number', 'brand_id', 'product_price', 'product_condition', 'product_description', 'product_invoice', 'location_id', 'photos'), [
            'user_id' => 'required',
            'brand_id' => 'required',
            'product_price' => 'required',
            'product_condition' => 'required',
            'product_description' => 'required',
            'product_invoice' => 'required|mimes:jpeg,png,jpg,pdf',
            'location_id' => 'required',
            'product_name' => 'required|min:5|max:150',
            'model_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productData = new UserSellProduc;
        $productData->user_id               =   $request->user_id;
        $productData->brand_id              =   $request->brand_id;
        $productData->product_name          =   $request->product_name;
        $productData->model_number          =   $request->model_number;
        $productData->product_price         =   $request->product_price;
        $productData->product_condition     =   $request->product_condition;
        $productData->product_description   =   $request->product_description;
        $productData->location_id           =   $request->location_id;
        $productData->save();
        if($productData->id != ''){
            /* Product invoice */
            if($request->file('product_invoice')){
                $file = $request->file('product_invoice');
                $filename= $productData->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                $file->move(public_path('uploads/product_invoice'), $filename);
                $productData->product_invoice = $filename;
                $productData->save();
            }

            /* Product photo */
            if ($request->hasFile('photos')) {
                $allowedfileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                $files = $request->file('photos');
                foreach ($files as $file) {

                    $file_new_name = $productData->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/product_image');
                    $imgFile = Image::make($file->getRealPath());
                    $imgFile->resize(150, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPath.'/'.$file_new_name);
                    $destinationPath = public_path('uploads/product_resize_image');
                    $file->move($destinationPath, $file_new_name);

                    ProductImage::create([
                        'product_id'    => $productData->id,
                        'image'         => $file_new_name,
                    ]);
                    /*
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $check = in_array($extension, $allowedfileExtension);
                    if ($check) {
                        foreach ($request->photos as $photo) {
                            $filename = $photo->store('photos');
                        }
                    }*/
                }
            }
            return response()->json([
                'product_id' => $productData->id,
                'success' => true,
                'message' => 'Product added successfully!',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Product are not added successfully. Please try again',
            ]);
        }
    }

    /**
     * @Function: Get Product Details
     */
    public function getProductDetails(Request $request){
         $validator = Validator::make($request->only('user_id', 'product_id'), [
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productData = UserSellProduc::where(['user_id' => $request->user_id, 'id' => $request->product_id])->first();
        if(!empty($productData)){
            $response = [];
            $imageArr = [];
            $imageIdArr = [];
            $response['id'] = $productData->id;
            $response['user_id'] = $productData->user_id;
            $response['product_name'] = $productData->product_name;
            $response['brand_id'] = $productData->brand_id;
            $response['brand_name'] = $productData->brand->brand_name;
            $response['product_price'] = $productData->product_price;
            $response['product_condition'] = $productData->product_condition;
            $response['product_description'] = $productData->product_description;
            $response['location'] = $this->locationName($productData->location_id);
            if($productData->product_invoice != ''){
                $response['product_invoice'] = 'uploads/product_invoice/'.$productData->product_invoice;
            }
            else{
                $response['product_invoice'] = 'uploads/blank.pdf';                
            }
            $productImage = ProductImage::where('product_id', $productData->id)->get();
            if(count($productImage) > 0){                
                foreach($productImage as $img){
                    $imgPath = 'uploads/product_resize_image/'.$img->image;
                    //array_push($imageArr, $imgPath);
                    //array_push($imageIdArr, $img->id);
                    $img_id['img_id'] = $img->id;
                    $img_path['path'] = $imgPath;
                    $imageIdArr[] = $img_id;
                    $imageArr[] = $img_path;
                }
                if(count($imageArr) > 0){
                    //$response['product_image_id'] = implode(',', $imageIdArr);
                   //$response['product_image'] = implode(',', $imageArr);
                    //$response['product_image_id'] = $imageIdArr;
                    //$response['product_image'] = $imageArr;
                }
                else{
                    //$response['product_image'] = '';
                    //$response['product_image_id'] = '';
                }
            }
            else{
                //$response['product_image'] = '';
                //$response['product_image_id'] = '';
            }

            return response()->json([
                'data' =>$response,
                'product_image_id' =>$imageIdArr,
                'product_image' =>$imageArr,
                'success' => true,
                'message' => 'Product details',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Product List
     */
    public function productList(Request $request){
         $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $result = UserSellProduc::where(['user_id' => $request->user_id])->orderBy('id', 'desc')->get();
        if(count($result) > 0){
            $resp = [];
            foreach($result as $productData){
                $response['id'] = $productData->id;
                $response['user_id'] = $productData->user_id;
                $response['product_name'] = $productData->product_name;
                $response['brand_id'] = $productData->brand_id;
                $response['brand_name'] = $productData->brand->brand_name;
                $response['model_number'] = $productData->model_number;
                $response['product_price'] = $productData->product_price;
                $response['product_condition'] = $productData->product_condition;
                $response['product_description'] = $productData->product_description;
                $response['location'] = $this->locationName($productData->location_id);
                if($productData->product_invoice != ''){
                    $response['product_invoice'] = 'uploads/product_invoice/'.$productData->product_invoice;
                }
                else{
                    $response['product_invoice'] = 'uploads/blank.pdf';
                }
                $productImage = ProductImage::where('product_id', $productData->id)->get();
                if(count($productImage) > 0){
                    $imageArr = [];
                    $imageIdArr = [];
                    $srimg = 1;
                    foreach($productImage as $img){
                        if($srimg == 1){
                            $imgPath = 'uploads/product_resize_image/'.$img->image;
                            array_push($imageArr, $imgPath);
                            array_push($imageIdArr, $img->id);                            
                        }

                        $srimg++;
                    }
                    if(count($imageArr) > 0){
                        $response['product_image'] = implode(',', $imageArr);
                        $response['product_image_id'] = implode(',', $imageIdArr);
                    }
                    else{
                        $response['product_image'] = '';
                        $response['product_image_id'] = '';
                    }
                }
                else{
                    $response['product_image'] = '';
                    $response['product_image_id'] = '';
                }
                $resp[] = $response;
            }
            return response()->json([
                'data' =>$resp,
                'success' => true,
                'message' => 'Product details',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Product List
     */
    public function sellProductList(Request $request){
        if(!empty($request->location)){
            $result = UserSellProduc::where('status', '=', 1)
                                        ->whereRaw('FIND_IN_SET(?, location_id)', [$request->location])
                                        ->orderBy('id','desc')
                                        ->get();
        }
        else{
            $result = UserSellProduc::where('status', '=', 1)->orderBy('id','desc')->get();
        }
        if(count($result) > 0){
            $resp = [];
            foreach($result as $productData){
                $response['id'] = $productData->id;
                $response['user_id'] = $productData->user_id;
                $response['product_name'] = $productData->product_name;
                $response['brand_id'] = $productData->brand_id;
                $response['brand_name'] = $productData->brand->brand_name;
                $response['model_number'] = $productData->model_number;
                $response['product_price'] = $productData->product_price;
                $response['product_condition'] = $productData->product_condition;
                $response['product_description'] = $productData->product_description;
                $response['location'] = $this->locationName($productData->location_id);
                if($productData->product_invoice != ''){
                    $response['product_invoice'] = 'uploads/product_invoice/'.$productData->product_invoice;
                }
                else{
                    $response['product_invoice'] = 'uploads/blank.pdf';
                }
                $productImage = ProductImage::where('product_id', $productData->id)->get();
                if(count($productImage) > 0){
                    $imageArr = [];
                    $imageIdArr = [];
                    foreach($productImage as $img){
                        $imgPath = 'uploads/product_resize_image/'.$img->image;
                        array_push($imageArr, $imgPath);
                        array_push($imageIdArr, $img->id);
                    }
                    if(count($imageArr) > 0){
                        $response['product_image'] = implode(',', $imageArr);
                        $response['product_image_id'] = implode(',', $imageIdArr);
                    }
                    else{
                        $response['product_image'] = '';
                        $response['product_image_id'] = '';
                    }
                }
                else{
                    $response['product_image'] = '';
                    $response['product_image_id'] = '';
                }
                $resp[] = $response;
            }
            return response()->json([
                'data' =>$resp,
                'success' => true,
                'message' => 'Product list',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * Function@ Search product list
     */
    public function serachProducts(Request $request){
        $validator = Validator::make($request->only('product_name', 'locations'), []);
        $productName = '';
        $locationIds = [];
        if($request->product_name != ''){
            $productName = $request->product_name;
        }
        if($request->locations != ''){
            $locationIds = explode(',',$request->locations);
        }        

        $query = UserSellProduc::where('status', '=', 1);
        if(!empty($productName)){
            $query->where('product_name', 'like', $productName.'%');
        }
        if(count($locationIds) > 0){
            //$query->whereIn('location_id', $locationIds);
        }

        $result = $query->get();
        if(count($result) > 0){
            $resp = [];
            foreach($result as $productData){
                $response['id'] = $productData->id;
                $response['user_id'] = $productData->user_id;
                $response['product_name'] = $productData->product_name;
                $response['brand_id'] = $productData->brand_id;
                $response['brand_name'] = $productData->brand->brand_name;
                $response['model_number'] = $productData->model_number;
                $response['product_price'] = $productData->product_price;
                $response['product_condition'] = $productData->product_condition;
                $response['product_description'] = $productData->product_description;
                $response['location'] = $this->locationName($productData->location_id);
                if($productData->product_invoice != ''){
                    $response['product_invoice'] = 'uploads/product_invoice/'.$productData->product_invoice;
                }
                else{
                    $response['product_invoice'] = 'uploads/blank.pdf';
                }
                $productImage = ProductImage::where('product_id', $productData->id)->get();
                if(count($productImage) > 0){
                    $imageArr = [];
                    $imageIdArr = [];
                    foreach($productImage as $img){
                        $imgPath = 'uploads/product_resize_image/'.$img->image;
                        array_push($imageArr, $imgPath);
                        array_push($imageIdArr, $img->id);
                    }
                    if(count($imageArr) > 0){
                        $response['product_image'] = implode(',', $imageArr);
                        $response['product_image_id'] = implode(',', $imageIdArr);
                    }
                    else{
                        $response['product_image'] = '';
                        $response['product_image_id'] = '';
                    }
                }
                else{
                    $response['product_image'] = '';
                    $response['product_image_id'] = '';
                }
                $resp[] = $response;
            }
            return response()->json([
                'data' =>$resp,
                'success' => true,
                'message' => 'Product list',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Get Product Details
     */
    public function productDetails(Request $request){
         $validator = Validator::make($request->only('product_id'), [
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productData = UserSellProduc::where(['id' => $request->product_id])->first();
        if(!empty($productData)){
            $response = [];
            $imageArr = [];
            $imageIdArr = [];
            $userDetail = User::where('id', $productData->user_id)->first('place');
            $response['id'] = $productData->id;
            $response['user_id'] = $productData->user_id;
            $response['product_name'] = $productData->product_name;
            $response['brand_id'] = $productData->brand_id;
            $response['brand_name'] = $productData->brand->brand_name;
            $response['model_number'] = $productData->model_number;
            $response['product_price'] = $productData->product_price;
            $response['product_condition'] = $productData->product_condition;
            $response['product_description'] = $productData->product_description;
            $response['location'] = $this->locationName($productData->location_id);
            $response['place'] = $userDetail->place;
            if($productData->product_invoice != ''){
                $response['product_invoice'] = 'uploads/product_invoice/'.$productData->product_invoice;
            }
            else{
                $response['product_invoice'] = 'uploads/blank.pdf';                
            }
            $productImage = ProductImage::where('product_id', $productData->id)->get();
            if(count($productImage) > 0){
                
                foreach($productImage as $img){
                    $imgPath = 'uploads/product_resize_image/'.$img->image;
                    //array_push($imageArr, $imgPath);
                    //array_push($imageIdArr, $img->id);
                    $img_id['img_id'] = $img->id;
                    $img_path['path'] = $imgPath;
                    $imageIdArr[] = $img_id;
                    $imageArr[] = $img_path;
                }
                if(count($imageArr) > 0){
                    //$response['product_image_id'] = implode(',', $imageIdArr);
                   //$response['product_image'] = implode(',', $imageArr);
                   // $response['product_image_id'] = $imageIdArr;
                    //$response['product_image'] = $imageArr;
                }
                else{
                   // $response['product_image'] = '';
                    //$response['product_image_id'] = '';
                }
            }
            else{
               // $response['product_image'] = '';
                //$response['product_image_id'] = '';
            }

            $userDetails = User::select('id','email','name','phone','place','image', 'business_name', 'experience')->where('id', $productData->user_id)->first();
            $userDetail = [];
            $profile_img = 'uploads/user.png';
            $path1 = public_path()."/uploads/profile/".$userDetails->image;
            if(is_file($path1)){
                $profile_img = "uploads/profile/".$userDetails->image;
            }
            $userDetail['user_id'] = $userDetails->id;
            $userDetail['name'] = $userDetails->name;
            $userDetail['email'] = $userDetails->email;
            $userDetail['phone'] = $userDetails->phone;
            $userDetail['experience'] = $userDetails->experience;
            $userDetail['place'] = $this->locationName($userDetails->place);
            $userDetail['image'] = $profile_img;
            $userDetail['business_name'] = $userDetails->business_name;

            return response()->json([
                'data' =>$response,
                'product_image_id' =>$imageIdArr,
                'product_image' =>$imageArr,
                'userDetail' => $userDetail,
                'success' => true,
                'message' => 'Product details',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @function: User Sell Product
    **/
    public function productUpdate(Request $request){
        $validator = Validator::make($request->only('user_id','product_id','product_name', 'model_number', 'brand_id', 'product_price', 'product_condition', 'product_description', 'product_invoice', 'location_id', 'photos'), [
            'user_id' => 'required',
            'product_id' => 'required',
            'brand_id' => 'required',
            'product_price' => 'required',
            'product_condition' => 'required',
            'product_description' => 'required',
            'location_id' => 'required',
            'product_name' => 'required|min:5|max:150',
            'model_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productData = UserSellProduc::where('id', $request->product_id)->first();
        if(!empty($productData)){
            $productData->brand_id              =   $request->brand_id;
            $productData->product_name          =   $request->product_name;
            $productData->model_number          =   $request->model_number;
            $productData->product_price         =   $request->product_price;
            $productData->product_condition     =   $request->product_condition;
            $productData->product_description   =   $request->product_description;
            $productData->location_id           =   $request->location_id;
            $productData->save();
            if($productData->id != ''){
                /* Product invoice */
                if($request->file('product_invoice')){
                    $path = public_path()."/uploads/product_invoice/".$productData->product_invoice;
                    if(is_file($path)){
                        unlink($path);
                    }
                    $file = $request->file('product_invoice');
                    $filename= $productData->id.''.rand(10000, 99999).'.'.$file->getClientOriginalExtension();         
                    $file->move(public_path('uploads/product_invoice'), $filename);
                    $productData->product_invoice = $filename;
                    $productData->save();
                }

                /* Product photo */
                if ($request->hasFile('photos')) {
                    $allowedfileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                    $files = $request->file('photos');
                    foreach ($files as $file) {

                        $file_new_name = $productData->id.''.rand(10000, 99999).'.'.$file->getClientOriginalExtension();
                        $destinationPath = public_path('uploads/product_image');
                        $imgFile = Image::make($file->getRealPath());
                        $imgFile->resize(150, null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPath.'/'.$file_new_name);
                        $destinationPath = public_path('uploads/product_resize_image');
                        $file->move($destinationPath, $file_new_name);

                        ProductImage::create([
                            'product_id'    => $productData->id,
                            'image'         => $file_new_name,
                        ]);
                    }
                }
                return response()->json([
                    'product_id' => $productData->id,
                    'success' => true,
                    'message' => 'Product update successfully!',
                    'status'  => 200
                ]);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Product are not updated successfully. Please try again',
                    'status'  => 202
                ]);
            }
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     *@function: Remove product 
     */
    public function removeProduct(Request $request){
        $validator = Validator::make($request->only('user_id', 'product_id'), [
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productData = UserSellProduc::where('id', $request->product_id)->first();
        if(!empty($productData)){
            $path = public_path()."/uploads/product_invoice/".$productData->product_invoice;
            if(is_file($path)){
                unlink($path);
            }
            $imageIds = ProductImage::where('product_id', $request->product_id)->get();
            //$imageIds = explode(',', $request->photo_id);
            if($imageIds){
                foreach($imageIds as $img){
                    $path = public_path()."/uploads/product_image/".$img->image;
                    if(is_file($path)){
                        unlink($path);
                    }
                    $path1 = public_path()."/uploads/product_resize_image/".$img->image;
                    if(is_file($path1)){
                        unlink($path1);
                    }
                }
                ProductImage::where('product_id', $request->product_id)->delete();            
            }
            UserSellProduc::where('id', $request->product_id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product remove successfully',
                'status'=>200
            ]);
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Remove Product Image/Photos
     */
    public function removeProductPhotos(Request $request){
        $validator = Validator::make($request->only('user_id', 'product_id'), [
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $imageIds = ProductImage::select('image')->where('product_id', $request->product_id)->get();
        //$imageIds = explode(',', $request->photo_id);
        if(count($imageIds) > 0){
            foreach($imageIds as $img){
                $path = "/uploads/product_image/".$img->image;
                if(is_file($path)){
                    unlink($path);
                }
                $path1 = "/uploads/product_resize_image/".$img->image;
                if(is_file($path1)){
                    unlink($path1);
                }
            }
            ProductImage::where('product_id', $request->product_id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product photos remove successfully',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Remove Product Invoice
     */
    public function removeProductInvoice(Request $request){
         $validator = Validator::make($request->only('user_id', 'product_id'), [
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productInvoice = UserSellProduc::where('id', $request->product_id)->first();
        if(!empty($productInvoice)){
            $path = public_path()."/uploads/product_invoice/".$productInvoice->product_invoice;
            if(is_file($path)){
                unlink($path);
            }
            $productInvoice->product_invoice = '';
            $productInvoice->save();
            return response()->json([
                'success' => true,
                'message' => 'Product invoice remove successfully',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: User category list
     */
    public function userCategoryList(Request $request){
         $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $categoryList = UserCategory::select('id', 'category_name')->where('user_id', $request->user_id)->get();
        if(count($categoryList) > 0){
            $response = $categoryList->toarray();
            return response()->json([
                'data'    => $response,
                'success' => true,
                'message' => 'Category list',
                'status'  =>200
            ]);
        }
        else{
            return response()->json([
                'data' => array(),
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Add category data
     */
    public function addUserCategory(Request $request){
         $validator = Validator::make($request->only('user_id', 'category_name'), [
            'user_id' => 'required',
            'category_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $userCategory = UserCategory::where('user_id', $request->user_id)->delete();
        $categoryArray = $request->category_name;
        $lid = '';
        if($categoryArray){
            foreach($categoryArray as $res){
                if($res != ''){
                    $categoryData = new UserCategory;
                    $categoryData->user_id = $request->user_id;
                    $categoryData->category_name = $res;
                    $categoryData->save();
                    $lid = $categoryData->id;
                }
            }
            if($lid != ''){
                return response()->json([
                    'success' => true,
                    'message' => 'Category added successfully',
                    'status'=>200
                ]);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Category are not stored successfully.',
                    'status'  => 400
                ]);
            }
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Category is required',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Remove User category
     */
    public function removeUserCategory(Request $request){
         $validator = Validator::make($request->only('user_id', 'category_id'), [
            'user_id' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $productInvoice = UserCategory::where('id', $request->category_id)->delete();
        if($productInvoice){
            return response()->json([
                'success' => true,
                'message' => 'Category remove successfully',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: User Media list
     */
    public function userMediaList(Request $request){
         $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $mediaList = UserMedia::select('id', 'media_type', 'media_name')->where('user_id', $request->user_id)->get();
        if(count($mediaList) > 0){
            $response = [];
            foreach($mediaList as $res){
                $data['id'] = $res->id;
                $data['media_type'] = $res->media_type;
                $data['media_name'] = 'uploads/media/'.$res->media_name;
                $response[] = $data;
            }
            return response()->json([
                'data'    => $response,
                'success' => true,
                'message' => 'Media list',
                'status'  =>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @function: Add media
    **/
    public function addMedia(Request $request){
        if($request->media_type == 'Video'){
            $validator = Validator::make($request->all(), [
                'user_id'       => ['required'],
                'media_name'    => ['required','mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv','max:10240'],
                'media_type'    => ['required']
            ],
            ['media_name.max'=> 'Video must not be greater than 10MB']
        );
        }
        else{
            $validator = Validator::make($request->only('user_id','media_type', 'media_name'), [
                'user_id'       => 'required',
                'media_name'    => 'required|mimes:jpeg,png,jpg',
                'media_type'    => 'required'
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $mediaData = new UserMedia;
        $mediaData->user_id           =   $request->user_id;
        $mediaData->media_type        =   $request->media_type;
        $mediaData->save();
        if($mediaData->id != ''){
            if($request->file('media_name')){
                $file = $request->file('media_name');
                $filename= $mediaData->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();         
                $file->move(public_path('uploads/media'), $filename);
                $mediaData->media_name = $filename;
                $mediaData->save();
            }
            return response()->json([
                'product_id' => $mediaData->id,
                'success' => true,
                'message' => 'Media added successfully!',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Media are not added successfully. Please try again',
            ]);
        }
    }

    /**
     * @Function: Remove User media
     */
    public function removeMedia(Request $request){
         $validator = Validator::make($request->only('user_id', 'media_id'), [
            'user_id' => 'required',
            'media_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $mediaData = UserMedia::where('id', $request->media_id)->first();
        $path = public_path()."/uploads/media/".$mediaData->media_name;
        if(is_file($path)){
            unlink($path);
        }
        $affectedRow = UserMedia::where('id', $request->media_id)->delete();
        if($affectedRow){
            return response()->json([
                'success' => true,
                'message' => 'Media remove successfully',
                'status'=>200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * My professional list
     */
    public function myProfessionalList(Request $request)
    {
        $validator = Validator::make($request->only('user_id'), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $myProfessionalData = UserProfessional::where('user_id', $request->user_id)->pluck('category_id');
        if(count($myProfessionalData) > 0){
            $categoryData = CategoryTable::select('id','category_id as professional_id', 'table_name')->whereIn('category_id', $myProfessionalData)->get();

            return response()->json([
                    'data'    => $categoryData,
                    'success' => true,
                    'message' => 'My professional list',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * Table column list
     */
    public function tableColumnList(Request $request)
    {
        $validator = Validator::make($request->only('user_id', 'category_table_id'), [
            'user_id'           => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $response = [];
        $tableName = CategoryTable::where('id', $request->category_table_id)->first('table_name');
        $tableFieldsData = TableColumn::where('category_table_id', $request->category_table_id)->get();
        if(count($tableFieldsData) > 0){
            foreach($tableFieldsData as $res){
                $data['id'] = $res->id;
                $data['column_name'] = $res->column_name;
                $data['column_type'] = $res->column_type;
                if($res->column_type == 'dropdown'){
                    if($res->column_value != ''){
                        $data['column_value'] = explode(',',$res->column_value);
                    }
                    else{
                        $data['column_value'] = ''; 
                    }
                }
                else{
                    $data['column_value'] = $res->column_value;
                }
                $response[] = $data;
            }
            return response()->json([
                    'table_name' => $tableName->table_name,
                    'data'    => $response,
                    'success' => true,
                    'message' => 'My professional list',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Function: Add Table Column Data 
     */
    public function addTableColumnData(Request $request){

        //Log::debug($request->all());
        $validator = Validator::make($request->only('user_id', 'category_table_id', 'column_name', 'column_type', 'column_value', 'field_value', 'location_id', 'table_id', 'field_value_file'), [
            'user_id'           => 'required',
            'category_table_id' => 'required',
            'column_name'       => 'required',
            'column_type'       => 'required',
            /*'column_value'      => 'required',*/
            'field_value'       => 'required'
            /*'location_id'       => 'required'*/
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        //table id check 
        $table_id   = $request->table_id;
        if(!empty($table_id))
        {
            $is_table = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id, 'table_number' => $table_id])->count();
            if ($is_table <= 0) {
                return response()->json(['success'=>false,'message' => 'Table id not found'], 200);
            }
        } 

        $field_value    = $request->field_value;
        $column_name    = $request->column_name;
        $column_type    = $request->column_type;
        $column_value   = $request->column_value;
      //Log::info($request->all());
      //Log::debug($request->all());
        $collid = ProfessionalTableData::orderBy('id', 'desc')->first('id');
        $lid = 1;
        if(!empty($collid)){
            $lid = $collid->id + 1;
        }

        if(count($column_name) > 0){
            $row_number   = rand(1000, 9999).$lid;
            if(!empty($table_id))
            {
                $table_number = $table_id;
            }else{
                $table_number = rand(10000, 99999).$lid;
            }            

            $i = 0;
            $j = 0;
            foreach($column_name as $res){
                $tableData = new ProfessionalTableData;
                $tableData->user_id             = $request->user_id;
                $tableData->category_table_id   = $request->category_table_id;
                $tableData->column_name         = $column_name[$i];
                $tableData->column_type         = $column_type[$i];
                if($column_type[$i] == 'file'){
                }
                else{
                    $tableData->field_value     = $field_value[$i];
                    $j++;
                }
                $tableData->column_value        = $column_value[$i];
                $tableData->row_number          = $row_number;
                $tableData->table_number        = $table_number;
                $tableData->location_id         = @$request->location_id;
                $tableData->save();

                if($column_type[$i] == 'file'){
                    if($request->file('field_value_file')){
                        $file = $request->file('field_value_file');
                        $filename= $tableData->id.''.rand(1000, 9999).'.'.$file->getClientOriginalExtension();     
                        $file->move(public_path('uploads/table_column_file'), $filename);
                        $tableData->field_value = 'uploads/table_column_file/'.$filename;
                        $tableData->save();
                    }
                }
                $i++;
            }

            if(!empty($table_id))
            {
                return response()->json([
                    'success' => true,
                    'table_id' => $table_id,
                    'message' => 'Table data are updated successfully',
                    'status'  => 200
                ]);
            }else{
                return response()->json([
                    'success' => true,
                    'message' => 'Table data are added successfully',
                    'status'  => 200
                ]);
            }
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Table data are not added successfully. Please try again',
                    'status'  => 202
                ]);
        }
    }

    /**
     * @Functoin: Table data location 
     */
    public function addTableLocation(Request $request){
        $validator = Validator::make($request->only('user_id', 'category_table_id', 'location_id'), [
            'user_id'           => 'required',
            'category_table_id' => 'required',
            'location_id'       => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $affectedRows = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id])->update(['location_id' => $request->location_id]);

        if($affectedRows){
            return response()->json([
                'success' => true,
                'message' => 'Location added successfully',
                'status'  => 200
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }


    /**
     * @Functoin: Table data list 
     */
    public function tableDataList(Request $request){
        $validator = Validator::make($request->only('user_id', 'category_table_id'), [
            'user_id'           => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }

        $tableDataRow = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id])->groupBy('row_number')->get();
        $row_id = '';
        $location = '';
        if(count($tableDataRow) > 0){
            $response = [];
            foreach($tableDataRow as $resp){
                $row_id   = $resp->row_number;
                $location = $this->locationName($resp->location_id);
                $tableData = ProfessionalTableData::select('field_value')->where('row_number', $resp->row_number)->get();
                foreach($tableData as $rrr){
                    $data['field_value'] = $rrr->field_value;
                    $data['row_id']      = $resp->row_number;
                    $response[] = $data;
                }
            }
            return response()->json([
                    'data'     => $response,
                    'location' => $location,
                    'success' => true,
                    'message' => 'Table data list',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }

    /**
     * @Functoin: Table column list and data list 
     */
    public function tableColumnListAndDataList(Request $request){
        
        $validator = Validator::make($request->only('user_id', 'category_table_id'), [
            'user_id'           => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        //colume list 
        $columnArr = array();
        $list_data = array();
        $responseColumn = [];
       
        $tableName = CategoryTable::where('id', $request->category_table_id)->first('table_name');
        $tableFieldsData = TableColumn::where('category_table_id', $request->category_table_id)->get();
        if(count($tableFieldsData) > 0){
            foreach($tableFieldsData as $res){
                $data = [];
                $data['id'] = $res->id;
                $data['column_name'] = $res->column_name;
                $data['column_type'] = $res->column_type;
                //$data['field_value'] = [];
                if($res->column_type == 'dropdown'){
                    if($res->column_value != ''){
                        /*$data['column_value'] = explode(',',$res->column_value);*/
                        $newarray = [];
                        $arr= explode(",", $res->column_value);
                        foreach($arr as $keys => $line){
                           // $ndata['id'] = $keys;
                            $ndata['title'] = $line;
                            $newarray[] = $ndata;
                        }
                        $data['column_value'] = $newarray;
                    }
                    else{
                        $data['column_value'] = ''; 
                    }
                }
                else{
                    $data['column_value'] = $res->column_value;
                }
                $responseColumn[] = $data;
            }
            $sdata = array(
                'id' => 0,
                'column_name' => 'Action',
                'column_type' => '',
                'column_value' => ''
            );
            $responseColumn[] = $sdata;
            /*$sdata = array(
                'id' => 0,
                'column_name' => 'ROW_ID',
                'column_type' => '',
                'column_value' => ''
            );
            $responseColumn[] = $sdata;*/
 
            //data list
            $tableDataRowTable_number = ProfessionalTableData::select('table_number')->where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id])->groupBy('table_number')->get();
            
            $row_id = '';
            $location = '';             
            $table_number = '';             
            $counter = 0;
            $final_result = [];
            $tablecolumnDataArray = [];
            $responseTableIds = [];
            if(count($tableDataRowTable_number) > 0){
                $response = [];
                foreach($tableDataRowTable_number as $tresp){
                    $columnResponse = array();
                    $tttable_number   = $tresp->table_number;
                    $responseTableIds[] = $tttable_number;
                    $tableDataRow = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id, 'table_number'=>$tttable_number])->groupBy('row_number')->get();

                    if(count($tableDataRow) > 0){
                        $response = [];
                        foreach($tableDataRow as $resp){
                            $row_id   = $resp->row_number;
                            $table_number   = $resp->table_number;
                            $location = $this->locationName($resp->location_id);

                            $response[] = $data;
                            $tableData = ProfessionalTableData::select('id','field_value', 'table_number', 'column_name')->where('row_number', $resp->row_number)->get();
                            foreach($tableData as $keyy => $rrr){
                                $data = [];
                                $data['field_value'] = $rrr->field_value;
                                $data['table_id'] = $rrr->table_number;
                                $data['row_id']      = $resp->row_number;
                                //$columnResponse[$keyy]['field_data'] = $data;
                                //$columnResponse[$keyy][] = $rrr->field_value;
                                //
                                $columnName = strtolower($rrr->column_name);
                                //$columnName = strtolower($responseColumn[$keyy]['column_name']);

                                // if(!array_key_exists('row_id', $columnResponse[$resp->row_number]))
                                // {
                                // }
                                $columnResponse[$resp->row_number]['row_id'] = $resp->row_number;
                                $columnResponse[$resp->row_number][$columnName] = $rrr->field_value;
                                if($columnName == 'action'){
                                    $columnResponse[$resp->row_number]['action'] = 'X';
                                }                                 
                            }
                        }
                    }

                    $tablecolumnDataArray[$tttable_number] = $columnResponse;
                } 
                    $final_result = $tablecolumnDataArray;
            }

            $final_result2 = array();

            if(!empty($final_result))
            {
                foreach($final_result as $tableid => $data)
                {
                    $row_data = array();

                    $row_data['id'] = $tableid;
                    $row_data['tabledata'] = $responseColumn;
                    $row_data['RowData'] =array_values($data);
                    $final_result2[] = $row_data;
                }
            }

            //print_r($final_result); die();
            
            return response()->json([ 
                'success' => true, 
                'data' => $final_result2,
                'table_name' => $tableName->table_name,
                'table_id' => $responseTableIds,
                'table_column' => $responseColumn,
                'location' => $location,
                'success' => true,
                'message' => 'My professional list',
                'status'  => 200
            ]); 
 
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
        
    }

    /**
     * @Function: tableColumnDataCategoryList 
     **/
    public function tableColumnDataCategoryList(Request $request){
        $validator = Validator::make($request->only('user_id'), [
            'user_id'   => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $tableRecord = ProfessionalTableData::select('category_table_id')->where(['user_id' => $request->user_id])->groupBy('row_number')->get();
        if($tableRecord){
            $response = [];
            foreach($tableRecord as $res){
                $tableName = CategoryTable::where('id', $res->category_table_id)->first('table_name');
                $data['user_id'] = $request->user_id;
                $data['category_table_id'] = $res->category_table_id;
                $data['table_name'] = $tableName->table_name;
                $response[] = $data;
            }
            return response()->json([
                    'success' => true,
                    'message' => 'User table record',
                    'data'  => $response
                ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }


    /**
     * @Functoin: Table column list and data list 
     */
    public function tableColumnDataListNotAction(Request $request){
        
        $validator = Validator::make($request->only('user_id', 'category_table_id'), [
            'user_id'           => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        //colume list 
        $columnArr = array();
        $list_data = array();
        $responseColumn = [];
        /*$tableRecord = ProfessionalTableData::select('category_table_id')->where(['user_id' => $request->user_id])->groupBy('row_number')->get();*/


        $tableName = CategoryTable::where('id', $request->category_table_id)->first('table_name');
        $tableFieldsData = TableColumn::where('category_table_id', $request->category_table_id)->get();
        if(count($tableFieldsData) > 0){
            foreach($tableFieldsData as $res){
                $data = [];
                $data['id'] = $res->id;
                $data['column_name'] = $res->column_name;
                $data['column_type'] = $res->column_type;
                //$data['field_value'] = [];
                if($res->column_type == 'dropdown'){
                    if($res->column_value != ''){
                        /*$data['column_value'] = explode(',',$res->column_value);*/
                        $newarray = [];
                        $arr= explode(",", $res->column_value);
                        foreach($arr as $keys => $line){
                            $ndata['id'] = $keys;
                            $ndata['title'] = $line;
                            $newarray[] = $ndata;
                        }
                        $data['column_value'] = $newarray;
                    }
                    else{
                        $data['column_value'] = ''; 
                    }
                }
                else{
                    $data['column_value'] = $res->column_value;
                }
                $responseColumn[] = $data;
            }
            //$responseColumn[] = $sdata;
            /*$sdata = array(
                'id' => 0,
                'column_name' => 'ROW_ID',
                'column_type' => '',
                'column_value' => ''
            );
            $responseColumn[] = $sdata;*/
 
            //data list
            $tableDataRowTable_number = ProfessionalTableData::select('table_number')->where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id])->groupBy('table_number')->get();
            
            $row_id = '';
            $location = '';             
            $table_number = '';             
            $counter = 0;
            $final_result = [];
            $tablecolumnDataArray = [];
            $responseTableIds = [];
            if(count($tableDataRowTable_number) > 0){
                $response = [];
                foreach($tableDataRowTable_number as $tresp){
                    $columnResponse = array();
                    $tttable_number   = $tresp->table_number;
                    $responseTableIds[] = $tttable_number;
                    $tableDataRow = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id, 'table_number'=>$tttable_number])->groupBy('row_number')->get();

                    if(count($tableDataRow) > 0){
                        $response = [];
                        foreach($tableDataRow as $resp){
                            $row_id   = $resp->row_number;
                            $table_number   = $resp->table_number;
                            $location = $this->locationName($resp->location_id);

                            $response[] = $data;
                            $tableData = ProfessionalTableData::select('id','field_value', 'table_number', 'column_name')->where('row_number', $resp->row_number)->get();
                            // foreach($tableData as $keyy => $rrr){
                            //     $data = [];
                            //     $data['field_value']    = $rrr->field_value;
                            //     $data['table_id']       = $rrr->table_number;
                            //     $data['row_id']         = $resp->row_number;
                            //     //$columnResponse[$keyy]['field_data'] = $data;
                            //     //$columnResponse[$keyy][] = $rrr->field_value;
                            //     //
                            //     // if($columnName == 'action'){
                            //     //     $columnResponse[$resp->row_number]['action'] = 'X';
                            //     // }  
                            //     if(!is_null(@$responseColumn[$keyy]['column_name'])){
                            //         $columnName = strtolower(@$responseColumn[$keyy]['column_name']);

                            //         // if(!array_key_exists('row_id', $columnResponse[$resp->row_number]))
                            //         // {
                            //         // }
                            //         $columnResponse[$resp->row_number]['row_id'] = $resp->row_number;
                            //         $columnResponse[$resp->row_number][$columnName] = $rrr->field_value;
                            //     }
                            // }

                            foreach($tableData as $keyy => $rrr){
                                $data = [];
                                $data['field_value'] = $rrr->field_value;
                                $data['table_id'] = $rrr->table_number;
                                $data['row_id']      = $resp->row_number;
                                
                                $columnName = strtolower($rrr->column_name);
                                if($columnName == 'action'){
                                    continue; 
                                    //$columnResponse[$resp->row_number]['action'] = 'X';
                                } 
                                 
                                $columnResponse[$resp->row_number]['row_id'] = $resp->row_number;
                                $columnResponse[$resp->row_number][$columnName] = $rrr->field_value;
                            }
                            
                        }
                    }
                    $tablecolumnDataArray[$tttable_number] = $columnResponse;
                } 
                $final_result = $tablecolumnDataArray;
            }

            $final_result2 = array();
            if(!empty($final_result))
            {
                foreach($final_result as $tableid => $data)
                {
                    $row_data = array();

                    $row_data['id'] = $tableid;
                    $row_data['tabledata'] = $responseColumn;
                    $row_data['RowData'] =array_values($data);
                    $final_result2[] = $row_data;
                }
            }
            //print_r($final_result); die();
            return response()->json([ 
                'success' => true, 
                'data' => $final_result2,
                'table_name' => $tableName->table_name,
                'table_id' => $responseTableIds,
                'table_column' => $responseColumn,
                'location' => $location,
                'success' => true,
                'message' => 'My professional list',
                'status'  => 200
            ]); 
 
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }   
    }

    /**
     *@Function: Remove row 
     */
    public function removeTableRow(Request $request){
        $validator = Validator::make($request->only('user_id', 'category_table_id', 'row_id'), [
            'user_id'           => 'required',
            'row_id'            => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $affectedRow = ProfessionalTableData::where(['user_id' => $request->user_id, 'row_number' => $request->row_id, 'category_table_id' => $request->category_table_id])->delete();
        if($affectedRow){
            return response()->json([
                    'success' => true,
                    'message' => 'Table data remove successfully!',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Table data are not remove successfully. Please try again',
                    'status'  => 202
                ]);
        }
    }

    /**
     * @Search Profession 
     */
    public function searchProfessional(Request $request){
        $professionalName = '';
        $locationIds      = [];
        if($request->professional_name != ''){
            $professionalName = $request->professional_name;
        }
        if($request->locations != ''){
            $locationIds = explode(',',$request->locations);
        }
        $query = User::select('id','name','phone','place','image', 'business_name', 'experience')->where('active', 1)->where('role', 2);
        if(!empty($professionalName)){
            $query->where('business_name', 'like', '%'.$professionalName.'%');
            $query->orWhere('name', 'like', '%'.$professionalName.'%');
            $query->orWhere('phone', 'like', '%'.$professionalName.'%');
        }
        if(count($locationIds) > 0){
            $query->whereIn('place', $locationIds);
        }
        $userList = $query->get();
        $response = [];
        if(count($userList) > 0){
            foreach($userList as $res){
                $profile_img = 'uploads/user.png';
                $path1 = public_path()."/uploads/profile/".$res->image;
                if(is_file($path1)){
                    $profile_img = "uploads/profile/".$res->image;
                }
                $data['id']             = $res->id;
                $data['name']           = $res->name;
                $data['phone']          = $res->phone;
                $data['business_name']  = $res->business_name;
                $data['experience']     = $res->experience;
                $data['place']          = $this->locationName($res->place);
                $data['image']          = $profile_img;
                $response[]             = $data;
            }
            return response()->json([
                    'data'    => $response,
                    'success' => true,
                    'message' => 'Professional list!',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                    'status'  => 400
                ]);
        }
    }

    /**
     * @ Function: Live Enquiry Photographer 
     */
    public function liveEnquiryPhotographer(Request $request){
        $userRecord = ProfessionalTableData::groupBy('user_id')->pluck('user_id');
        $response = [];
        if(count($userRecord) > 0){
            $userList = User::whereIn('id', $userRecord)->where('active', 1)->get();
            if(count($userList) > 0){
                foreach($userList as $res){
                    $profile_img = 'uploads/user.png';
                    $path1 = public_path()."/uploads/profile/".$res->image;
                    if(is_file($path1)){
                        $profile_img = "uploads/profile/".$res->image;
                    }
                    $data['id']             = $res->id;
                    $data['name']           = $res->name;
                    $data['phone']          = $res->phone;
                    $data['business_name']  = $res->business_name;
                    $data['place']          = $this->locationName($res->place);
                    $data['image']          = $profile_img;
                    $response[]             = $data;
                }
                return response()->json([
                        'data'    => $response,
                        'success' => true,
                        'message' => 'Live enquiry user list',
                        'status'  => 200
                    ]);
            }
            else{
                return response()->json([
                        'success' => false,
                        'message' => 'Record not found',
                        'status'  => 404
                    ]);
            }
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                    'status'  => 404
                ]);
        }
    }

    /**
     * @ Function: Professional wise list
     */
    public function professionalWisePhotographerList(Request $request){
        $validator = Validator::make($request->only('category_table_id'), [
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $userRecord = ProfessionalTableData::where('category_table_id', $request->category_table_id)->groupBy('user_id')->pluck('user_id');
        $response = [];
        if(count($userRecord) > 0){
            $userList = User::whereIn('id', $userRecord)->where('active', 1)->get();
            if(count($userList) > 0){
                foreach($userList as $res){
                    $profile_img = 'uploads/user.png';
                    $path1 = public_path()."/uploads/profile/".$res->image;
                    if(is_file($path1)){
                        $profile_img = "uploads/profile/".$res->image;
                    }
                    $data['id']             = $res->id;
                    $data['name']           = $res->name;
                    $data['phone']          = $res->phone;
                    $data['business_name']  = $res->business_name;
                    $data['place']          = $this->locationName($res->place);
                    $data['image']          = $profile_img;
                    $response[]             = $data;
                }
                return response()->json([
                        'data'    => $response,
                        'success' => true,
                        'message' => 'Photographer list',
                        'status'  => 200
                    ]);
            }
            else{
                return response()->json([
                        'success' => false,
                        'message' => 'Record not found',
                        'status'  => 404
                    ]);
            }
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                    'status'  => 404
                ]);
        }
    }

    /**
     * @Function: Search professional wise photograper
     */
    public function searchProfessionalPhotographer(Request $request){
        $validator = Validator::make($request->only('category_table_id', 'professional_name', 'locations', 'user_id'), [
            'user_id' => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $category_tables = CategoryTable::where('id', $request->category_table_id)->first();
        if(!empty($category_tables)){
            if($category_tables->category_id == 16){
                $parent_id = $category_tables->category_id;
            }
            else{
                $parent_id = 0;
            }
        }
        else{
            $parent_id = 0;
        }
        $userRecord = ProfessionalTableData::where('category_table_id', $request->category_table_id)->groupBy('user_id')->pluck('user_id');
        $response           = [];
        $professionalName   = '';
        $locationIds        = [];
        if(count($userRecord) > 0){
            //$userList = User::whereIn('id', $userRecord)->where('active', 1)->get();
            if($request->professional_name != ''){
                $professionalName = $request->professional_name;
            }
            if($request->locations != ''){
                $locationIds = explode(',',$request->locations);
            }
            /*
            $query = User::select('id','name','phone','place','image', 'business_name', 'experience', 'created_at')->whereIn('id', $userRecord)->where('active', 1)->where('role', 2);
            */
            $query = User::select('id','name','phone','place','image', 'business_name', 'experience', 'created_at')->whereIn('id', $userRecord)->where('active', 1);
            if(!empty($professionalName)){
                $query->where('name', 'like', '%'.$professionalName.'%');
                $query->OrWhere('business_name', 'like', '%'.$professionalName.'%');
                $query->OrWhere('phone', 'like', '%'.$professionalName.'%');
            }
            if(count($locationIds) > 0){
                $query->whereIn('place', $locationIds);
            }
            $userList = $query->get();
            if(count($userList) > 0){
                foreach($userList as $res){
                    $profile_img = 'uploads/user.png';
                    $path1 = public_path()."/uploads/profile/".$res->image;
                    if(is_file($path1)){
                        $profile_img = "uploads/profile/".$res->image;
                    }
                    $data['id']             = $res->id;
                    $data['name']           = $res->name;
                    $data['phone']          = $res->phone;
                    $data['business_name']  = $res->business_name;
                    $data['experience']     = $res->experience;
                    $data['place']          = $this->locationName($res->place);
                    $data['image']          = $profile_img;
                    $data['parent_id']      = $parent_id;
                    $data['category_table_id']  = $request->category_table_id;
                    $data['date_time']      = $this->dateTimeFormate($res->created_at);
                    $response[]             = $data;
                }
                return response()->json([
                        'data'    => $response,
                        'success' => true,
                        'message' => 'Photographer list',
                        'status'  => 200
                    ]);
            }
            else{
                return response()->json([
                        'success' => false,
                        'message' => 'Record not found',
                        'status'  => 404
                    ]);
            }
        }
        else{
            return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                    'status'  => 404
                ]);
        }
    }

    /**
     * @Function: photographerProfessionalDetails
     */
    public function photographerProfessionalDetails(Request $request){
        $validator = Validator::make($request->only('category_table_id', 'user_id'), [
            'user_id' => 'required',
            'category_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'message' => $validator->messages()->first()], 200);
        }
        $tableDataRow = ProfessionalTableData::where(['user_id' => $request->user_id, 'category_table_id' => $request->category_table_id])->groupBy('row_number')->get();
        $row_id = '';
        $location = '';
        $userDetails = User::select('id','email','name','phone','place','image', 'business_name', 'experience')->where('id', $request->user_id)->first();
        if(count($tableDataRow) > 0){
            $response = [];
            $rowData = [];
            foreach($tableDataRow as $resp){
                $row_id   = $resp->row_number;
                $location = $this->locationName($resp->location_id);
                $tableData = ProfessionalTableData::select('field_value', 'column_name')->where('row_number', $resp->row_number)->get();
                $fdata = [];
                foreach($tableData as $rrr){
                    $fdata[$rrr->column_name] = $rrr->field_value;
                    //$data['row_id']      = $resp->row_number;
                    //$response[] = $fdata;
                }
                $rowData['row'] = $fdata;
                $response[] = $fdata;
            }
            $userDetail = [];
            $profile_img = 'uploads/user.png';
            $path1 = public_path()."/uploads/profile/".$userDetails->image;
            if(is_file($path1)){
                $profile_img = "uploads/profile/".$userDetails->image;
            }
            $userDetail['user_id'] = $userDetails->id;
            $userDetail['name'] = $userDetails->name;
            $userDetail['email'] = $userDetails->email;
            $userDetail['phone'] = $userDetails->phone;
            $userDetail['place'] = $this->locationName($userDetails->place);
            $userDetail['image'] = $profile_img;
            $userDetail['business_name'] = $userDetails->business_name;
            $userDetail['experience'] = $userDetails->experience;
            return response()->json([
                    'data'     => $response,
                    'location' => $location,
                    'userDetails' => $userDetail,
                    'success' => true,
                    'message' => 'Photographer professional data',
                    'status'  => 200
                ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
                'status'  => 400
            ]);
        }
    }
    
    /****   Date formate   ****/
    public function dateTimeFormate($date)
    {
        return date('M d, Y | H:i a', strtotime($date));
    }

    /**
    *Function@ Send SMS
    */
    public function smsSendSignUp($otp, $number){
        $uname = "lokendrakushwah";
        $upass = "lok23";
        $senderid = "MORVIA";
        $msg = "Welcome to the MORVIA. Use OTP ".$otp." to Sign Up into your account. Do not share the OTP or your number with anyone.";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://smsl.myappstores.com/api/mt/SendSMS?user=lokendrakushwah&password=lok23&senderid=MORVIA&channel=Trans&DCS=0&flashsms=0&number=$number&text=Thank%20you%20for%20choosing%20Morvi%20Application.%20We%20value%20your%20security%20and%20want%20to%20ensure%20the%20protection%20of%20your%20account.%20As%20part%20of%20our%20verification%20process%2C%20we%20require%20you%20to%20confirm%20your%20account%20using%20a%20One-Time%20Password%20(OTP).%20Please%20find%20below%20the%20OTP%20needed%20to%20verify%20your%20account%3A%20Your%20OTP%20is%20$otp&route=8",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            return false;
        } else {
            return true;
        }
    }
    /**
    *Function@ Send SMS
    */
    public function smsSend($otp, $number){
        $uname = "lokendrakushwah";
        $upass = "lok23";
        $senderid = "MORVIA";
        $msg = "Welcome to the MORVIA. Use OTP ".$otp." to Sign Up into your account. Do not share the OTP or your number with anyone.";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://smsl.myappstores.com/api/mt/SendSMS?user=lokendrakushwah&password=lok23&senderid=MORVIA&channel=Trans&DCS=0&flashsms=0&number=$number&text=Welcome%20to%20the%20Morvi%20Application%20Use%20OTP%20$otp%20to%20reseting%20your%20Password.%20Do%20not%20share%20the%20OTP%20or%20your%20Mobile%20number%20with%20anyone.&route=8",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            return false;
        } else {
            return true;
        }
    }
    public function uploadImagePost(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                'image' => ['required'],
                // 'text' => ['required'],
                'user_id' => ['required'],
                'location' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $post = $request->all();
            $imagesP=[];
            if($request->hasfile('image')){
                foreach($request->file('image') as $file)
                {
                    $randomNum = rand(10,100);
                    $img = time().$randomNum.'.'.$file->getClientOriginalExtension();
                    $imagesP[] = $img;
                    $destinationPath = base_path('public/post');
                    $file->move($destinationPath, $img);

                    // /******** Create thumbnail image **********/
                    // $videoPath = storage_path('public/thumbnails/' . $this->video_path);
                    // $thumbnailPath = 'thumbnails/' . $this->id . '.jpg'; // Set the thumbnail path as needed

                    // // Create an instance of Intervention Image
                    // $image = Image::make($videoPath);

                    // // Crop the image or resize it as needed
                    // $image->fit(200, 150);

                    // // Save the thumbnail image
                    // $image->save(storage_path('public/' . $thumbnailPath));

                    // $this->thumbnail_path = $thumbnailPath;
                    // $this->save();
                    // /***** //end *****/
                }
                $NewImage = implode(',', $imagesP);
                $post['image'] =  $NewImage;
            }
            $post =  Post::create($post);
            if(!empty($post)){
                return response()->json = [
                    'status' => True,
                    'message' => 'Post Added Successfully',
                ];
            }
            else{
                return response()->json = [
                    'status' => false,
                    'message' => 'Not Added',
                ];
            }

        }catch(Exception $ex){
            return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }    
    }
    public function videoPost(Request $request){
        try{
           
            $data = $request->only('video', 'text', 'user_id', 'location');
            $validator = Validator::make($data, [
                'video.*' => 'mimes:mp4,mov,ogg,qt | max:51200',
                // 'text' => 'required',
                'user_id' => 'required',
                'location' => 'required',
            ],[
                'video.mimes' => "The video must be a file of type: mp4, mov, ogg, qt.",
                'video.max' => "The video must be smallerthan from 50 mb.",
            ]);
             if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $post = $request->all();
            $videoPath=[];
            if($request->hasfile('video')){
                    foreach($request->file('video') as $file)
                    {
                        $randomNum = rand(10,100);
                        $vid = time().$randomNum.'.'.$file->getClientOriginalExtension();
                        $videoPath[] = $vid;
                        $destinationPath = base_path('public/post');
                        $file->move($destinationPath, $vid);
                    }
                    $NewVideo = implode(',', $videoPath);
                    $post['video'] =  $NewVideo;
               }
                $post =  Post::create($post);
                if(!empty($post)){
                    return response()->json = [
                      'status' => True,
                      'message' => 'Post Added Successfully',
                  ];
                }
                else{
                    return response()->json = [
                      'status' => false,
                      'message' => 'Not Added',
                  ];
                }

        }catch(Exception $ex){
            return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    public function getPostByUserID(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                 'user_id' => ['required'], 
                 'page' => ['required'],
                 'limit' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $checkUserFollow = DB::table('follow_unfollows')->select('user_id')->where(['follower_id' => $request->user_id ,'status' => 1,'type' => 0])->pluck('user_id');

            if(count($checkUserFollow)== 0){
                return response()->json = [
                      'status' => false,
                      'message' => 'you are not follow this user',
                  ];
            }else{
            $allimg[] = '';
            //$imgArr = '';
            $response = [];
            $basepath = url('/post/');
            $newlikeCounts =0; 
            if(!empty($request->self_id)){
              $getAllPosts = Post::select('posts.*','posts.id as post_id','posts.user_id as user_id','users.name','users.name','users.email','users.phone','users.address','users.role','users.active','users.follower_count','users.image as user_image','posts.created_at as post_created_at')
                           ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                           ->where('posts.user_id',$request->self_id)
                           ->latest('posts.id')->paginate($request->limit);
            }else{
                $getAllPosts =Post::select('follow_unfollows.status as follow_status', 'follow_unfollows.post_id', 'master_cities.city_name as location_name', 'posts.*', 'users.name', 'users.email', 'users.phone', 'users.address', 'users.role', 'users.active', 'users.follower_count', 'users.image as user_image', 'posts.created_at as post_created_at')
                           ->leftJoin('users', 'users.id', '=', 'posts.user_id')
                            ->leftJoin('follow_unfollows', 'follow_unfollows.user_id', '=', 'users.id')
                           ->leftJoin('master_cities', 'master_cities.id', '=', 'posts.location')
                           ->whereIn('follow_unfollows.user_id', $checkUserFollow)
                           ->orWhere('follow_unfollows.user_id', $request->user_id)
                           ->groupBy('posts.id')
                           ->latest('posts.id')->paginate($request->limit); 
            }
            $videoThumnail =  $basepath.'/thumbnail.png';
            foreach($getAllPosts as $gtpost){
                $likeCounts = DB::table('follow_unfollows')->where(['user_id'=> $gtpost->user_id,'post_id'=>           $gtpost->post_id,'type' => 1])->count();
                if($likeCounts > 999){
                    $newlikeCounts = $likeCounts.'k';
                }
                else{
                    $newlikeCounts = $likeCounts;
                }
                $commentCount = Comment::where(['post_id'=>           
                    $gtpost->post_id])->count();
                $dbImage = $gtpost['image'];
                $dbVideo = $gtpost ['video'];
                $videoArr = explode(",", $dbVideo);
                $imgArr = explode(",", $dbImage);
                $imgPostArr = [];
                $videoPostArr = [];
                $videoThumnailPostArr = [];
                if(!is_null($gtpost['image'])){
                    if(is_array($imgArr)){
                        foreach($imgArr as $img){
                            if(!empty($img)){
                                array_push($imgPostArr, $basepath.'/'.$img);
                            }
                        }
                    }
                    else{
                        array_push($imgPostArr, $basepath.'/'.$gtpost['image']);
                    }
                }
                if(!is_null($gtpost['video'])){
                    if(is_array($videoArr)){
                        foreach($videoArr as $img){
                            if(!empty($img)){
                                array_push($videoPostArr, $basepath.'/'.$img);
                                array_push($videoThumnailPostArr,$videoThumnail);
                            }
                        }
                    }
                    else{
                        array_push($imgPostArr, $basepath.'/'.$gtpost['video']);
                        array_push($videoThumnailPostArr,$videoThumnail);
                    }
                }
                $data['id'] = $gtpost['id'];
                if(count($imgPostArr) > 0){
                 $data['media_type'] = '1'; 
                }else{
                  $data['media_type'] = '2';  
                }
                $data['image'] = $imgPostArr;
                $data['video'] = $videoPostArr;
                $data['video_thum'] = $videoThumnailPostArr;
                $data['text']  = $gtpost['text'];
                $data['user_id'] = $gtpost['user_id'];
                $data['location'] = $gtpost['location'];
                // $data['location'] = $gtpost['location_name'];
                $data['likes']  = $gtpost['likes'];
                $data['comments']  = $gtpost['comments'];
                $data['created_at']  = date('d M, Y',strtotime($gtpost['created_at']));
                $data['name'] = $gtpost['name'];
                $data['email'] = $gtpost['email'];
                $data['phone'] = $gtpost['phone'];
                $data['user_image'] = 'uploads/profile/'.$gtpost['user_image'];
                $data['address'] = $gtpost['address'];
                $data['active'] = $gtpost['active'];
                $data['role'] = $gtpost['role'];
                $data['is_follower'] = $this->isFav($gtpost['user_id'],$request->login_user_id,$gtpost['post_id'],'0');
                $data['is_fav'] = $this->isFav($gtpost['user_id'],$request->login_user_id,$gtpost['post_id'],'2');
                $data['is_like'] = $this->isFav($gtpost['user_id'],$request->login_user_id,$gtpost['post_id'],'1');
                $data['posted_date'] = $this->getDateInFormat($gtpost); 
                $data['follower_count'] =  $this->getCountOfFollowerAndFollowing($type = 1,$request->user_id);
                $data['following_count'] =  $this->getCountOfFollowerAndFollowing($type = 2,$request->user_id);
                $data['likes_count'] = $newlikeCounts;
                $data['comment_count'] = $commentCount;
                $response[] = $data;
                unset($imgArr);
                unset($videoArr);
             }
             if(count($getAllPosts) > 0){
                    return response()->json = [
                      'status' => True,
                      'data' => $response,
                      'message' => 'Get post successfully',
                  ];
                }
                else{
                    return response()->json = [
                      'status' => false,
                      'message' => 'Not Post available',
                  ];
                }
            }
            
        }catch(Exception $ex){
            return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    
    public function updatePost(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'user_id' => ['required'],
                'location' => ['required'],
                //'text' => ['required'],

            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $post = $request->all();
            $imagesP=[];
            if($request->hasfile('image')){
                foreach($request->file('image') as $file)
                {            
                    $randomNum = rand(10,100);
                    $img = time().$randomNum.'.'.$file->getClientOriginalExtension();
                    $imagesP[] = $img;
                    $destinationPath = base_path('public/post');
                    $file->move($destinationPath, $img);
                }
                    $NewImage = implode(',', $imagesP);
                    $post['image'] =  $NewImage;
               }
            $videoPath=[];
            if($request->hasfile('video')){
                foreach($request->file('video') as $file)
                {
                    $randomNum = rand(10,100);
                    $vid = time().$randomNum.'.'.$file->getClientOriginalExtension();
                    $videoPath[] = $vid;
                    $destinationPath = base_path('public/post');
                    $file->move($destinationPath, $vid);
                }
                    $NewVideo = implode(',', $videoPath);
                    $post['video'] =  $NewVideo;
               }   
               $id = $request->id;
               $updatePost = Post::find($id);
               if(!empty($updatePost)){
                 $newUpdate = $updatePost->update($post);
               }else{
                 return response()->json = [
                      'status' => false,
                      'message' => 'Post not found',
                  ];
               }
               if(!empty($newUpdate)){
                 return response()->json = [
                      'status' => true,
                      'message' => 'Post updated successfully',
                  ];
               }else{
                return response()->json = [
                      'status' => false,
                      'message' => 'Post not found',
                  ];
               }

        }catch(Exception $ex){
          return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    public function deletePost(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $deletePost = Post::where('id',$request->id)->where('user_id',$request->user_id)->delete();
            if(!empty($deletePost)){
                return response()->json = [
                      'status' => True,
                      'message' => 'Post deleted successfully',
                  ];
              }else{
                return response()->json = [
                      'status' => false,
                      'message' => 'Post not found',
                  ];
              }

        }catch(Exception $ex){
            return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    public function listOfFriend(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $search = $request->search;
            $query = DB::table('users')->select('users.id','users.place','users.name','users.email','users.image','users.address','users.phone','users.business_name','users.created_at');
            if(!empty($search)){
               $query->where('name', 'like', $search. '%');
            }else{
                $query->where('role','=',2);
            }
            $query->where('id','!=',$request->user_id);
            $friendList = $query->get();
            if(count($friendList) > 0){
               $response = [];
               foreach($friendList as $newDt){
                  $data['id'] = $newDt->id;
                  $data['name'] = $newDt->name;
                  $data['email'] = $newDt->email;
                  $data['image'] = 'uploads/profile/'.$newDt->image;
                  $data['address'] = $newDt->address;
                  $data['phone'] = $newDt->phone;
                  $data['business_name'] = $newDt->business_name;
                  $data['created_at'] = $newDt->created_at;
                  $data['location'] = $this->getUserLocation($newDt->place);

                  $response[] = $data;
               }
               return response()->json = [
                      'status' => True,
                       'data' => $response,
                      'message' => 'Get successfully',
                  ];
            }else{
                 return response()->json = [
                      'status' => false,
                      'message' => 'No data available',
                  ];
            }

        }catch(Exception $ex){
             return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    public function friendsDetials(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                 'user_id' => ['required'],
                 'login_user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }
            $userID = $request->user_id;
            $details = User::select('users.id','users.name','users.email','users.phone','users.image',             'users.address','users.place','users.business_name','users.follower_count',             'users.created_at','users.about_business','users.experience','users.follower_count')
                             ->where('users.id',$userID)->groupBy('users.id')->get()->toArray();

            // Convert null values to empty strings in the $details array
                $details = array_map(function ($item) {
                    return array_map(function ($value) {
                        return is_null($value) ? '' : $value;
                    }, $item);
                }, $details);
             if(count($details) > 0){
                 $response = [];
               foreach($details as $newDt){
                   $following_count = DB::table('follow_unfollows')->select('user_id')
                                     ->where('follower_id',$userID)->where('type',0)->count();
                  $is_friend = DB::table('follow_unfollows')->where(['user_id' => $request->login_user_id, 'follower_id' => $request->user_id])->count(); 
                  $categories = DB::table('user_professionals')->select('categoriess.id','categoriess.category_name')
                             ->leftJoin('categoriess', 'categoriess.id', '=', 'user_professionals.category_id')
                             ->where('user_professionals.user_id',$newDt['id'])
                            ->get();                  
                  $data['id'] = $newDt['id'];
                  $data['name'] = $newDt['name'];
                  $data['email'] = $newDt['email'];
                  $data['image'] = 'uploads/profile/'.$newDt['image'];
                  $data['address'] = $newDt['address'];
                  $data['place'] = $newDt['place'];
                  $data['business_name'] = $newDt['business_name'];
                  $data['created_at'] = $newDt['created_at'];
                  $data['about_business'] = $newDt['about_business'];
                  $data['experience'] = $newDt['experience'];
                  $data['is_friend'] = $is_friend;
                  if($newDt['follower_count'] > 0){
                    $data['follower_count'] = $newDt['follower_count'];
                  }
                  else{
                    $data['follower_count'] = 0; 
                  }
                  if($following_count > 0){
                    $data['following_count'] = $following_count;
                  }
                  else{
                    $data['following_count'] = 0;
                  }
                  $data['categories'] = $categories;
                  $response[] = $data;
               }
               return response()->json = [
                      'status' => True,
                       'data' => $response,
                      'message' => 'Get friend detail successfully',
                  ];
            }else{
                 return response()->json = [
                      'status' => false,
                      'message' => 'No data available',
                  ];
            }
        }catch(Exception $ex){
            return response()->json = [
                'status' => false,
                'message' => $ex->getMessage(),
             ];
        }
    }
    public function getLatestProduct(Request $request){
        try{
          $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
           ]);
          if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
           }
            $details = User::select('user_sell_products.*')
                           ->leftJoin('user_sell_products', 'user_sell_products.user_id', '=', 'users.id')
                           ->where('users.id',$request->user_id)->latest()->take(5)->get()->toArray();
         if(count($details) > 0){
           return response()->json = [
                  'status' => True,
                   'data' => $details,
                  'message' => 'Get latest 5 product successfully',
                  ];
            }else{
             return response()->json = [
                  'status' => false,
                  'message' => 'No data available',
              ];
            }             
        }catch(Exception $ex){
            return response()->json = [
            'status' => false,
            'message' => $ex->getMessage(),
          ]; 
        }
    }
    public function followUnfollow(Request $request){
        try{
            $validator = Validator::make($request->all(), [
            'user_id' => ['required','exists:users,id'], 
            'follower_id' => ['required','exists:users,id'],
           ]);
          if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
           }  
           $post = $request->all();
           $getUserName = User::select('name')->where('id',$request->user_id)->first();
           $getDeviceID = User::select('device_id')->where('id',$request->follower_id)->first();
           $post = $request->all(); 
           $updateFollowerCountData = DB::table('users')
                                 ->where('id',$request->follower_id)
                                 ->first();
           if(!empty($updateFollowerCountData)){
              $updateFollowerCount = $updateFollowerCountData->follower_count;
           }else{
            $updateFollowerCount = 0;
           }
           $followerCount = '';
           $dt = DB::table('follow_unfollows')->where(['user_id'=> $request->user_id,'follower_id'=>$request->follower_id,'type' =>0])->count();
           if($dt > 0){
            $data = DB::table('follow_unfollows')->where(['user_id'=> $request->user_id,'follower_id'=>$request->follower_id])->where('type',0)->delete();

            $deleteNotification = DB::table('notifications')->where(['sender_id'=> $request->user_id,'receiver_id'=>$request->follower_id])->delete();

            if(!empty($data)){
             $followerCount = $updateFollowerCount - 1;
             $followers = DB::table('users')->where('id', $request->follower_id)->update(['follower_count' => $followerCount]);
              return response()->json = [
               'status' => true,
               'message' => 'Unfollow successfully',
              ];     
            }
            else{
             return response()->json = [
               'status' => false,
               'message' => 'User not found',
              ];      
            }
           }
           else{
            $data = DB::table('follow_unfollows')->insert($post);
            $id = DB::getPdo()->lastInsertId();
            if(!empty($getDeviceID)){
            $title =  $getUserName->name . ' requested to follow you';
            $messageData['title']           = $title;
            $messageData['message']         = $getUserName->name .' '.$this->notificationMessage('1');    
            $messageData['user_id']         = $request->user_id;
            $messageData['id']              = $id;
            $messageData['notify_type']     = 1; // 1 for following notification
            $messageData['redirection']     = $id;
            $messageData['image']           = '';
            $device_id = $getDeviceID->device_id;
            $send = $this->sendNotification($device_id, $title, $messageData);
            if($send){
                $data = array(
                    'type' => 1,
                    'notifiable_type' => 1,
                    'data' => json_encode($messageData),
                    'sender_id' => $post['user_id'],
                    'receiver_id' => $request->follower_id,
                    'notifiable_id' => $id,
                );
                DB::table('notifications')->insert($data);
            }
           }
            $followerCount = $updateFollowerCount + 1;
            $followers = DB::table('users')->where('id', $request->follower_id)->update(['follower_count' => $followerCount]);
            if(!empty($data)){
              return response()->json = [
               'status' => true,
               'message' => 'Follow successfully',
              ];     
            }
           }
        }catch(Exception $ex){
           return response()->json = [
            'status' => false,
            'message' => $ex->getMessage(),
          ];  
        }
    }
    public function likeDislike(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'follower_id' => ['required'],
                'post_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }  
            $post = $request->all(); 
            $getUser = DB::table('posts')->select('user_id')->where('id',$request->post_id)->first();
            $getUserName = DB::table('users')->select('name')->where('id',$request->follower_id)->first();
            if(!is_null($getUser)){
                $dt = DB::table('follow_unfollows')->where(['follower_id' => $request->follower_id,'post_id' => $request->post_id,'user_id'=>$getUser->user_id,'type' => 1])->count();
                if($dt > 0){
                    $data = DB::table('follow_unfollows')->where(['follower_id' => $request->follower_id,'post_id' => $request->post_id,'user_id'=>$getUser->user_id,'type' => 1])->delete();
                    $deleteNotification = DB::table('notifications')->where(['sender_id'=> $request->follower_id,'receiver_id'=>$getUser->user_id,'notifiable_id' => $request->post_id])->delete();
                    if(!empty($data)){
                        return response()->json = [
                          'status' => true,
                          'message' => 'Dislike successfully',
                        ]; 
                    }
                }
                else{
                    $disLike[] = [
                     'follower_id' => $post['follower_id'],
                     'user_id' => $getUser->user_id,
                     'post_id' => $post['post_id'],
                     'type' => 1
                   ];
                     $like = DB::table('follow_unfollows')->insert($disLike);
                     $getDeviceID = User::select('device_id')->where('id',$getUser->user_id)->first();
                     if(!empty($getDeviceID)){
                        $title =  $getUserName->name . ' like your post';
                        $messageData['title']           = $title;
                        $messageData['message']         = $getUserName->name .' '.$this->notificationMessage('2');    
                        $messageData['user_id']         = $request->follower_id;
                        $messageData['id']              = $request->post_id;
                        $messageData['notify_type']     = 2; // 2 for like
                        $messageData['redirection']     = $request->post_id;
                        $messageData['image']           = '';
                        $device_id = $getDeviceID->device_id;
                        $send = $this->sendNotification($device_id, $title, $messageData);
                        if($send){
                            $data = array(
                                'type' => 2,
                                'notifiable_type' => 2,
                                'data' => json_encode($messageData),
                                'sender_id' => $post['follower_id'],
                                'receiver_id' => $getUser->user_id,
                                'notifiable_id' => $request->post_id,
                            );
                            DB::table('notifications')->insert($data);
                        }
                       }
                      if(!empty($like)){
                        return response()->json = [
                          'status' => true,
                          'message' => 'Like successfully',
                          ]; 
                    }
                }
            }
            else{
                return response()->json = [
                          'status' => false,
                          'message' => 'Post not found',
                      ]; 
            }
        }catch(Exception $ex){
            return response()->json = [
                    'status' => false,
                    'message' => $ex->getMessage(),
                ]; 
        }
    }
    public function checkFavorite(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'follower_id' => ['required'],
                'post_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            } 
            $userData = DB::table('posts')->select('user_id')->where('id',$request->post_id)->first();
            if(!is_null($userData)){
                $user_id = $userData->user_id;
                $post = $request->all();
                $fav = DB::table('follow_unfollows')->where(['type'=> 2, 'follower_id'=> $request->follower_id, 'user_id' => $user_id,'post_id'=> $request->post_id])->count();
                if($fav > 0){
                    $unFav = DB::table('follow_unfollows')->where(['type'=> 2, 'user_id' => $user_id,'follower_id'=> $request->follower_id,'post_id'=> $request->post_id])->delete();
                    if(!empty($unFav)){
                        return response()->json = [
                            'status' => true,
                            'message' => 'Unfavorite successfully',
                        ]; 
                    }
                }
                else{
                    $post['type'] = 2;
                    $post['user_id'] = $user_id;
                    $like = DB::table('follow_unfollows')->insert($post);
                    if(!empty($like)){
                        return response()->json = [
                            'status' => true,
                            'message' => 'Favorite successfully',
                        ]; 
                    }
                }
            }else{
                return response()->json = [
                  'status' => false,
                  'message' => 'Post not found',
                  ];  
            }
                
                        
        }catch(Exception $ex){
         return response()->json = [
            'status' => false,
            'message' => $ex->getMessage(),
          ];  
        }
    }
    public function mediaList(Request $request){
    try{
      if($request->type > 0){
         $validator = Validator::make($request->all(), [
        'user_id' => ['required'],
        'post_id' => ['required'],
        'type' => ['required'],
       ]);
         }else{
             $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'type' => ['required'],
           ]);
          }
          if($request->type == 0){
            $message = 'Get followers list successfully';
          }elseif($request->type == 1){
            $message = 'Get like list successfully';
          }
          else{
            $message = 'Get Favorite list successfully';
          }
           if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
           } 
           if($request->type >0){
              $data = DB::table('follow_unfollows')->select('follow_unfollows.follower_id','users.name')
                 ->leftJoin('users', 'users.id', '=', 'follow_unfollows.follower_id')
                 ->where(['user_id'=> $request->user_id,'post_id'=> $request->post_id,'type'=>$request->type])->get();
           }
           else{
             $data = DB::table('follow_unfollows')->select('follow_unfollows.follower_id','users.name')
                 ->leftJoin('users', 'users.id', '=', 'follow_unfollows.follower_id')
                 ->where(['user_id'=> $request->user_id,'type'=>$request->type])->get();
           }
          if(count($data) > 0){
            return response()->json = [
             'status' => true,
             'data' => $data,
             'message' => $message,
           ];  
          }
          else{
            return response()->json = [
             'status' => false,
             'message' => 'Record not found'
           ];  
          }
     }catch(Exception $ex){
      return response()->json = [
        'status' => false,
        'message' => $ex->getMessage(),
      ];   
     }
    }
    public function followerList(Request $request){
        try{
          $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            }  
            $query =  DB::table('follow_unfollows')->select('follow_unfollows.follower_id','users.name','users.email','users.image','users.address','users.phone','users.business_name','users.place','users.created_at','follow_unfollows.status')
               ->leftJoin('users', 'users.id', '=', 'follow_unfollows.follower_id');
            if(!empty($request->search)){
                         $query->where('users.name', 'like', $request->search. '%')
                                ->where(['follow_unfollows.user_id'=> $request->user_id,'type' => 0]);
            }else{
                 $query->where(['follow_unfollows.user_id'=> $request->user_id,'type' => 0]);
            }
            $followerData = $query->groupBy('follower_id')->get();
            if(count($followerData) > 0){
             foreach($followerData as $dt){
                if($dt->status == 0){
                    $dt->status = 'pending';
                }else{
                    $dt->status = "accepted";
                }
                $data['follower_id'] = $dt->follower_id;
                $data['name']  = $dt->name;
                $data['image']  = 'uploads/profile/'.$dt->image;
                $data['email'] = $dt->email;
                $data['address'] = $dt->address;
                $data['phone']  = $dt->phone;
                $data['business_name']  = $dt->business_name;
                $data['status']  = $dt->status;
                $data['created_at']  = date('d-M-y',strtotime($dt->created_at));
                $data['location'] = $this->getUserLocation($dt->place);
                $response[] = $data;
                }
              return response()->json = [
               'status' => True,
               'data' => $response,
               'message' => 'Get Follower List Successfully',
             ];  
            }else{
              return response()->json = [
               'status' => false,
               'message' => 'Follower not found',
             ];   
            }
        }catch(Exception $ex){
          return response()->json = [
           'status' => false,
           'message' => $ex->getMessage(),
         ];     
        }
    }
    public function followingList(Request $request){
        try{
         $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            } 
            $query = DB::table('follow_unfollows')->select('follow_unfollows.user_id','users.name',            'users.email','users.image','users.address','users.phone','users.business_name',            'users.place','users.created_at','follow_unfollows.status')
                            ->leftJoin('users', 'users.id', '=', 'follow_unfollows.user_id');
            if(!empty($request->search)){
                    $query->where('users.name', 'like', $request->search. '%');
                    $query->where(['follow_unfollows.user_id'=> $request->user_id,'status' => 1]);
            }else{
             $query->where(['follow_unfollows.follower_id'=> $request->user_id,'status' => 1,'type' =>0]);
            }
            $response = [];
            $followingData =  $query->get();
            // echo "<pre>";
            // print_r($followingData);die;
            if(count($followingData) > 0){
             foreach($followingData as $dt){
                if($dt->status == 0){
                    $dt->status = 'pending';
                }else{
                    $dt->status = "accepted";
                }
                $data['following_id'] = $dt->user_id;
                $data['name']  = $dt->name;
                $data['image']  = 'uploads/profile/'.$dt->image;
                $data['email'] = $dt->email;
                $data['address'] = $dt->address;
                $data['phone']  = $dt->phone;
                $data['status']  = $dt->status;
                $data['business_name']  = $dt->business_name;
                $data['created_at']  = date('d-M-y',strtotime($dt->created_at));
                $data['location'] = $this->getUserLocation($dt->place);
                $response[] = $data;
                }
              
             return response()->json = [
               'status' => True,
               'data' => $response,
               'message' => 'Get Following List Successfully',
             ];
            }else{
               return response()->json = [
               'status' => false,
               'message' => 'Following not found',
             ];   
            }          
        }catch(Exception $ex){
            return response()->json = [
           'status' => false,
           'message' => $ex->getMessage(),
          ];  
        }
    }
    public function removeImgAndVideo(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id' => ['required'],
                'name' => ['required'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
            } 
          $postData = Post::where('id', $request->post_id)
                    ->whereRaw("FIND_IN_SET(?, image) > 0", [$request->name])
                    ->first();
          if(!is_null($postData)){
             $storeVidoes = $postData['video'];
            $storeImages = $postData['image'];
            $postData->first();
          }else{
             return response()->json = [
                 'status' => false,
                 'message' => 'Post not found',
                ];
          }
          if(!is_null($postData->image)){
            $arrayImg = explode(",",$postData->image);
            foreach (array_keys($arrayImg, $request->name) as $key) {
                unset($arrayImg[$key]);
            }
             $postData->image = implode(',',$arrayImg);
          }
          if(!is_null($postData->video)){
              $arrayVideo = explode(",",$postData->video);
            foreach (array_keys($arrayVideo, $request->video) as $key) {
                unset($arrayImg[$key]);
            }
             $postData->video = implode(',',$arrayVideo);
          }
          $data = $postData->save();
          if($data){
            return response()->json = [
                  'status' => true,
                  'message' => 'Remove Successfully',
              ];
          }
          else{
             return response()->json = [
                      'status' => false,
                      'message' => 'Post not found',
                  ];
          }
        }catch(Exception $ex){
            return response()->json = [
           'status' => false,
           'message' => $ex->getMessage(),
          ]; 
        }
    }
    public function comment(Request $request){
     try{
       $validator = Validator::make($request->all(), [
            'post_id' => ['required'],
            'user_id' => ['required','exists:users,id'],
            'comment' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
        }
        $getUserName = User::select('name')->where('id',$request->user_id)->first();
        $checkPostId = Post::find($request->post_id);
        if(!empty($checkPostId)){
             $comments = Comment::create($request->all());
             $getDeviceID = User::select('device_id')->where('id',$checkPostId->user_id)->first();
                     if(!empty($getDeviceID)){
                        $title =  $getUserName->name . ' commented on your post';
                        $messageData['title']           = $title;
                        $messageData['message']         = $getUserName->name .' '.$this->notificationMessage('2');    
                        $messageData['user_id']         = $request->user_id;
                        $messageData['id']              = $request->post_id;
                        $messageData['notify_type']     = 3; // 3 for comment on any post
                        $messageData['redirection']     = $request->post_id;
                        $messageData['image']           = '';
                        $device_id = $getDeviceID->device_id;
                        $send = $this->sendNotification($device_id, $title, $messageData);
                        if($send){
                            $data = array(
                                'type' => 3,
                                'notifiable_type' => 3,
                                'data' => json_encode($messageData),
                                'sender_id' => $request->user_id,
                                'receiver_id' => $checkPostId->user_id,
                                'notifiable_id' => $request->post_id,
                            );
                            DB::table('notifications')->insert($data);
                        }
                       }
        if(!empty($comments)){
         return response()->json = [
                  'status' => true,
                  'message' => 'Comment added successfully.',
              ];
        }else{
           return response()->json = [
                  'status' => false,
                  'message' => 'Comment not added.',
              ];
        } 
    }else{
     return response()->json = [
      'status' => false,
        'message' => 'This post is not found.',
      ];
    }
     }catch(Exception $ex){
        return $ex->getMessage();
     }
    }
    public function commentList(Request $request){
     try{
        $validator = Validator::make($request->all(), [
            'post_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
        }
       $getAllComment = Comment::select('posts.user_id as post_user_id','users.name','comments.comment','users.image','comments.user_id as comment_user_id','comments.created_at','comments.id')
                                  ->leftJoin('users', 'users.id', '=', 'comments.user_id')
                                  ->leftJoin('posts', 'posts.id', '=', 'comments.post_id')
                                  ->where('posts.id',$request->post_id)
                                  ->get();
      if(count($getAllComment) > 0){
        $response = [];
               foreach($getAllComment as $newDt){
                $commentCounts = DB::table('comment_like_dislike')->where('comment_id',$newDt->id)->count();
                if($commentCounts > 999){
                    $newcommentCounts = $commentCounts.'k';
                }
                else{
                    $newcommentCounts = $commentCounts;
                }
                  $data['comment_id'] = $newDt->id;
                  $data['user_id'] = $newDt->comment_user_id;
                  $data['user_name'] = $newDt->name;
                  $data['comment'] = $newDt->comment;
                  if($newDt->image){
                   $data['image'] = 'uploads/profile/'.$newDt->image;
                  }else{
                    $data['image'] = 'uploads/profile/user.jpg';
                  }
                  $data['time'] = $this->getDateInFormat($newDt); 
                  $data['comment_like_counts'] = $commentCounts;
                  $data['is_like'] = $this->checkCommentStatus($newDt->id , $newDt->comment_user_id); 
                  $response[] = $data;
               }
           return response()->json = [
                      'status' => true,
                       'data' => $response,
                      'message' => 'Get all comment successfully.',
                  ];
          }else{
            return response()->json = [
                      'status' => false,
                      'message' => 'Comment not found.',
                  ];
           }
     }catch(Exception $ex){
        return $ex->getMessage();
     }
    }
    function checkCommentStatus($comment_id , $user_id){
      $checkUSerId = DB::table('comment_like_dislike')->where(['comment_id' => $comment_id,'user_id'=>$user_id])->count();
      if($checkUSerId){
        return '1';
      }else{
        return '0';
      }
    }
    public function commentLikeDislike(Request $request){
    try{
     $validator = Validator::make($request->all(), [
            'comment_id' => ['required','exists:comments,id'],
            'user_id' => ['required','exists:users,id'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
        }
         $getUserName = User::select('name')->where('id',$request->user_id)->first();
         $getUserIdOfComment = Comment::select('post_id','comments.user_id as user_id')
                              ->leftJoin('posts', 'posts.user_id', '=', 'comments.user_id')
                              ->where('comments.id',$request->comment_id)->first();
         $checkId = CommentLikeDislike::where(['comment_id' => $request->comment_id ,
                  'user_id' => $request->user_id])->first();
         if(is_null($checkId)){
          $likeComment = CommentLikeDislike::create($request->all());
          $getDeviceID = User::select('device_id')->where('id',$getUserIdOfComment->user_id)->first();
                     if(!empty($getDeviceID)){
                        $title =  $getUserName->name . ' like your comment';
                        $messageData['title']   = $title;
                       $messageData['message']         = $getUserName->name .' '.$this->notificationMessage('4');   
                        $messageData['user_id'] = $getUserIdOfComment->user_id;
                        $messageData['id']      = $getUserIdOfComment->post_id;
                        $messageData['notify_type']  = 4; // 3 for comment like
                        $messageData['redirection']  = $getUserIdOfComment->post_id;
                        $messageData['image']        = '';
                        $device_id = $getDeviceID->device_id;
                        $send = $this->sendNotification($device_id, $title, $messageData);
                        if($send){
                            
                            $data = array(
                                'type' => 4,
                                'notifiable_type' => 4,
                                'data' => json_encode($messageData),
                                'sender_id' => $request->user_id,
                                'receiver_id' => $getUserIdOfComment->user_id,
                                'notifiable_id' => $getUserIdOfComment->post_id,
                            );
                            DB::table('notifications')->insert($data);
                        }
                       }
          if($likeComment){
           return response()->json = [
                'status' => true,
                'message' => 'Comment like successfully.',
            ];
          }
      }else{
        $dislike = CommentLikeDislike::where(['comment_id' => $request->comment_id , 'user_id' => $request->      user_id])->delete();
        $deleteNotification = DB::table('notifications')->where(['sender_id'=> $request->user_id,'receiver_id'=>
            $getUserIdOfComment->user_id,'type' => 4])->delete();
         if($dislike){
            return response()->json = [
               'status' => true,
                'message' => 'Comment dislike successfully.',
            ];
         }else{
            return response()->json = [
               'status' => false,
                'message' => 'Something Went Wrong.',
            ];
         }
      }
        
    }catch(Exception $ex){
        return $ex->getMessage();
    }
    }
    public function deleteComment(Request $request){
        try{
          $validator = Validator::make($request->all(), [
            'comment_id' => ['required','exists:comments,id'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
        }
       $deleteCommentLike = CommentLikeDislike::where(['comment_id' => $request->comment_id])->delete();
       $deleteComment = Comment::where(['id' => $request->comment_id])->delete();

       if($deleteComment){
        return response()->json = [
               'status' => true,
                'message' => 'Comment delete successfully.',
            ];
       }
        }catch(Exception $ex){
            return $ex->getMessage();
        }
    }
    function sendNotification($fcm_token, $title, $message, $url = null,$redirection_url = '')
    {
        if($fcm_token!='')
        {
            $registrationDeviceIds = array($fcm_token);
            $msg = array
            (
                'title'         => $message['title'],
                'body'          => $message['message']
            );
            $msg2 = array
            (
                'id'                => $message['id'],
                'user_id'           => $message['user_id'],
                'notify_type'       => $message['notify_type'],
                'redirection'       => @$message['redirection'],
                'title'             => $message['title'],
                'message'           => $message['message'],
                'image'             => $message['image'],    
            );
             
            $fields = array
            (
                'registration_ids'  => $registrationDeviceIds,
                'notification'      => $msg,
                'data'              => $msg2
            );
          
            $headers = array
            (
                'Authorization: key=AAAAEGuSohw:APA91bG9gNFuomhcq6mAqx8I-ER60Knc7WJ8MopHuA0Z4Am2OndTukxUk8E2c7OIEvIMEfQmD3cp3-QRAQZfcJ8RiwwbZD9A6lcxbTOFlZh25Wa2nECR_xM_Nvi4JWWI7mu894MHJG3j',
                'Content-Type: application/json'
            );
             
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result = curl_exec($ch );
            //var_dump($result);//
            curl_close( $ch );
            //echo $result;
            //die;
            return $result;
        }else{
            return true;
        }
    }
    function notificationMessage($type){
        if($type == 1){
            return ' requested to follow you';
        }
        elseif($type == 2){
            return ' like your post';
        }elseif($type == 3){
            return ' commented on your post';
        }elseif($type == 4){
            return ' like your comment.';
        }elseif($type == 5){
            return ' started following with you';
        }
    }
    public function followUnfollowStatus(Request $request){
    try{
        $validator = Validator::make($request->all(), [
        'follow_unfollow__id' => ['required','exists:follow_unfollows,id'],
        'user_id' => ['required','exists:follow_unfollows,user_id'],
        'follower_id' => ['required','exists:follow_unfollows,follower_id'],
        'status' => ['required','numeric']
       ]);
       if ($validator->fails()) {
        return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
       } 
       $getUserName = User::select('name')->where('id',$request->follower_id)->first();
       $post = $request->all();
       $message = ''; 
       if($request->status == 1){
         $message = "Request accept successfully";
       }elseif($request->status == 2){
          $message = "Request rejected successfully";
          $rejectRequest = DB::table('follow_unfollows')->where(['user_id' => $request->user_id ,              'follower_id' => $request->follower_id,'type' => 0 ,'id' => $request->follow_unfollow__id])->delete();
           $deleteNotification = DB::table('notifications')->where(['type' => 1 ,                     'notifiable_id' => $request->follow_unfollow__id,])->delete();
       }
        $updateStatus = FollowUnFollow::where(['user_id' => $request->user_id ,'follower_id'  =>          $request->follower_id,'type' => 0 ,'id' => $request->follow_unfollow__id])
                     ->update(['status' => $request->status]);
         $id = '';
       if(!empty($updateStatus)){
          $getDeviceID = User::select('device_id')->where('id',$request->user_id)->first();
         if(!empty($getDeviceID)){
            $title =  $getUserName->name . ' started following with you';
            $messageData['title']           = $title;
            $messageData['message']         = $getUserName->name .' '.$this->notificationMessage('5');    
            $messageData['user_id']         = $request->follower_id;
            $messageData['id']              = $id;
            $messageData['notify_type']     = 5; // 1 for following notification
            $messageData['redirection']     = $id;
            $messageData['image']           = '';
            $device_id = $getDeviceID->device_id;
            $send = $this->sendNotification($device_id, $title, $messageData);
            if($send){
                // $data = array(
                //     'type' => 5,
                //     'notifiable_type' => 5,
                //     'data' => json_encode($messageData),
                //     'sender_id' => $post['follower_id'],
                //     'receiver_id' => $request->user_id,
                //     'notifiable_id' => $id,
                // );
                // DB::table('notifications')->insert($data);
            }
           }
        return response()->json = [
           'status' => true,
           'message' => $message,
          ];   
       }elseif(!empty($rejectRequest)){
        return response()->json = [
           'status' => true,
           'message' => $message,
          ];
       }
       else{
         return response()->json = [
           'status' => false,
           'message' => 'Record Not Found.',
          ];  
       }
    }catch(Exception $ex){
        return $ex->getMessage();
    }
}
function getCountOfFollowerAndFollowing($type , $user_id){
    if($type == 1){
         $data = DB::table('follow_unfollows')->where(['follow_unfollows.user_id'=> $user_id,                     'type' => 0])->count();
        return $data;
    }
    else{
       $data = DB::table('follow_unfollows')->where(['follow_unfollows.follower_id'=> $user_id,                      'type' => 0,'status' => 1])->count();
       return $data;  
    }
  }
  public function notificationList(Request $request){
    try{
      $validator = Validator::make($request->all(), [
        'user_id' => ['required','exists:users,id'],
       ]);
       if ($validator->fails()) {
        return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
       } 
    $getNotification = DB::table('notifications')->select('notifications.id','data','sender_id','receiver_id',              'notifiable_id','name as receiver_name','image','email','address','phone',              'business_name','users.created_at','place','follow_unfollows.status')
                      ->leftJoin('users','users.id' ,'=', 'notifications.sender_id')
                      ->leftJoin('follow_unfollows','follow_unfollows.id','=','notifications.notifiable_id')
                      ->get();
     $response = [];
     // echo "<pre>";
     // print_r($getNotification);die;
    if(count($getNotification) > 0){
        foreach($getNotification as $dt){
        $messaage = json_decode($dt->data, true);
         $data['notification_id'] = $dt->id;
         $data['sender_id'] = $dt->sender_id;
         $data['receiver_id'] = $dt->receiver_id;
         $data['receiver_name'] = $dt->receiver_name;
         if($dt->image){
           $data['receiver_image'] = 'uploads/profile/'.$dt->image;
          }else{
            $data['receiver_image'] = 'uploads/profile/user.jpg';
          }
         $data['receiver_email'] = $dt->email;
         $data['receiver_address'] = $dt->address;
         $data['receiver_phone']  = $dt->phone;
         $data['receiver_business_name']  = $dt->business_name;
         $data['receiver_created_at']  = date('d-M-y',strtotime($dt->created_at));
         $data['receiver_location'] = $this->getUserLocation($dt->place);
         $data['notifiable_id'] = $dt->notifiable_id;
         if($dt->status == 0){
             $data['status'] = 'pending';
         }else{
           $data['status'] = "accepted";
         }
         if (isset($messaage['message'])) {
            $data['message'] = $messaage['message'];    
         }
         $response[] = $data;
        }
        return response()->json = [
           'status' => true,
           'data' => $response,
           'message' => 'Get notifications successfully.',
          ]; 
    }else{
        return response()->json = [
           'status' => false,
           'message' => 'Notification not found.',
          ];
     }
    }catch(Exception $ex){
        return $ex->getMessage();
    }
  }
  public function deleteNotification(Request $request){
    try{
        $validator = Validator::make($request->all(), [
        'notification_id' => ['required','exists:notifications,id'],
       ]);
       if ($validator->fails()) {
        return response()->json(['status'=>false,'message' => $validator->messages()->first()], 200);
       } 
       $deleteNotification = DB::table('notifications')->where(['id' => $request->notification_id])                 ->delete();
    if($deleteNotification){
        return response()->json = [
           'status' => true,
           'message' => 'Notification deleted successfully',
          ];
      }
    }catch(Exception $ex){
        return $ex->getMessage();
    }
  }
}
