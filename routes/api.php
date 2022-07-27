<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\MessagesController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
return $request->user();
});*/

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::get('loginauth', [UserController::class,'notauthenticated'])->name('login');
Route::get('fetchstates', [UserController::class,'fetchstates']);
Route::get('fetchlgas', [UserController::class,'fetchlgas']);
Route::get('fetchlgasbystateid/{stateid}', [UserController::class,'fetchlgasbystateid']);
Route::get('fetchclienttypes', [UserController::class,'fetchclienttypes']);
Route::get('fetchhousesizes', [UserController::class,'fetchhousesizes']);
Route::get('fetchclients', [UserController::class,'fetchclients']);
Route::get('getclientbyclientid/{id}', [UserController::class,'getclientbyclientid']);
Route::get('fetchprojects', [ProjectController::class,'fetchprojects']);
Route::get('getprojectbyid/{id}', [ProjectController::class,'getprojectbyid']);
Route::get('paymentrequest/{id}', [PaymentController::class,'getprojectpaymentbyid']);
Route::post('confirmpayment/{id}/{projectid}', [PaymentController::class,'confirmPayment']);
Route::get('addpayment', [PaymentController::class,'addPayment']);

Route::get('fetchavailableprojectstatus', [ProjectController::class,'fetchavailableprojectstatus']);
Route::get('fetchavailableprojecttypes', [ProjectController::class,'fetchavailableprojecttypes']);
Route::get('fetchmessagesbyprojectid/{projectid}', [MessagesController::class,'fetchmessagesbyprojectid']);
Route::get('fetchmessagebyid/{id}', [MessagesController::class,'fetchmessagebyid']);
Route::get('fetchroles', [UserController::class,'fetchroles']);
Route::get('fetchreport', [ProjectController::class,'fetchreport']);
Route::get('fetchsalesbystateid/{stateid}', [ProjectController::class,'fetchsalesbystateid']);
Route::get('fetchweeklylinechart', [ProjectController::class,'fetchweeklylinechart']);
Route::get('fetchyearlylinechart', [ProjectController::class,'fetchyearlylinechart']);
Route::get('fetchmonthlylinechart', [ProjectController::class,'fetchmonthlylinechart']);



Route::post('sendpasswordresetlink', [UserController::class, 'sendpasswordresetlink']);
Route::post('resetpassword', [UserController::class, 'resetpassword']);
Route::get('confirmemail/{link}', [UserController::class, 'confirmemail'])->name('confirmemail');
Route::get('fetchadmindashboard', [UserController::class,'fetchadmindashboard']);
Route::post('addpayment/{projectid}', [PaymentController::class,'addPayment']);

Route::middleware('auth:sanctum')->group( function () {
    Route::post('shout', [UserController::class, 'shout']);
    Route::get('deletesubitem/{id}', [InventoryController::class, 'deleteSubitem']);
    Route::post('createproduct', [ProductController::class, 'createproduct']);
    Route::get('fetchproducts', [ProductController::class, 'fetchproducts']);
    Route::post('addclient', [UserController::class, 'addclient']);
    Route::post('addproject', [ProjectController::class, 'addproject']);
    Route::post('sendmessage', [MessagesController::class, 'sendmessage']);
    Route::get('fetchmessagesforloggedinclients', [MessagesController::class,'fetchmessagesforloggedinclients']);
    Route::get('fetchmessagesforadmins', [MessagesController::class,'fetchmessagesforadmins']);
    Route::post('updatepasswordfrominside', [UserController::class,'updatepasswordfrominside']);
    Route::post('inviteteammember', [UserController::class,'inviteteammember']);
    Route::get('fetchteammembers', [UserController::class,'fetchteammembers']);
    Route::get('getteammemberbyid/{id}', [UserController::class,'getteammemberbyid']);
    Route::post('updateteammember', [UserController::class,'updateteammember']);
    Route::get('fetchclientprojects', [UserController::class,'fetchclientprojects']);
    Route::get('fetchclientdashboard', [UserController::class,'fetchclientdashboard']);
    Route::get('getloggedinclient', [UserController::class,'getloggedinclient']);
    Route::post('editproduct', [productController::class,'editproduct']);
    Route::post('deleteproduct', [productController::class,'deleteproduct']);
    Route::post('editclient', [UserController::class, 'editclient']);
    Route::post('editproject', [ProjectController::class, 'editproject']);
    Route::post('deleteproject', [ProjectController::class, 'deleteproject']);
    Route::post('loggedinuseruploadprofilepicture', [UserController::class, 'loggedinuseruploadprofilepicture']);
    Route::post('postpicture', [UserController::class, 'postpicture']);
    Route::post('createsubitem', [InventoryController::class, 'createsubitem']);
    Route::post('editsubitem', [InventoryController::class, 'editsubitem']);
    Route::post('addstock', [InventoryController::class, 'addstock']);
    Route::post('createproject', [ProjectController::class, 'createProject']);
    Route::post('updatestockreff', [InventoryController::class, 'updateStock']);
});

Route::get('fetchitems', [InventoryController::class,'fetchitems']);
Route::get('getitembyid/{id}', [InventoryController::class,'getitembyid']);
Route::get('fetchsubitems', [InventoryController::class, 'fetchsubitems']);
Route::get('getsubitembyid/{id}', [InventoryController::class,'getsubitembyid']);
Route::get('viewavailablestocks', [InventoryController::class, 'viewavailablestocks']);
Route::get('viewinventory', [InventoryController::class, 'viewinventory']);
Route::get('viewstockentries', [InventoryController::class, 'viewstockentries']);
Route::get('fetchaudittrail', [InventoryController::class, 'fetchaudittrail']);
Route::get('getsubitemsbyitemid/{id}', [InventoryController::class, 'getsubitemsbyitemid']);
