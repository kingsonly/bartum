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




        public function shout()
        {
            $loggedinuser = auth()->guard('sanctum')->user();
            var_dump($loggedinuser);
        }


      public function getitembyid($id)
      {
          $item = Item::where('id', $id)->first();
          return response()->json(['status'=>'success', 'message'=>'item fetched', 'data'=>$item],200);
      }

      public function fetchitems()
      {
        $items = Item::all();
        return response()->json(['status'=>'success', 'message'=>'items fetched', 'data'=>$items],200);
      }

      public function createsubitem(Request $request)
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
        $subitem->referencenumber = $request->input('referencenumber');
        $subitem->userid = $id;
        $subitem->save();

        $statement = "Created a subitem  ". $request->input('name');
        //$changes =  json_encode($project->getChanges());
        //$changes = json_encode($changes);
        //$this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);
        $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $subitem);

        return response()->json(['status'=>'success', 'message'=>'subitem added', 'data'=>$subitem],200);
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


              $subitems = Subitem::with('item')->where('trashed',0)->paginate($perpage)->toarray();
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


              $subitems = Subitem::with('item')->where('trashed',0)->paginate($perpage)->toarray();
              $data = $subitems["data"];
              $page = $subitems["current_page"];
              $totalpages = ceil($subitems["total"]/$perpage);
              return response()->json(['status'=>'success', 'message'=>'availabe stocks fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);
          }


          public function viewinventory(Request $request)
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


              $stock = Stockaddition::with('subitem', 'item',  'Addedby')->paginate($perpage)->toarray();
              $data =   $stock["data"];
              $page =   $stock["current_page"];
              $totalpages = ceil($stock["total"]/$perpage);
              return response()->json(['status'=>'success', 'message'=>'inventory fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);
          }


          public function viewstockentries(Request $request)
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


              $stock = Stockaddition::with('subitem',  'item', 'Addedby')->where('transactiontype','addition')->paginate($perpage)->toarray();
              $data =   $stock["data"];
              $page =   $stock["current_page"];
              $totalpages = ceil($stock["total"]/$perpage);
              return response()->json(['status'=>'success', 'message'=>'stock entry fetched with pagination', 'data'=>$data, 'page'=>$page, 'totalpages'=>$totalpages, 'perpage'=>$perpage],200);
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
                $subitem->referencenumber = $request->input('referencenumber');
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

            $validator = Validator::make($request->all(),[
                'quantity' => 'required|integer',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'quantity  is required and must be a number' , 'data'=>''],400);
            }

            $item = Item::where('id',$request->input('itemid'))->first();
            if($item == null){
              return response()->json(['status' => 'error' , 'message'=>'item  does not exist, check itemid' , 'data'=>''],400);
            }

            $sitem = Subitem::where('id',$request->input('subitemid'))->first();
            if($sitem == null){
              return response()->json(['status' => 'error' , 'message'=>'subitem  does not exist, check subitemid' , 'data'=>''],400);
            }

            if($request->input('quantity') < 0)
            {
              return response()->json(['status' => 'error' , 'message'=>'quantity must be greater than 0' , 'data'=>''],400);
            }

            $st =  new Stockaddition();
            $st->userid = $id;
            $st->subitemid = $request->input('subitemid');
            $st->itemid = $request->input('itemid');
            $st->quantity = $request->input('quantity');
            $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
            $st->transactiontype = "addition";
            $st->save();

            $subitem = Subitem::where('id', $request->input('subitemid'))->first();
            $subitem->quantity =   $subitem->quantity + $request->input('quantity');
            $subitem->save();

            $statement = "Added stock  ".  $subitem->name . "  quantity :". $request->input('quantity');
            $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $st);

            return response()->json(['status'=>'success', 'message'=>'stock added', 'data'=>$st],200);
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




}
