<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Product;
use App\Models\Accessories;
use Illuminate\Support\Facades\Mail;
use URL;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditrail;

class ProductController extends Controller
{

    function createproduct(Request $request)
    {

        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;


        if($loggedinuser->role != 1)
        {
          return response()->json(['status'=>'error', 'message'=>'this action is for superadmin',  'data' =>''],400);
        }

        $validator = Validator::make($request->all(),[
            'productname' => 'required|unique:products',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'productname  is required and productname must not repeat' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofpanels' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofbatteries' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required' , 'data'=>''],400);
        }

        $product  = new Product();
        if($request->input('type') == 'default'){
            $product->numberofbatteries = $request->input('numberofbatteries');
            $product->numberofpanels = $request->input('numberofpanels');
            $product->inverter_type = $request->input('invertertypeid');
            $product->panel_type = $request->input('solarpaneltypeid');
            $product->batteries_type = $request->input('batterytypeid');
            $product->numberofinverters = $request->input('numberofinverters');
        }

        if($request->input('type') == 'light'){
            $product->light_type = $request->input('lighttypeid');
            $product->numberoflight = $request->input('numberoflights');
        }
        
        $product->productname = $request->input('productname');
        $product->description = $request->input('description');
        $product->addedby = $id;
        $accessories = json_decode($request->input('accessories'));
        
        
        if($product->save()){
            $accessoriesStatus = false;
            if(!empty($accessories)){
                foreach($accessories as $accesoriesValue){
                    $accessoriesModel = new Accessories();
                    $accessoriesModel->product_id = $product->id;
                    $accessoriesModel->subitem_id = $accesoriesValue->subitem_id;
                    $accessoriesModel->quantity = $accesoriesValue->quantity;
                    if($accessoriesModel->save()){
                        $accessoriesStatus = true;
                    }else{
                        $accessoriesStatus = false;
                    }
                } 
            }
            
            if($accessoriesStatus){
                return response()->json(['status'=>'success', 'message'=>'product saved successfully', 'data'=>$product],200);
            }else{
                return response()->json(['status'=>'success', 'message'=>'accessories was not created', 'data'=>$product],200);
            }
        }


        
    }


    public function fetchproducts()
    {
        $product  = new Product();
        $all = $product::where('trashed', 0)->orderby('id','desc')->get();
        if(!empty($all)){
            foreach($all as $value){
                $value->inverter_type = $value->invertertype->name;
                $value->panel_type = $value->paneltype->name;
                $value->batteries_type = $value->batteriestype->name;
                $value->accessories = $value->accessories;
                //array_push($value->accessories,$value->accessories->subitem);
                
                unset($value->invertertype);
                unset($value->paneltype);
                unset($value->batteriestype);
                unset($value->trashed);
                unset($value->addedby);
                unset($value->created_at);
                unset($value->updated_at);
                
            }
        }
        
        return response()->json(['status'=>'success', 'message'=>'products fetched successfully', 'data'=>$all],200);
    }

    private function logAudit($email, $action, $ip, $useragent, $object="null")
    {

      $auditlog = new Auditrail();
      $auditlog->email = $email;
      $auditlog->action = $action;
      $auditlog->time =  date("Y-m-d H:i:s");
      $auditlog->ip =  $ip;
      $auditlog->useragent = $useragent;
      $auditlog->object =  $object;
      $auditlog->save();
    }


    function editproduct(Request $request)
    {

        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;


        if($loggedinuser->role != 1 && $loggedinuser->role != 2)
        {
          return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
        }

        $validator = Validator::make($request->all(),[
            'productname' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'productname  is required and productname must not repeat' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofpanels' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofbatteries' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'price' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'price  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'id  is required' , 'data'=>''],400);
        }


        $product  = new Product();
        $product = $product::where('id', $request->input('id'))->first();
        $product->price = $request->input('price');
        $product->numberofbatteries = $request->input('numberofbatteries');
        $product->numberofpanels = $request->input('numberofpanels');
        $product->productname = $request->input('productname');
        $product->description = $request->input('description');
        $product->save();
        $statement = "Edited product with name ". $product->productname;
        $changes =  json_encode($product->getChanges());
        $changes = json_encode($changes);
        $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);
        return response()->json(['status'=>'success', 'message'=>'product edited successfully', 'data'=>$product],200);
    }

    public function deleteproduct(Request $request)
    {
                $loggedinuser = auth()->guard('sanctum')->user();
                $id = $loggedinuser->id;


                if($loggedinuser->role != 1 && $loggedinuser->role != 2)
                {
                  return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
                }

                $validator = Validator::make($request->all(),[
                    'id' => 'required',
                ]);
                if($validator->fails()){
                return response()->json(['status' => 'error' , 'message'=>'id  is required' , 'data'=>''],400);
                }

              $product  = new Product();
              $product = $product::where('id', $request->input('id'))->first();
              if($product)
              {
                $product->trashed = 1;
                $product->save();

                $statement = "Deleted product with name ". $product->productname;
                $changes =  json_encode($product->getChanges());
                $changes = json_encode($changes);
                $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);
                return response()->json(['status'=>'success', 'message'=>'product delete successfully', 'data'=>''],200);
              }
              else{
                return response()->json(['status' => 'error' , 'message'=>'product not found' , 'data'=>''],400);
              }


    }




}
