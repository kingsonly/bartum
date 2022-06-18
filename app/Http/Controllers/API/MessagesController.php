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
use App\Models\Messages;
use App\Models\Projectmessage;

class MessagesController extends Controller
{
    public function sendmessage(Request $request)
    {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;

        $validator = Validator::make($request->all(),[
            'message' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'message  is required' , 'data'=>''],400);
        }
        $validator = Validator::make($request->all(),[
            'projectid' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'projectid  is required' , 'data'=>''],400);
        }

         
         $proj = new Project();
         $proj = $proj::where('id', $request->input('projectid'))->first();
         if($proj == null)
         {
            return response()->json(['status' => 'error' , 'message'=>'project does not exist' , 'data'=>''],400);
         }

         $Projectmessage  = new Projectmessage();
         $Projectmessage = $Projectmessage::where('projectid',$request->input('projectid'))->first();
         if($Projectmessage == null)
         {
            $Projectmessage1  = new Projectmessage();
            $Projectmessage1->projectid = $request->input('projectid');
            $Projectmessage1->project = $proj->projectname;
            $Projectmessage1->clientuserid = $proj->clientuserid;
            $Projectmessage1->save();
         }

         $datetime =  new \DateTime();
         $Messages = new Messages();
         $Messages->senderid = $id;
         $Messages->projectid = $request->input('projectid');
         $Messages->message = $request->input('message');
         $Messages->subject = $request->input('subject');
         $Messages->timestam = time();
         $Messages->date = $datetime->format("F d"); 
         $Messages->sendername =  $loggedinuser->firstname;
         $Messages->save();
         return response()->json(['status'=>'success', 'message'=>'message sent', 'data'=>$Messages],200);
    }


    public function fetchmessagesbyprojectid($id)
    {
          $message =  new Messages();
          $message = $message::where('projectid', $id)->orderby('id','desc')->get();  
          return response()->json(['status'=>'success', 'message'=>'messages fetched', 'data'=>$message],200);
    }

    public function fetchmessagebyid($id)
    {
        $message =  new Messages();
        $message = $message::where('id',$id)->first();
        return response()->json(['status'=>'success', 'message'=>'message fetched', 'data'=>$message],200);
   }

   public function fetchmessagesforloggedinclients()
   {
            $loggedinuser = auth()->guard('sanctum')->user();
            $id = $loggedinuser->id;
            
            $Projectmessage = new Projectmessage();
            $Projectmessage = $Projectmessage::where('clientuserid',$id)->orderby('id','desc')->get();
            $messageBox = array();
            foreach($Projectmessage as $one)
            {
                $array = array();
                $array["projectname"] = $one->project;
                $array["projectmessages"] = Messages::where('projectid',$one->projectid)->orderby('id','desc')->get();
                $messageBox[] = $array;
            }
            return response()->json(['status'=>'success', 'message'=>'messages for client fetched', 'data'=>$messageBox],200);
   } 


   public function fetchmessagesforadmins()
   {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;
        if($loggedinuser->role == 4)
        {
            return response()->json(['status' => 'error' , 'message'=>'this request is for only admins' , 'data'=>''],400);
        }
        
        $Projectmessage = new Projectmessage();
        $Projectmessage = $Projectmessage::orderby('id','desc')->get();
        $messageBox = array();
        foreach($Projectmessage as $one)
        {
            $array = array();
            $array["projectname"] = $one->project;
            $array["projectmessages"] = Messages::where('projectid',$one->projectid)->orderby('id','desc')->get();
            $messageBox[] = $array;
        }
        return response()->json(['status'=>'success', 'message'=>'messages  fetched', 'data'=>$messageBox],200);
    }


}
