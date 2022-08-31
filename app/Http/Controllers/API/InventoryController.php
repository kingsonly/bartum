<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use URL;
use Illuminate\Support\Facades\Auth;
use App\Mail\Welcomeemail;
use App\Mail\Forgotpassword;
use App\Models\State;
use App\Models\Lga;
use App\Models\Client;
use App\Mail\Passwordcreation;
use App\Models\Project;
use App\Models\Product;
use DB;
use App\Models\Auditrail;
use App\Models\Item;
use App\Models\Subitem;
use App\Models\Stockaddition;




class InventoryController extends Controller
{

  public function createSubitem(Request $request)
  {
    $loggedinuser = auth()->guard('sanctum')->user();
    $id = $loggedinuser->id;

    $validator = Validator::make($request->all(),[
      'name' => 'required',
    ]);

    if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'name  is required' , 'data'=>''],400);
    }

    $validator = Validator::make($request->all(),[
      'itemid' => 'required',
    ]);

    if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'itemid  is required' , 'data'=>''],400);
    }

    $item = Item::where('id',$request->input('itemid'))->first();

    if($item == null){
      return response()->json(['status' => 'error' , 'message'=>'item  does not exist, check itemid' , 'data'=>''],400);
    }

    $oldsub = Subitem::where(['itemid'=>$request->input('itemid'), 'name'=>$request->input('name')])->first();
    if($oldsub != null)
    {
      return response()->json(['status' => 'error' , 'message'=>'you have added this before' , 'data'=>''],400);
    }

    $subitem = new Subitem();
    $subitem->itemid = $request->input('itemid');
    $subitem->name = $request->input('name');
    $subitem->userid = $id;
    $subitem->save();

    $statement = "Created a subitem  ". $request->input('name');
    $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $subitem);
    return response()->json(['status'=>'success', 'message'=> $statement, 'data'=>$subitem],200);
  }

  public function createItem(Request $request)
  {
    $loggedinuser = auth()->guard('sanctum')->user();
    $id = $loggedinuser->id;

    $validator = Validator::make($request->all(),[
      'item' => 'required',
    ]);

    if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'item name  is required' , 'data'=>''],400);
    }


    $oldItem = Item::where('item',$request->input('item'))->first();

    if($oldItem !== null){
      return response()->json(['status' => 'error' , 'message'=>'item with above name already exist' , 'data'=>''],400);
    }

    $item = new Item();
    $item->item = $request->input('item');
    
    if($item->save()){
      $statement = "Created an item  ". $request->input('item');
      $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $item);
      return response()->json(['status'=>'success', 'message'=> $statement, 'data'=>$subitem],200);

    }else{
      return response()->json(['status'=>'error', 'message'=> "We were unable to create an item at this time", 'data'=>$subitem],400);
    }
  }


  public function getitembyid($id)
  {
    $item = Item::where('id', $id)->orderBy('id', 'asc')->first();
    if($item !== null){
      return response()->json(['status'=>'success', 'message'=>'item fetched', 'data'=>$item],200);
    }else{
      return response()->json(['status'=>'error', 'message'=>'No item has the above ID', 'data'=>$item],400);
    }

  }

  public function fetchitems()
  {
    $items = Item::orderBy('id', 'asc')->all();
    return response()->json(['status'=>'success', 'message'=>'items fetched', 'data'=>$items],200);
  }

  private function logAudit($email, $action, $ip, $useragent, $object="null")
  {
    $datetime = new \DateTime("Africa/Lagos");
    $auditlog = new Auditrail();
    $auditlog->email = $email;
    $auditlog->action = $action;

    $auditlog->time =  $datetime->format("Y-m-d H:i:s");
    $auditlog->ip =  $ip;
    $auditlog->useragent = $useragent;
    $auditlog->object =  $object;
    $auditlog->save();
  }

  public function fetchsubitems(Request $request)
  {
    $query = $request->all();
    if(array_key_exists('perpage', $query))
    {//check if perpage is in query string
        $perpage = $query["perpage"];
    }
    else {
      $perpage = 10;
    }

    $query = $request->all();
    if(array_key_exists('page', $query))
    {//check if page is in query string
        $page = $query["page"];
    }
    else {
      $page = 1;
    }


    $subitems = Subitem::with('item')->where('trashed',0)->orderBy('id', 'asc')->paginate($perpage)->toarray();
    $data = $subitems["data"];
    $page = $subitems["current_page"];
    $totalpages = ceil($subitems["total"]/$perpage);
    return response()->json(['status'=>'success', 'message'=>'subitems fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);
  }

  public function viewavailablestocks()
  {
    $query = $request->all();
    if(array_key_exists('perpage', $query))
    {//check if perpage is in query string
        $perpage = $query["perpage"];
    }
    else {
      $perpage = 10;
    }

    $query = $request->all();
    if(array_key_exists('page', $query))
    {//check if page is in query string
        $page = $query["page"];
    }
    else {
      $page = 1;
    }


      $subitems = Stockaddition::with('subitem', 'item',  'Addedby')->orderBy('id', 'asc')->where('status',1)->paginate($perpage)->toarray();
      $data = $subitems["data"];
      $page = $subitems["current_page"];
      $totalpages = ceil($subitems["total"]/$perpage);
      return response()->json(['status'=>'success', 'message'=>'availabe stocks fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);
  }

  public function editStock(Request $request ,$id){
    
    $model = Stockaddition::where("id",$id)->first();
    
    $validated = Validator::make($request->all(),[
      'subitemid' => 'required|unique:posts|max:255',
      'itemid' => 'required',
      'capacity' => 'required',
      'name' => 'required',
      'price' => 'required',
      'status' => 'required',
      'rating' => 'required',
      'stockid' => 'required',
    ]);

    $validator = Validator::make($request->all(),[
      'subitemid' => 'required',
    ]);

    if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'subitemid  is required' ],400);
    }

    $validator = Validator::make($request->all(),[
      'itemid' => 'required',
    ]);

    if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'itemid  is required' ],400);
    }



    

    if(!empty($request->all())){
      return response()->json(['status'=>'success', 'message'=>'Stock updated successfully', 'data'=>$model, ],200);
      $model->subitemid = $request->input("subitemid");
      $model->itemid = $request->input("itemid");
      $model->capacity = $request->input("capacity");
      $model->name = $request->input("name");
      $model->price = $request->input("price");
      $model->status = $request->input("status");
      $model->rating = $request->input("rating");
      $model->stockid = $request->input("stockid");
      if($model->save()){
        return response()->json(['status'=>'success', 'message'=>'Stock updated successfully', 'data'=>$model, ],200);
      }else{
        return response()->json(['status'=>'error', 'message'=>'something went wrong please retry', 'data'=>$model, ],400);
      }
    }else{
      return response()->json(['status'=>'error', 'message'=>'please no  field can be left empty  ', 'data'=>$validated, ],400);
    }
    

  }


  public function viewinventory(Request $request)
  {
    $query = $request->all();
    if(array_key_exists('perpage', $query))
    {//check if perpage is in query string
        $perpage = $query["perpage"];
    }
    else {
      $perpage = 100;
    }

    $query = $request->all();
    if(array_key_exists('page', $query))
    {//check if page is in query string
        $page = $query["page"];
    }
    else {
      $page = 1;
    }

   

    if(!empty($query["status"]) and $query["status"] != "all"){
      $statusCode = 0;
      if($query["status"] == "available"){
        $statusCode = 1;
      }
      $stockDetails = Stockaddition::where([
        "itemid" => $query["id"],
        "status" => $statusCode,
      ])->with('subitem', 'item',  'Addedby')->orderBy('id', 'desc');
    }else{
      
      $stockDetails = Stockaddition::where([
        "itemid" => $query["id"],
      ])->with('subitem', 'item',  'Addedby')->orderBy('id', 'desc');
    }
      $counter = $stockDetails->count();
      $stock = $stockDetails->paginate($perpage)->toarray();
      $data =   $stock["data"];
      $page =   $stock["current_page"];
      $totalpages = ceil($stock["total"]/$perpage);
      return response()->json(['status'=>'success', 'message'=>'inventory fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage, "total" => $counter ],200);
  }


  public function viewinventorybyitem($id)
  {
    // $query = $request->all();
    // if(array_key_exists('perpage', $query))
    // {//check if perpage is in query string
    //     $perpage = $query["perpage"];
    // }
    // else {
    //   $perpage = 100;
    // }

    //$query = $request->all();

    // if(array_key_exists('page', $query))
    // {//check if page is in query string
    //     $page = $query["page"];
    // }
    // else {
    //   $page = 1;
    // }


      $stock = Stockaddition::where("itemid",$id)->orderBy('id', 'asc')->get();
      // $data =   $stock["data"];
      // $page =   $stock["current_page"];
      // $totalpages = ceil($stock["total"]/$perpage);
      foreach($stock as $key => $value){
        $value->Item;
        $value->Subitem;
      }
      return response()->json(['status'=>'success', 'message'=>'inventory fetched without pagination', 'data'=>$stock],200);
  }

  public function getsubitembyid($id)
  {
    $subitem = Subitem::with('item')->where('id', $id)->first();
    return response()->json(['status'=>'success', 'message'=>'subitem  fetched',  'data'=>$subitem],200);

  }

  

  public function editsubitem(Request $request)
  {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;

        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'name  is required' , 'data'=>''],400);
        }
        $validator = Validator::make($request->all(),[
            'itemid' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'itemid  is required' , 'data'=>''],400);
        }

        $item = Item::where('id',$request->input('itemid'))->first();
        if($item == null){
          return response()->json(['status' => 'error' , 'message'=>'item  does not exist, check itemid' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'id  is required' , 'data'=>''],400);
        }


        $subitem = Subitem::where('id', $request->input('id'))->first();
        if($subitem == null){
          return response()->json(['status' => 'error' , 'message'=>'subitem  does not exist, check id' , 'data'=>''],400);
        }

        $subitem->itemid = $request->input('itemid');
        $subitem->name = $request->input('name');
        $subitem->userid = $id;
        $subitem->save();

        $statement = "Edited a subitem  ". $request->input('name');
        $changes =  json_encode($subitem->getChanges());
        $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);

        return response()->json(['status'=>'success', 'message'=>'subitem editted', 'data'=>$subitem],200);
  }


  public function addstock(Request $request)
  {
    $loggedinuser = auth()->guard('sanctum')->user();
    $id = $loggedinuser->id;

    $validator = Validator::make($request->all(),[
        'itemid' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'itemid  is required' , 'data'=>''],400);
    }
    $validator = Validator::make($request->all(),[
        'subitemid' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'subitemid  is required' , 'data'=>''],400);
    }


    $item = Item::where('id',$request->input('itemid'))->first();
    if($item == null){
      return response()->json(['status' => 'error' , 'message'=>'item  does not exist, check itemid' , 'data'=>''],400);
    }

    $sitem = Subitem::where('id',$request->input('subitemid'))->first();
    if($sitem == null){
      return response()->json(['status' => 'error' , 'message'=>'subitem  does not exist, check subitemid' , 'data'=>''],400);
    }

    $status = false;
    for($i = 0; $i < $request->input('quantity'); $i++ ){
      $st =  new Stockaddition();
      $st->userid = $id;
      $st->subitemid = $request->input('subitemid');
      $st->itemid = $request->input('itemid');
      $st->capacity = $request->input('capacity');
      $st->rating = $request->input('rating');
      $st->name = $request->input('name');
      $st->stockid = env('REFF_PREFFIX').time();
      $re = "/\\D/"; 
      $subst = ""; 
      $price = preg_replace($re, $subst, $request->input('price'));
      $st->price =  $price;
      //$st->stockid = $request->input('stockid');

      if($st->save()){
        $status = true;
      }else{
        $status = false;
      }

    }
    
    if($status){

      return response()->json(['status'=>'success', 'message'=>'stock added', 'data'=>"all stoks have been created"],200);
    }else{
      return response()->json(['status'=>'error', 'message'=>'sorry stock could not be added please try again', 'data'=>$st],400);
    }
    
  }

  public function fetchaudittrail(Request $request)
  {
    $query = $request->all();
    if(array_key_exists('perpage', $query))
    {//check if perpage is in query string
        $perpage = $query["perpage"];
    }
    else {
      $perpage = 100;
    }

    $query = $request->all();
    if(array_key_exists('page', $query))
    {//check if page is in query string
        $page = $query["page"];
    }
    else {
      $page = 1;
    }

    $stock = Auditrail::with('user')->paginate($perpage)->toarray();
    $data =   $stock["data"];
    $page =   $stock["current_page"];
    $totalpages = ceil($stock["total"]/$perpage);
    return response()->json(['status'=>'success', 'message'=>'audit trail fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);

  }

  public function getsubitemsbyitemid($itemid)
  {
    $subitems = Subitem::where('itemid', $itemid)->orderby('name','asc')->get();
    return response()->json(['status'=>'success', 'message'=>'subitems fetched', 'data'=>$subitems],200);
  }

  /**
   * Patch method to be used for the aim of updating stock details
   * @param interger request
   * @return array object 
   */

  public function updateProjectStock(Request $request){
    
    $updateStatus = false;
    foreach(json_decode($request->details) as $value){
      $getStock = Stockaddition::where("id",$value->id)->first();
      $getStock->stockid = $value->reff;
      if($getStock->save()){
        $updateStatus = true;
      }else{
        $updateStatus = false;
      }

    }
    
    if($updateStatus){
      return response()->json(['status'=>'success', 'message'=>'All stuck product number have been updated' ],200);
    }else{
      return response()->json(['status'=>'faild', 'message'=>'Could not update inventory' ],400);
    }
  
  }

  public function deleteSubitem($id){
    $subitem = Subitem::where('id', $id)->first();
    if(!empty($subitem) and $subitem->delete()){
      return response()->json(['status' => 'success' , 'message'=>'subitem  with the name '.$subitem->name.'has been deleted successfuly' , 'data'=>$subitem],200);
    }else{
      return response()->json(['status' => 'error' , 'message'=>'subitem  does not exist, check id' , 'data'=>$subitem],400);
    }
  }

}
