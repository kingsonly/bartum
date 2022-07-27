<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use URL;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\Http;


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

    public function confirmPayment(Request $request,$id,$projectid){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer sk_test_ba3673f03dce08fa0a3f8e362ed7baf529c3b68d' 
        ])->get("https://api.paystack.co/transaction/verify/".$id);
        // payment is made to project total amount
  
        if ($response->object() == null) {
            return response()->json(['status'=>'error', 'message'=>'project not found', 'data'=>$err],400);
        } else {
            // check if amount is equal to project amount 
            $projectModel = Project::where("id",$projectid)->first();
            if($response->object()->data->status === "success"){
                //update project status to paid
                $amountPaid = ($response->object()->data->amount/100);
                $actualAmount = ($response->object()->data->amount/100);
                // get all payment and check if payment has been compleated 
                $paymentModelPaySum = Payment::where("project_id",$projectid)->sum("amount");
                if($amountPaid + $paymentModelPaySum >= $projectModel->price){
                    $projectModel->payment_status = 1;
                }

                $paymentModel = new Payment();
                $paymentModel->project_id = $projectid;
                $paymentModel->amount = $amountPaid;
                $paymentModel->actual_amount = $actualAmount;
                $paymentModel->status = 1;
                $projectModel->mode_of_payment = $request->modeofpayment;
                $projectModel->type_of_payment = $request->typeofpayment;
                if($request->paymentduration > 0){
                    $projectModel->payment_duration = date('d/m/Y', strtotime('+'.$request->paymentduration.' months'));
                }else{
                    $projectModel->payment_duration = 0;
                }
                
                 
                if($projectModel->save() and $paymentModel->save()){
                    return response()->json(['status'=>'success', 'message'=>"Payment was a success", 'data'=>$response->object()],200);
                }else{
                    return response()->json(['status'=>'error', 'message'=>"could not update Project payment status", 'data'=>$projectModel],400);
                }
                    
                

            }else{
                return response()->json(['status'=>'error', 'message'=>'Could not get a feedback from paystack', 'data'=>$response->object()],400);
            }
            
        }
    }

    public function addPayment(Request $request,$projectid ){
        // create a payment table migration and log all payments
        $projectModel = Project::where("id",$projectid)->first();
        // create a customer 
        $firstname = "Bartum";
        $lastname = str_shuffle("1234567890ghhgABC");
        $phone = "+".str_shuffle("12345678901");
        $email = "bartum_".str_shuffle("1234567890ABC")."@bartumenergy.com";
        $data = [
            "first_name" => $firstname,
            "last_name" => $lastname,
            "phone" =>$phone,
            "email" =>$email
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer sk_test_a81674220468d407c42fdd4291f225f590066c02' 
        ])->post("https://api.paystack.co/customer",$data);
    
        if($response->object()->status == "success"){
            $customerDetails = $response->object()->data;
            $url = "https://api.paystack.co/dedicated_account";

            $accountPostDetails = [
                "customer" => $customerDetails->id,
                "preferred_bank" => "test-bank",
            ];

            $accountCreation = Http::withHeaders([
                'Authorization' => 'Bearer sk_test_a81674220468d407c42fdd4291f225f590066c02' 
            ])->post($url,$accountPostDetails);

            if($accountCreation->object()->status == "success"){
                // save relevant information to project table .
                $accountNumberDetails = $accountCreation->object()->data;

                $projectModel->mode_of_payment = $request->modeofpayment;
                $projectModel->type_of_payment = $request->typeofpayment;

                // *** note save the account details in the db  for project with the right information
                $projectModel->account_number = $request->typeofpayment;
                $projectModel->account_name = $request->typeofpayment;
                $projectModel->bank = $request->typeofpayment;

                if($request->paymentduration > 0){
                    $projectModel->payment_duration = date('d/m/Y', strtotime('+'.$request->paymentduration.' months'));
                }else{
                    $projectModel->payment_duration = 0;
                }
                
                if($projectModel->save()){
                    // send back account details
                    return response()->json(['status'=>'success', 'message'=>"Account number generated successfully", 'data'=>$accountNumberDetails],200);
                }else{
                    return response()->json(['status'=>'error', 'message'=>"Sorry account number can not be generated at this time,please try again", 'data'=>$accountNumberDetails],400);
                }
                
            }else{
                return response()->json(['status'=>'error', 'message'=>"Payment was a success", 'data'=>$accountCreation->object()],400);
            }

        }
        
    }


}
