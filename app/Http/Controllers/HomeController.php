<?php

namespace App\Http\Controllers;

use App\Items;
use App\Order;
use App\Restorant;
use App\User;
use App\UserAdvertisment;
use App\Category;
use App\UserSellProduc;
use App\ProfessionalTableData;
use Carbon\Carbon;
use DB;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /*if (! config('app.ordering')) {
            if (auth()->user()->hasRole('owner')) {
                return redirect()->route('admin.restaurants.edit', auth()->user()->restorant->id);
            } elseif (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.restaurants.index');
            }
        }*/
        $months = [
            0 => __('Jan'),
            1 => __('Feb'),
            2 => __('Mar'),
            3 => __('Apr'),
            4 => __('May'),
            5 => __('Jun'),
            6 => __('Jul'),
            7 => __('Aug'),
            8 => __('Sep'),
            9 => __('Oct'),
            10 => __('Nov'),
            11 => __('Dec'),
        ];

        $totalExam = 0;
        $totalCoaching = 0;
        $totalBook = 0;
        $totalUsers = User::where('role','>', 0)->count();
        $booksListDashboard = [];
        $userListDashboard = User::select('id', 'name', 'email', 'phone')->where('role', 1)->orderBy('id', 'desc')->take(5)->get();
        $photographerListDashboard = User::select('id', 'name', 'email', 'phone')->where('role', 2)->orderBy('id', 'desc')->take(5)->get();
        $userDCount = User::where('role', 1)->count();
        $userPDCount = User::where('role', 2)->count();
        $photographerAdsCount = UserAdvertisment::where('user_id','>',1)->where('valid_till', '>', time())->count();
        $adminAdsCount = UserAdvertisment::where('user_id','=',1)->count();
        $categoryDCount = Category::count();
        $productSellDCount = UserSellProduc::count();
        $liveEnquiryTableDataD = ProfessionalTableData::select('user_id')->where('category_table_id', 1)->groupBy('table_number')->get();
        $liveEnquiryNUserCount = 0;
        $liveEnquiryPUserCount = 0;
        $liveEnquiryUserDCount = [];
        if(count($liveEnquiryTableDataD) > 0){
            foreach($liveEnquiryTableDataD as $npuser){
                $utypedashboard = '';
                $userTypeData = User::where('id', $npuser->user_id)->first();
                if(!is_null($userTypeData)){
                    if($userTypeData->role == 1){
                        $liveEnquiryNUserCount++;
                        $utypedashboard = 'Normal';
                    }
                    else{
                        $liveEnquiryPUserCount++;
                        $utypedashboard = 'Photographer';
                    }
                    $duseData['id'] = $userTypeData->id;
                    $duseData['name'] = $userTypeData->name;
                    $duseData['email'] = $userTypeData->email;
                    $duseData['user_type'] = $utypedashboard;
                    $liveEnquiryUserDCount[] = $duseData;
                }
            }
        }
        if(auth()->user()->role == 0){
            if (auth()->user()->hasRole('admin')) {
                //first analytics
                $last30days = Carbon::now()->subDays(30);
                $last30daysOrders = Order::all()->where('created_at', '>', $last30days)->count();
                $last30daysOrdersValue = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('order_price');
                //$uniqueUsersOrders = Order::all()->unique('address_id')->count();
                $uniqueUsersOrders = Order::select('client_id')->groupBy('client_id')->get()->count();
                $allClients = User::all()->count();

                //Last 7 months sales values
                $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
                $salesValue = DB::table('orders')
                            ->select(DB::raw('SUM(order_price + delivery_price) AS sumValue'))
                            ->where('created_at', '>', $sevenMonthsDate)
                            ->where('payment_status','paid')
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('sumValue');

                $monthLabels = DB::table('orders')
                            ->select(DB::raw('MONTH(created_at) as month'))
                            ->where('created_at', '>', $sevenMonthsDate)
                            ->where('payment_status','paid')
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('month');

                $totalOrders = DB::table('orders')
                            ->select(DB::raw('count(id) as totalPerMonth'))
                            ->where('created_at', '>', $sevenMonthsDate)
                            ->where('payment_status','paid')
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('totalPerMonth');

                $last30daysDeliveryFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('delivery_price');
                $last30daysStaticFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('static_fee');
                $last30daysDynamicFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('fee_value');
                $last30daysTotalFee = DB::table('orders')
                                    ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                    ->where('created_at', '>', $last30days)
                                    ->where('payment_status','paid')
                                    ->value('sumValue');

                //dd(Carbon::now()->format('M'));
                $views = Restorant::sum('views');
                
                return view('dashboard', [
                    'totalUsers' => $totalUsers,
                    'totalExam' => $totalExam,
                    'totalCoaching' => $totalCoaching,
                    'totalBook' => $totalBook,
                    'userListDashboard' => $userListDashboard,
                    'photographerListDashboard' => $photographerListDashboard,

                    'userDCount' => $userDCount,
                    'userPDCount' => $userPDCount,
                    'photographerAdsCount' => $photographerAdsCount,
                    'adminAdsCount' => $adminAdsCount,
                    'categoryDCount' => $categoryDCount,
                    'productSellDCount' => $productSellDCount,
                    'liveEnquiryNUserCount' => $liveEnquiryNUserCount,
                    'liveEnquiryPUserCount' => $liveEnquiryPUserCount,
                    'liveEnquiryUserDCount' => $liveEnquiryUserDCount,
                    
                    'booksListDashboard' => $booksListDashboard,
                    'last30daysOrders'=> $last30daysOrders,
                    'last30daysOrdersValue'=> $last30daysOrdersValue,
                    'uniqueUsersOrders' => $uniqueUsersOrders,
                    'allClients' => $allClients,
                    'allViews' => $views,
                    'salesValue' => $salesValue,
                    'monthLabels' => $monthLabels,
                    'totalOrders' => $totalOrders,
                    'countItems'=>Restorant::count(),
                    'last30daysDeliveryFee' => $last30daysDeliveryFee,
                    'last30daysStaticFee' => $last30daysStaticFee,
                    'last30daysDynamicFee' => $last30daysDynamicFee,
                    'last30daysTotalFee' => $last30daysTotalFee,
                    'months' => $months,
                ]);
            } elseif (auth()->user()->hasRole('owner')) {
                //first analytics
                $restorant_id = auth()->user()->restorant->id;

                //Change currency
                \App\Services\ConfChanger::switchCurrency(auth()->user()->restorant);

                $last30days = Carbon::now()->subDays(30);
                // $last30daysOrders = Order::all()->where('created_at', '>', $last30days, 'AND', 'restorant_id', '=' ,$restorant_id)->count();
                $last30daysOrders = Order::where([
                    ['created_at', '>', $last30days],
                    ['restorant_id', '=', $restorant_id],
                ])->count();

                //$last30daysOrdersValue = Order::all()->where('created_at', '>', $last30days, 'AND', 'restorant_id', '=', $restorant_id)->sum('order_price');
                $last30daysOrdersValue = Order::where([
                    ['created_at', '>', $last30days],
                    ['restorant_id', '=', $restorant_id],
                    ['payment_status', '=', 'paid'],
                ])->sum('order_price');

                //$uniqueUsersOrders = Order::all()->unique('address_id')->where('restorant_id', '=', $restorant_id)->count();
                $uniqueUsersOrders = Order::select('client_id')->where('restorant_id', '=', $restorant_id)->groupBy('client_id')->get()->count();

                //update this query when will be added user id column in the orders
                $allClients = User::all()->count();

                //Last 7 months sales values
                $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
                /*$salesValue = DB::table('orders')
                            ->select(DB::raw('SUM(order_price + delivery_price) AS sumValue'))
                            ->where('created_at', '>', $sevenMonthsDate, 'AND', 'restorant_id', '=', $restorant_id)
                            ->groupBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
                            ->orderBy(DB::raw("YEAR(created_at), MONTH(created_at)"), 'asc')
                            ->pluck('sumValue');*/
                $salesValue = DB::table('orders')
                            ->select(DB::raw('SUM(order_price + delivery_price) AS sumValue'))
                            ->where([['created_at', '>', $sevenMonthsDate], ['restorant_id', '=', $restorant_id], ['payment_status', '=', 'paid']])
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('sumValue');

                /*$monthLabels = DB::table('orders')
                            ->select(DB::raw('MONTH(created_at) as month'))
                            ->where('created_at', '>', $sevenMonthsDate, 'AND', 'restorant_id', '=', $restorant_id)
                            ->groupBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
                            ->orderBy(DB::raw("YEAR(created_at), MONTH(created_at)"), 'asc')
                            ->pluck('month');*/
                $monthLabels = DB::table('orders')
                            ->select(DB::raw('MONTH(created_at) as month'))
                            ->where([['created_at', '>', $sevenMonthsDate], ['restorant_id', '=', $restorant_id]])
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('month');

                /*$totalOrders = DB::table('orders')
                            ->select(DB::raw('count(id) as totalPerMonth'))
                            ->where('created_at', '>', $sevenMonthsDate, 'AND', 'restorant_id', '=', $restorant_id)
                            ->groupBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
                            ->orderBy(DB::raw("YEAR(created_at), MONTH(created_at)"), 'asc')
                            ->pluck('totalPerMonth');*/
                $totalOrders = DB::table('orders')
                            ->select(DB::raw('count(id) as totalPerMonth'))
                            ->where([['created_at', '>', $sevenMonthsDate], ['restorant_id', '=', $restorant_id]])
                            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                            ->pluck('totalPerMonth');

                $last30daysDeliveryFee = Order::where([['created_at', '>', $last30days], ['restorant_id', '=', $restorant_id]])->sum('delivery_price');
                $last30daysStaticFee = Order::where([['created_at', '>', $last30days], ['restorant_id', '=', $restorant_id]])->sum('static_fee');
                $last30daysDynamicFee = Order::where([['created_at', '>', $last30days], ['restorant_id', '=', $restorant_id]])->sum('fee_value');
                $last30daysTotalFee = DB::table('orders')
                                    ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                    ->where([['created_at', '>', $last30days], ['restorant_id', '=', $restorant_id]])
                                    ->value('sumValue');
                $itemsCount = Items::whereIn('category_id', auth()->user()->restorant->categories->pluck('id')->toArray())->count();
                $totalExam = Exam::count();
                return view('dashboard', [
                    'totalUsers' => $totalUsers,
                    'totalExam' => $totalExam,
                    'totalCoaching' => $totalCoaching,
                    'totalBook' => $totalBook,
                    'userListDashboard'=> $userListDashboard,
                    'photographerListDashboard'=> $photographerListDashboard,
                    'booksListDashboard'=> $booksListDashboard,
                    'last30daysOrders'=> $last30daysOrders,
                    'last30daysOrdersValue'=> $last30daysOrdersValue,
                    'uniqueUsersOrders' => $uniqueUsersOrders,
                    'allClients' => $allClients,
                    'allViews' => auth()->user()->restorant->views,
                    'salesValue' => $salesValue,
                    'monthLabels' => $monthLabels,
                    'totalOrders' => $totalOrders,
                    'countItems'=>$itemsCount,
                    'last30daysDeliveryFee' => $last30daysDeliveryFee,
                    'last30daysStaticFee' => $last30daysStaticFee,
                    'last30daysDynamicFee' => $last30daysDynamicFee,
                    'last30daysTotalFee' => $last30daysTotalFee,
                    'months' => $months,
                ]);
            } elseif (auth()->user()->hasRole('driver')) {

                $driver = auth()->user();

                 //Today paid orders
                $today=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::today());
            
                //Week paid orders
                $week=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfWeek());

                //This month paid orders
                $month=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfMonth());

                //Previous month paid orders 
                $previousmonth=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=',  Carbon::now()->subMonth(1)->startOfMonth())->where('created_at', '<',  Carbon::now()->subMonth(1)->endOfMonth());


                //This user driver_percent_from_deliver
                $driver_percent_from_deliver=intval(auth()->user()->getConfig('driver_percent_from_deliver',config('settings.driver_percent_from_deliver')))/100;

                $earnings = [
                    'today'=>[
                        'orders'=>$today->count(),
                        'earning'=>$today->sum('delivery_price')*$driver_percent_from_deliver,
                        'icon'=>'bg-gradient-red'
                    ],
                    'week'=>[
                        'orders'=>$week->count(),
                        'earning'=>$week->sum('delivery_price')*$driver_percent_from_deliver,
                        'icon'=>'bg-gradient-orange'
                    ],
                    'month'=>[
                        'orders'=>$month->count(),
                        'earning'=>$month->sum('delivery_price')*$driver_percent_from_deliver,
                        'icon'=>'bg-gradient-green'
                    ],
                    'previous'=>[
                        'orders'=>$previousmonth->count(),
                        'earning'=>$previousmonth->sum('delivery_price')*$driver_percent_from_deliver,
                        'icon'=>'bg-gradient-info'
                    ]
                ];

                return view('dashboard', [
                    'earnings' => $earnings
                ]);
                //return redirect()->route('orders.index');
            } elseif (auth()->user()->hasRole('client')) { dd('client');
                return redirect()->route('front');
            }
        }
        elseif(auth()->user()->role == 2){
            $last30days = Carbon::now()->subDays(30);
            $last30daysOrders = Order::all()->where('created_at', '>', $last30days)->count();
            $last30daysOrdersValue = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('order_price');
            //$uniqueUsersOrders = Order::all()->unique('address_id')->count();
            $uniqueUsersOrders = Order::select('client_id')->groupBy('client_id')->get()->count();
            $allClients = User::all()->count();

            //Last 7 months sales values
            $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
            $salesValue = DB::table('orders')
                        ->select(DB::raw('SUM(order_price + delivery_price) AS sumValue'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('sumValue');

            $monthLabels = DB::table('orders')
                        ->select(DB::raw('MONTH(created_at) as month'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('month');

            $totalOrders = DB::table('orders')
                        ->select(DB::raw('count(id) as totalPerMonth'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('totalPerMonth');

            $last30daysDeliveryFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('delivery_price');
            $last30daysStaticFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('static_fee');
            $last30daysDynamicFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('fee_value');
            $last30daysTotalFee = DB::table('orders')
                                ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                ->where('created_at', '>', $last30days)
                                ->where('payment_status','paid')
                                ->value('sumValue');

            //dd(Carbon::now()->format('M'));
            $views = Restorant::sum('views');
           
            $totalBooks = 0;
            $totalBooksList = [];
            return view('dashboard-publisher', [
                'totalBooks' => $totalBooks,
                'totalBooksList' => $totalBooksList,
                'totalOrdersList' => [],
                'totalUsers' => $totalUsers,
                'totalExam' => $totalExam,
                'totalCoaching' => $totalCoaching,
                'totalBook' => $totalBook,
                'userListDashboard' => $userListDashboard,
                'photographerListDashboard' => $photographerListDashboard,
                'last30daysOrders'=> $last30daysOrders,
                'last30daysOrdersValue'=> $last30daysOrdersValue,
                'uniqueUsersOrders' => $uniqueUsersOrders,
                'allClients' => $allClients,
                'allViews' => $views,
                'salesValue' => $salesValue,
                'monthLabels' => $monthLabels,
                'totalOrders' => $totalOrders,
                'countItems'=>Restorant::count(),
                'last30daysDeliveryFee' => $last30daysDeliveryFee,
                'last30daysStaticFee' => $last30daysStaticFee,
                'last30daysDynamicFee' => $last30daysDynamicFee,
                'last30daysTotalFee' => $last30daysTotalFee,
                'months' => $months,
            ]);
        }  
        else{
            $last30days = Carbon::now()->subDays(30);
            $last30daysOrders = Order::all()->where('created_at', '>', $last30days)->count();
            $last30daysOrdersValue = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('order_price');
            //$uniqueUsersOrders = Order::all()->unique('address_id')->count();
            $uniqueUsersOrders = Order::select('client_id')->groupBy('client_id')->get()->count();
            $allClients = User::all()->count();

            //Last 7 months sales values
            $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
            $salesValue = DB::table('orders')
                        ->select(DB::raw('SUM(order_price + delivery_price) AS sumValue'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('sumValue');

            $monthLabels = DB::table('orders')
                        ->select(DB::raw('MONTH(created_at) as month'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('month');

            $totalOrders = DB::table('orders')
                        ->select(DB::raw('count(id) as totalPerMonth'))
                        ->where('created_at', '>', $sevenMonthsDate)
                        ->where('payment_status','paid')
                        ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                        ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                        ->pluck('totalPerMonth');

            $last30daysDeliveryFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('delivery_price');
            $last30daysStaticFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('static_fee');
            $last30daysDynamicFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('fee_value');
            $last30daysTotalFee = DB::table('orders')
                                ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                ->where('created_at', '>', $last30days)
                                ->where('payment_status','paid')
                                ->value('sumValue');

            //dd(Carbon::now()->format('M'));
            $views = Restorant::sum('views');
            $totalNotes = 0;
            $totalNotesList = [];
            return view('dashboard-coaching', [
                'totalNotes' => $totalNotes,
                'totalNotesList' => $totalNotesList,
                'totalUsers' => [],
                'totalExam' => [],
                'totalCoaching' => [],
                'totalBook' => [],
                'userListDashboard' => [],
                'photographerListDashboard' => [],
                'last30daysOrders'=> $last30daysOrders,
                'last30daysOrdersValue'=> $last30daysOrdersValue,
                'uniqueUsersOrders' => $uniqueUsersOrders,
                'allClients' => $allClients,
                'allViews' => $views,
                'salesValue' => $salesValue,
                'monthLabels' => $monthLabels,
                'totalOrders' => $totalOrders,
                'countItems'=>Restorant::count(),
                'last30daysDeliveryFee' => $last30daysDeliveryFee,
                'last30daysStaticFee' => $last30daysStaticFee,
                'last30daysDynamicFee' => $last30daysDynamicFee,
                'last30daysTotalFee' => $last30daysTotalFee,
                'months' => $months,
            ]);
        }            
    }
}
