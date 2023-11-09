<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes  V1 /api
|--------------------------------------------------------------------------
|
*/

Route::post('register','ApiController@register');

Route::post('login','ApiController@login');

Route::post('matchOTP','ApiController@matchOTP');

Route::post('resendOTP','ApiController@resendOTP');

Route::post('forgotpassword','ApiController@forgotpassword');

Route::post('resetPassword','ApiController@resetPassword');

Route::post('slider','ApiController@slider');

Route::post('about_us','ApiController@about_us');

Route::post('term_condition','ApiController@term_condition');
  
Route::post('policy','ApiController@policy');

Route::get('categoryList','ApiController@categoryList');

Route::get('socialMediaLink','ApiController@socialMediaLink');

Route::post('subCategoryList','ApiController@subCategoryList');

Route::get('allSubCategoryList','ApiController@allSubCategoryList');

Route::post('stateList','ApiController@stateList');

Route::post('cityList','ApiController@cityList');

Route::post('allCityList','ApiController@allCityList');

Route::post('getUserProfile','ApiController@getUserProfile');

Route::post('userProfile','ApiController@userProfile');

Route::post('getPhotographerProfile','ApiController@getPhotographerProfile');

Route::post('photographerProfile','ApiController@photographerProfile');

Route::post('updatePhotographerName','ApiController@updatePhotographerName');
Route::post('updatePhotographerPlace','ApiController@updatePhotographerPlace');
Route::post('updatePhotographerPassword','ApiController@updatePhotographerPassword');
Route::post('updatePhotographerImage','ApiController@updatePhotographerImage');
Route::post('removePhotographerImage','ApiController@removePhotographerImage');
Route::post('updatePhotographerBusinessName','ApiController@updatePhotographerBusinessName');
Route::post('updatePhotographerAboutBusiness','ApiController@updatePhotographerAboutBusiness');
Route::post('updatePhotographerAddress','ApiController@updatePhotographerAddress');

Route::post('updateSocialLink','ApiController@updateSocialLink');

Route::post('userProfessional','ApiController@userProfessional');
Route::post('userProfessionalList','ApiController@userProfessionalList');

Route::post('changeProfessionalStatus','ApiController@changeProfessionalStatus');
Route::post('removeUserProfessional','ApiController@removeUserProfessional');
Route::post('compleleProfile','ApiController@compleleProfile');

Route::post('addAdvertisement','ApiController@addAdvertisement');
Route::post('removeAdvertisment','ApiController@removeAdvertisment');
Route::post('getAdvertisement','ApiController@getAdvertisement');
Route::post('photographerInFocus','ApiController@photographerInFocus');
Route::get('showcaseList','ApiController@showcaseList');

Route::post('addProduct','ApiController@addProduct');
Route::post('getProductDetails','ApiController@getProductDetails');
Route::post('productList','ApiController@productList');
Route::post('sellProductList','ApiController@sellProductList');
Route::post('serachProducts','ApiController@serachProducts');
Route::post('productDetails','ApiController@productDetails');
Route::post('productUpdate','ApiController@productUpdate');
Route::post('removeProduct','ApiController@removeProduct');
Route::post('removeProductPhotos','ApiController@removeProductPhotos');
Route::post('removeProductInvoice','ApiController@removeProductInvoice');
Route::post('userCategoryList','ApiController@userCategoryList');
Route::post('addUserCategory','ApiController@addUserCategory');
Route::post('removeUserCategory','ApiController@removeUserCategory');
Route::post('userMediaList','ApiController@userMediaList');
Route::post('addMedia','ApiController@addMedia');
Route::post('removeMedia','ApiController@removeMedia');

Route::post('searchProfessional','ApiController@searchProfessional');
Route::post('myProfessionalList','ApiController@myProfessionalList');
Route::post('tableColumnList','ApiController@tableColumnList');
Route::post('addTableColumnData','ApiController@addTableColumnData');
Route::post('tableDataList','ApiController@tableDataList');
Route::post('tableColumnDataCategoryList','ApiController@tableColumnDataCategoryList');
Route::post('tableColumnDataList','ApiController@tableColumnListAndDataList');
Route::post('tableColumnDataListWithoutAction','ApiController@tableColumnDataListNotAction');
Route::post('subCategoryUserData','ApiController@subCategoryUserData');
Route::post('removeTableRow','ApiController@removeTableRow');
Route::post('addTableLocation','ApiController@addTableLocation');

Route::get('liveEnquiryPhotographer','ApiController@liveEnquiryPhotographer');
Route::get('brandList','ApiController@brandList');
Route::post('professionalWisePhotographerList','ApiController@professionalWisePhotographerList');
Route::post('searchProfessionalPhotographer','ApiController@searchProfessionalPhotographer');
Route::post('photographerProfessionalDetails','ApiController@photographerProfessionalDetails');

Route::post('uploadImagePost','ApiController@uploadImagePost');
Route::post('videoPost','ApiController@videoPost');
Route::post('getPostByUserID','ApiController@getPostByUserID');
Route::post('updatePost','ApiController@updatePost');
Route::post('deletePost','ApiController@deletePost');
Route::post('listOfFriend','ApiController@listOfFriend');
Route::post('friendsDetials','ApiController@friendsDetials');
Route::post('getLatestProduct','ApiController@getLatestProduct');
Route::post('followUnfollow','ApiController@followUnfollow');
Route::post('likeDislike','ApiController@likeDislike');
Route::post('checkFavorite','ApiController@checkFavorite');
Route::post('mediaList','ApiController@mediaList');
Route::post('followerList','ApiController@followerList');
Route::post('followingList','ApiController@followingList');
Route::post('removeImgAndVideo','ApiController@removeImgAndVideo');
Route::post('comment','ApiController@comment');
Route::post('commentList','ApiController@commentList');
Route::post('commentLikeDislike','ApiController@commentLikeDislike');
Route::post('deleteComment','ApiController@deleteComment');
Route::post('followUnfollowStatus','ApiController@followUnfollowStatus');
Route::post('notificationList','ApiController@notificationList');
Route::post('deleteNotification','ApiController@deleteNotification');
Route::post('followBack','ApiController@followBack');
Route::post('cancelRequest','ApiController@cancelRequest');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    //Driver - Private
    Route::get('/driverorders', 'DriverController@getOrders')->name('driver.orders');
    Route::get('/updateorderstatus/{order}/{status}', 'DriverController@updateOrderStatus')->name('driver.updateorderstatus');
    Route::get('/updateorderlocation/{order}/{lat}/{lng}', 'DriverController@orderTracking')->name('driver.updateorderlocation');
    Route::get('/rejectorder/{order}', 'DriverController@rejectOrder')->name('driver.rejectorder');
    Route::get('/acceptorder/{order}', 'DriverController@acceptOrder')->name('driver.acceptorder');
    Route::get('/driveronline', 'DriverController@goOnline')->name('driver.goonline');
    Route::get('/drveroffline', 'DriverController@goOffline')->name('driver.gooffline');
});

//Driver - Public
Route::post('/drivergettoken', 'DriverController@getToken')->name('driver.getToken');

/*
|--------------------------------------------------------------------------
| API Routes  V2 /api/v2/
|--------------------------------------------------------------------------
|
*/

//DRIVER
Route::prefix('v2/driver')->group(function () {
    /**
     * AUTH
     */
    //Auth /api/v2/driver/auth
    Route::prefix('auth')->name('driver.auth.')->group(function () {
        Route::post('gettoken', 'API\Driver\AuthController@getToken')->name('getToken'); 
        Route::post('register', 'API\Driver\AuthController@register')->name('register'); 
        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('data', 'API\Driver\AuthController@getUseData')->name('getUseData'); 
            Route::get('driveronline', 'API\Driver\AuthController@goOnline')->name('goonline');
            Route::get('drveroffline', 'API\Driver\AuthController@goOffline')->name('gooffline');
        });   
    });

    /**
     * Settings - uses the same from client
     */
    //Settings /api/v2/driver/settings
    Route::prefix('settings')->name('driver.settings.')->group(function () {
        Route::get('/', 'API\Client\SettingsController@index')->name('indexapi');
    });

    //NEEDS AUTHENTICATION
    Route::group(['middleware' => 'auth:api'], function () {

        /**
         * ORDERS
         */

        //Orders /api/v2/client/orders
        Route::prefix('orders')->name('driver.orders.')->group(function () {
            Route::get('/', 'API\Driver\OrdersController@index');
            Route::get('/order/{order}', 'API\Driver\OrdersController@order');
            Route::get('earnings','API\Driver\OrdersController@earnings');
            Route::get('updateorderstatus/{order}/{status}', 'API\Driver\OrdersController@updateOrderStatus')->name('driver.updateorderstatus');
            Route::get('updateorderlocation/{order}/{lat}/{lng}', 'API\Driver\OrdersController@orderTracking')->name('driver.updateorderlocation');
            Route::get('rejectorder/{order}', 'API\Driver\OrdersController@rejectOrder')->name('driver.rejectorder');
            Route::get('acceptorder/{order}', 'API\Driver\OrdersController@acceptOrder')->name('driver.acceptorder');
        });
    });


});


//Vendor
Route::prefix('v2/vendor')->group(function () {
    /**
     * AUTH
     */
    //Auth /api/v2/vendor/auth
    Route::prefix('auth')->name('vendor.auth.')->group(function () {
        Route::post('gettoken', 'API\Vendor\AuthController@getToken')->name('getToken'); 
        Route::post('register', 'API\Vendor\AuthController@register')->name('register'); 
        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('data', 'API\Vendor\AuthController@getUseData')->name('getUseData'); 
        });   
    });

    /**
     * Settings - uses the same from client
     */
    //Settings /api/v2/vendor/settings
    Route::prefix('settings')->name('vendor.settings.')->group(function () {
        Route::get('/', 'API\Client\SettingsController@index')->name('indexapivendor');
    });

    //NEEDS AUTHENTICATION
    Route::group(['middleware' => 'auth:api'], function () {

        /**
         * ORDERS
         */

        //Orders /api/v2/client/orders
        Route::prefix('orders')->name('vendor.orders.')->group(function () {
            Route::get('/', 'API\Vendor\OrdersController@index');
            Route::get('/order/{order}', 'API\Vendor\OrdersController@order');
            Route::get('earnings','API\Vendor\OrdersController@earnings');
            Route::get('updateorderstatus/{order}/{status}', 'API\Vendor\OrdersController@updateOrderStatus')->name('vendor.updateorderstatus');
            Route::get('updateorderlocation/{order}/{lat}/{lng}', 'API\Vendor\OrdersController@orderTracking')->name('vendor.updateorderlocation');
            Route::get('rejectorder/{order}', 'API\Vendor\OrdersController@rejectOrder')->name('vendor.rejectorder');
            Route::get('acceptorder/{order}', 'API\Vendor\OrdersController@acceptOrder')->name('vendor.acceptorder');
        });
    });


});


//CLIENT
Route::prefix('v2/client')->group(function () {
    
    /**
     * AUTH
     */
    //Auth /api/v2/client/auth
    Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('gettoken', 'API\Client\AuthController@getToken')->name('getToken'); 
            Route::post('register', 'API\Client\AuthController@register')->name('register'); 
            Route::post('loginfb', 'API\Client\AuthController@loginFacebook'); 
            Route::post('logingoogle', 'API\Client\AuthController@loginGoogle'); 
            Route::group(['middleware' => 'auth:api'], function () {
                Route::get('data', 'API\Client\AuthController@getUseData')->name('getUseData'); 
            });   
    });

     /**
     * Settings
     */
    //Settings /api/v2/client/settings
    Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', 'API\Client\SettingsController@index')->name('indexapiclient');
    });
   


    /**
     * VENDOR
     */

    //Vendor /api/v2/client/vendor
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('cities', 'API\Client\VendorController@getCities')->name('cities');
        Route::get('list/{city_id}', 'API\Client\VendorController@getVendors')->name('list');
        Route::get('{id}/items', 'API\Client\VendorController@getVendorItems')->name('items');
        Route::get('{id}/hours', 'API\Client\VendorController@getVendorHours')->name('hours');
        Route::get('/deliveryfee/{res}/{adr}', 'API\Client\VendorController@getDeliveryFee')->name('delivery.fee');
    });


   

    //NEEDS AUTHENTICATION
    Route::group(['middleware' => 'auth:api'], function () {

        /**
         * ORDERS
         */

        //Orders /api/v2/client/orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', 'API\Client\OrdersController@index');
            Route::post('/', 'API\Client\OrdersController@store')->name('storeapi');
        });


        /**
         * Addresses
         */

        //Addresses /api/v2/client/addresses
        Route::prefix('addresses')->name('orders.')->group( function () {
            Route::get('/', 'API\Client\AddressController@getMyAddresses');
            Route::get('/fees/{restaurant_id}', 'API\Client\AddressController@getMyAddressesWithFees');
            Route::post('/', 'API\Client\AddressController@makeAddress')->name('make.address');
            Route::post('/delete', 'API\Client\AddressController@deleteAddress')->name('delete.address');
        });

        /**
         * Notifications
         */

        //Notifications /api/v2/client/notifications
        Route::prefix('notifications')->name('orders.')->group( function () {
            Route::get('/', 'API\Client\NotificationsController@index');
        });


    });
});