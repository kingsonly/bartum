<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use URL;
use Illuminate\Support\Facades\Auth;
use App\Models\State;
use App\Models\Lga;
use App\Models\Client;
use App\Models\Project;
use DB;
use App\Models\Auditrail;
use App\Models\Item;
use App\Models\Subitem;
use App\Models\Stockaddition;
use App\Mail\ProjectPaymentRequest;


class PaymentController extends Controller
{
    public function getprojectpaymentbyid($id)
    {
        $pr = Project::where('id',$id)->first();
        if($pr == null)
        {
            return response()->json(['status'=>'error', 'message'=>'project not found', 'data'=>''],400);
        }
        else{
            
            $client =  Client::where('id', $pr->clientid)->first();
            $pr->client = $client;
            $pr->product =  Product::where('id',$pr->productid)->first();
            $data = [
                "amount" => $pr->price,
                "name" => $client->clientname,
                "email" => $client->email,
            ];

            return response()->json(['status'=>'success', 'message'=>'project fetched successfully', 'data'=>$data],200);
        }
    }//ends function

    public function confirmPayment(){
        
    }


}
