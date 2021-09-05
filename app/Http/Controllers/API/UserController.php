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


class UserController extends Controller
{

  public function login(Request $request)
  {
      if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
          $authUser = Auth::user();
          $authUser['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;;;

          return response()->json(['status' => 'success' , 'message'=>'user logged in' , 'data'=>$authUser],200);
      }
      else{
          return response()->json(['status' => 'error' , 'message'=>'wrong details' , 'data'=>''],200);
      }
  }


  public function register(Request $request)
  {
    $validator = Validator::make($request->all(),[
        'email' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'email  is required' , 'data'=>''],200);
    }

    $validator = Validator::make($request->all(),[
        'email' => 'unique:users',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'email  has been taken' , 'data'=>''],200);
    }

    $validator = Validator::make($request->all(),[
        'password' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'password  is required' , 'data'=>''],200);
    }

    $validator = Validator::make($request->all(),[
        'firstname' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'firstname is required',  'data'=>''],200);
    }

    $validator = Validator::make($request->all(),[
        'lastname' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'lastname is required',  'data'=>''],200);
    }



        $user = new User();
        $user->email = $request->input('email');
        //$user->isactive = 1;
        $password =  $request->input('password');
        $user->firstname =  ucwords($request->input('firstname'));
        $user->lastname =  ucwords($request->input('lastname'));
        $password =  $request->input('password');
        $encriptedPassword = bcrypt($password);
        $user->password = $encriptedPassword;
        $user->passwordresetcode = substr(str_shuffle("01234567893ABCDEFGHIJKLMN01234567893ABCDEFGHIJKLMN"),-10);
        $user->emailresetcode = substr(str_shuffle("01234567893ABCDEFGHIJKLMN01234567893ABCDEFGHIJKLMN"),-10);
        $user->reverse = strrev($request->input('password'));


        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');

        if($user->save())
        {

            $user->link = time().str_shuffle("01234567893ABCDEFGHIJKLMN01234567893ABCDEFGHIJKLMN").$user->emailresetcode;

            $token =  $user->createToken('MyAuthApp')->plainTextToken;;

            $user->reverse = time();
           
            
            

             try{
                     Mail::to($user)->send(new Welcomeemail($user));
               }
                catch(\Exception $e){
                  $error = $e->getMessage();
                   echo $error;
              }

            return response()->json(['status'=>'success', 'message'=>"user created successfully",  'data' =>$user],200);
        }
        else{
            return response()->json(['status'=>'error', 'message'=>'cannot create user',  'data' =>$user],200);
        }

  }

  public function shout()
  {
      $loggedinuser = auth()->guard('sanctum')->user();
      var_dump($loggedinuser);
  }

  public function confirmemail($code)
      {
          $link = \Config::get('constants.frontend');
          $user = new User();
          $code = substr($code,-10);
          $user =  $user::where('emailresetcode',$code)->first();

          if($user != NULL)
          {
            $user->emailresetcode = "bam".str_shuffle('1234567');
            $time =  new \DateTime("Africa/Lagos");
            $user->email_verified_at =  $time->format("Y-m-d h:m:s");
            $user->save();
            $link = \Config::get('constants.frontend');
            return redirect()->away($link);
          }
          else
          {
            return redirect()->away($link);
          }
      }


      public function sendpasswordresetlink(Request $request)
      {
                    $time =  new \DateTime("Africa/Lagos");
                    $validator = Validator::make($request->all(),[
                        'email' => 'required|email',
                    ]);

                    if($validator->fails())
                    {
                      return response()->json(['status'=>'error', 'message'=>'email is required',  'data' =>''],200);
                    }
                    else
                    {
                    $email = $request->input('email');
                    $user  = User::where('email', $email)->first();


                    if(empty($user))
                    {
                        return response()->json(["status"=>"error", "message" =>"The email address you entered does not exist.", "data"=>''], 200);
                    }
                    else
                    {
                     $codex = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"),-3);
                     $user->passwordresetcode = $codex.str_shuffle('1234567');

                     $user->save();
                     $data = array(
                        'firstname' => $user->firstname,

                        'link' => \Config::get('constants.frontend').'/recoverpassword/'.time().$user->passwordresetcode,
                       );

                      try{

                            Mail::to($email)->send(New Forgotpassword($data));
                       }
                       catch(\Exception $e){

                       }
                        return response()->json(['status'=>'success' ,  'message' =>'Please check your email for further instruction', 'data'=>$data], 200); return redirect()->back()->with('success', 'Please check your email for further instruction');
                      }
                }
      }

      public function resetpassword(Request $request)
      {
        $validator = Validator::make($request->all(),[
            'password' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'password is required',  'data' =>''],200);
        }


        $validator = Validator::make($request->all(),[
            'resetcode' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'resetcode is required',  'data' =>''],200);
        }


        $password =  $request->input('password');
        $code = $request->input('resetcode');
        $code = substr($code,-10);
        $user = new User();
        $user2 =  $user::where('passwordresetcode',$code)->first();

        if($user2 == null)
        {
          return response()->json(["status"=>"error", "message" =>"code does not exist or expired", "data"=>''], 200);
        }
        else{
          $user2->passwordresetcode = time();
          $user2->password =  bcrypt($password);
          $time =  new \DateTime("Africa/Lagos");
          $user2->email_verified_at =  $time->format("Y-m-d h:m:s");
          $user2->reverse =  strrev($request->input('password'));
          $user2->save();
          return response()->json(['status'=>'success', 'message'=>'password changed successfully', 'data'=>$user2],200);
        }
      }


}
