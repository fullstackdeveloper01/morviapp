<?php

namespace App\Http\Controllers;

use App\User;
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

use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$userAdvertisementList = UserAdvertisment::where('user_id', '>', '1')->groupBy('user_id')->get();
        return view('monitor.index', ['userAdvertisementList' => $userAdvertisementList]);
    }

    /**
     *@Function: User advertisement list 
     */
    public function advertisementList($uid){
        $userData  = User::where('id', $uid)->first('name');
        $userName = $userData->name;
        $userAdvertisementList = UserAdvertisment::where('user_id', $uid)->get();
        return view('monitor.user-advertisement', ['userAdvertisementList' => $userAdvertisementList, 'userName' => $userName]);
    }

    /**
     *@Function: Approve Disapprove Advertisement
     */
    public function approveDisapproveAdvertisement($id, $status){
        $strtotime = '--';
        if($status == 1){
            $strtotime = date('d, M Y H:s', strtotime('+1 year'));
            $validtill = strtotime('+1 year');
            $rowaffected = UserAdvertisment::where('id', $id)->update(['status' => $status, 'valid_till' => $validtill]);        
        }
        else{
            $rowaffected = UserAdvertisment::where('id', $id)->update(['status' => $status, 'valid_till' => '--']);        
        }
        return $strtotime;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratings $rating)
    {
        $rating->delete();

        return redirect()->route('reviews.index')->withStatus(__('Rating has been removed'));
    }
}
