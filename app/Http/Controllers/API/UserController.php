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




class UserController extends Controller
{

  public function login(Request $request)
  {
      if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
          $authUser = Auth::user();
          if($authUser->role == 1){   $authUser['usertype'] = 'superadmin'; }
          if($authUser->role == 4){   $authUser['usertype'] = 'client'; }
          if($authUser->role == 2){   $authUser['usertype'] = 'write_user'; }
          if($authUser->role == 3){   $authUser['usertype'] = 'read_user'; }
          $authUser['profilepictureurl'] = $authUser->profilepicture == null ? NULL :  \Config::get('constants.uploadurl').'profilepicture/'.$authUser->profilepicture;

          $authUser['note'] = "Usertypes are superadmin, client, write_user and read_user";
          $authUser['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;


          return response()->json(['status' => 'success' , 'message'=>'user logged in' , 'data'=>$authUser],200);
      }
      else{
          return response()->json(['status' => 'error' , 'message'=>'wrong details' , 'data'=>''],400);
      }
  }


  public function register(Request $request)
  {
    
    $validator = Validator::make($request->all(),[
        'email' => 'required',
    ]);
    
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'email  is required' , 'data'=>''],400);
    }

    $validator = Validator::make($request->all(),[
        'email' => 'unique:users',
    ]);

    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'email  has been taken' , 'data'=>''],400);
    }

    $validator = Validator::make($request->all(),[
        'password' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'password  is required' , 'data'=>''],400);
    }

    $validator = Validator::make($request->all(),[
        'firstname' => 'required',
    ]);
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'firstname is required',  'data'=>''],400);
    }

    $validator = Validator::make($request->all(),[
        'lastname' => 'required',
    ]);
    
    if($validator->fails()){
    return response()->json(['status' => 'error' , 'message'=>'lastname is required',  'data'=>''],400);
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
            return response()->json(['status'=>'error', 'message'=>'cannot create user',  'data' =>$user],400);
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
                      return response()->json(['status'=>'error', 'message'=>'email is required',  'data' =>''],400);
                    }
                    else
                    {
                    $email = $request->input('email');
                    $user  = User::where('email', $email)->first();


                    if(empty($user))
                    {
                        return response()->json(["status"=>"error", "message" =>"The email address you entered does not exist.", "data"=>''], 400);
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
          return response()->json(['status'=>'error', 'message'=>'password is required',  'data' =>''],400);
        }


        $validator = Validator::make($request->all(),[
            'resetcode' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'resetcode is required',  'data' =>''],400);
        }


        $password =  $request->input('password');
        $code = $request->input('resetcode');
        $code = substr($code,-10);
        $user = new User();
        $user2 =  $user::where('passwordresetcode',$code)->first();

        if($user2 == null)
        {
          return response()->json(["status"=>"error", "message" =>"code does not exist or expired", "data"=>''], 400);
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

      public function notauthenticated()
      {
        return response()->json(['status'=>'error', 'message'=>'user not authenticated',  'data' =>''],401);
      }

      public function addclient(Request $request)
      {
        $loggedinuser = auth()->guard('sanctum')->user();
          $id = $loggedinuser->id;

          // if($loggedinuser->role != 1 || $loggedinuser->role != 2)
          // {
          //   return response()->json(['status'=>'error', 'message'=>'you do not have the privilege for this action',  'data' =>''],400);
          // }



          $validator = Validator::make($request->all(),[
              'clientname' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'clientname is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'clienttype' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'clienttype is required',  'data' =>''],400);
          }
          $validator = Validator::make($request->all(),[
            'phone' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'phone is required',  'data' =>''],400);
          }

          // $validator = Validator::make($request->all(),[
          //   'email' => 'unique:users',
          // ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'email is required',  'data' =>''],400);
          }
          $validator = Validator::make($request->all(),[
            'address' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'address is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'lgaid' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'lgaid is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'stateid' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'stateid is required',  'data' =>''],400);
          }


          $client = new Client();
          $client->clientname = $request->input('clientname');
          $client->clienttype = $request->input('clienttype');
          $client->email = $request->input('email');
          $client->phone = $request->input('phone');
          $client->load = $request->input('load');
          $client->housesize = $request->input('housesize');
          $client->address = $request->input('address');
          $client->userid = $id;
          $client->clientcode = str_shuffle('1234567ABC');
          $client->addedby = $id;
          $client->stateid = $request->input('stateid');
          $client->lgaid = $request->input('lgaid');
          $lga = Lga::where('lgaid',$client->lgaid)->first();
          $client->lga = $lga->lganame;
          $state = State::where('stateid',$client->stateid)->first();
          $client->state = $state->sname;
          if($client->save())
          {
            return response()->json(['status'=>'success', 'message'=>'Client saved successfully', 'data'=>$client],200);
          }
          else{
            $user->delete();
            return response()->json(['status'=>'error', 'message'=>'cannot create client',  'data' =>''],400);
          }

      }


      public function editclient(Request $request)
      {
        $loggedinuser = auth()->guard('sanctum')->user();
          $id = $loggedinuser->id;

          if($loggedinuser->role != 1 && $loggedinuser->role != 2)
          {
            return response()->json(['status'=>'error', 'message'=>'you do not have the privilege for this action',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
              'clientname' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'clientname is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'clienttype' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'clienttype is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'phone' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'phone is required',  'data' =>''],400);
          }


          $validator = Validator::make($request->all(),[
            'address' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'address is required',  'data' =>''],400);
          }


          $validator = Validator::make($request->all(),[
            'lgaid' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'lgaid is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'stateid' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'stateid is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'stateid' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'stateid is required',  'data' =>''],400);
          }

          $validator = Validator::make($request->all(),[
            'id' => 'required',
          ]);

          if($validator->fails())
          {
            return response()->json(['status'=>'error', 'message'=>'id is required',  'data' =>''],400);
          }


          $id = $request->input('id');
          $client = new Client();
          $client = $client::where('id',$id)->first();
          if($client == null)
          {
            return response()->json(['status'=>'error', 'message'=>'client not found',  'data' =>''],400);
          }

          $client->clientname = $request->input('clientname');
          $client->email = $request->input('email');
          $client->clienttype = $request->input('clienttype');
          $client->phone = $request->input('phone');
          $client->load = $request->input('load');
          $client->housesize = $request->input('housesize');
          $client->address = $request->input('address');
          $client->stateid = $request->input('stateid');
          $client->lgaid = $request->input('lgaid');
          $lga = Lga::where('lgaid',$client->lgaid)->first();
          $client->lga = $lga->lganame;
          $state = State::where('stateid',$client->stateid)->first();
          $client->state = $state->sname;
          if($client->save())
          {

            $user = new User();
            $user = $user::where('id',$client->userid)->first();
            $user->firstname = $request->input('clientname');
            $user->save();


            $statement = "Edited Client with name ". $client->clientname;
            $changes =  json_encode($client->getChanges());
            $changes = json_encode($changes);
            $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);


            return response()->json(['status'=>'success', 'message'=>'Client saved successfully', 'data'=>$client],200);
          }
          else{

            return response()->json(['status'=>'error', 'message'=>'cannot edit client',  'data' =>''],400);
          }

      }


      public function fetchstates()
      {
        $states =  State::all();
        return response()->json(['status'=>'success', 'message'=>'All States fetched successfully', 'data'=>$states],200);
      }

      public function fetchlgas()
      {
        $states =  Lga::all();
        return response()->json(['status'=>'success', 'message'=>'All LGAs fetched successfully', 'data'=>$states],200);
      }

      public function fetchlgasbystateid($stateid)
      {
        $lga = new Lga();
        $lgas = $lga::where('stateid', $stateid)->get();
        return response()->json(['status'=>'success', 'message'=>'All LGAs in the state fetched successfully', 'data'=>$lgas],200);
      }

      public function fetchclienttypes()
      {
        $cienttypes = array("Individual", "Business", "Company", "Others");
        return response()->json(['status'=>'success', 'message'=>'All Client types fetched successfully', 'data'=>$cienttypes],200);
      }

      public function fetchhousesizes()
      {
        $housetypes = array("Small", "Medium", "Large");
        return response()->json(['status'=>'success', 'message'=>'All House sizes fetched successfully', 'data'=>$housetypes],200);
      }

      public function fetchclients()
      {
        $client = new Client();
        $client = $client::orderby('clientname', 'desc')->get();
        return response()->json(['status'=>'success', 'message'=>'All  Clients fetched successfully', 'data'=>$client],200);
      }

      public function getclientbyclientid($id)
      {
        $client = new Client();
        $client = $client::where('id',$id)->first();
        return response()->json(['status'=>'success', 'message'=>'Client data fetched successfully', 'data'=>$client],200);
      }

      public function updatepasswordfrominside(Request $request)
      {
          $loggedinuser = auth()->guard('sanctum')->user();
          $id = $loggedinuser->id;

          $currentpassword =  $request->input('currentpassword');
          $newpassword = $request->input('newpassword');

          $oldpassword  = strrev($loggedinuser->reverse);
          if($oldpassword != $currentpassword)
          {
            return response()->json(['status' => 'error' , 'message'=>'current password is wrong' , 'data'=>''],400);
          }

          $user = new User();
          $user = $user::where('id',$id)->first();
          $user->password = bcrypt($newpassword);
          $user->reverse = strrev($newpassword);
          $user->save();
          return response()->json(['status'=>'success', 'message'=>'Password updated successfully', 'data'=>$user],200);
      }


      public function inviteteammember(Request $request)
      {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;
        if($loggedinuser->role != 1)
        {
          return response()->json(['status'=>'error', 'message'=>'you do not the privilege for this action',  'data' =>''],400);
        }
        $validator = Validator::make($request->all(),[
            'fullname' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'fullname is required',  'data' =>''],400);
        }
        $validator = Validator::make($request->all(),[
          'email' => 'unique:users',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'email is required',  'data' =>''],400);
        }


        $user = new User();
        $user->firstname = $request->input('fullname');
        $user->email = $request->input('email');
        $user->emailresetcode = $request->input('email');
        $password =  str_shuffle("abcd98");
        $encriptedPassword = bcrypt($password);
        $user->password = $encriptedPassword;
        $user->passwordresetcode = substr(str_shuffle("01234567893ABCDEFGHIJKLMN01234567893ABCDEFGHIJKLMN"),-10);
        $user->emailresetcode = substr(str_shuffle("01234567893ABCDEFGHIJKLMN01234567893ABCDEFGHIJKLMN"),-10);
        $user->reverse = strrev($password);
        $user->role = 3;
        $user->save();

        $codex = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"),-3);
        $user->passwordresetcode = $codex.str_shuffle('1234567');

        $user->save();
        $data = array(
           'firstname' => $user->firstname,

           'link' => \Config::get('constants.frontend').'/recoverpassword/'.time().$user->passwordresetcode,
          );

         try{

               Mail::to($user->email)->send(New Passwordcreation($data));
          }
          catch(\Exception $e){

          }
          return response()->json(['status'=>'success', 'message'=>'Team member invited, email sent', 'data'=>$user],200);
      }


      public function fetchteammembers()
      {
        $user = new User();
        $team = $user::where('role' ,'<', 4)->orderby('id','desc')->get();
        return response()->json(['status'=>'success', 'message'=>'Team members  fetched', 'data'=>$team],200);
      }

      public function fetchroles()
      {
         $roles = array(array("id"=>1, "role"=>"admin"), array("id"=>2, "role"=>"Write"), array("id"=>3, "role"=>"Read"), array("id"=>4, "role"=>"Client"));
        return response()->json(['status'=>'success', 'message'=>'Roles fetched', 'data'=>$roles],200);
      }

      public function getteammemberbyid($id)
      {
        $user = new User();
        $user = $user::where('id',$id)->first();
        return response()->json(['status'=>'success', 'message'=>'team member fetched', 'data'=>$user],200);
      }


      public function updateteammember(Request $request)
      {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;
        if($loggedinuser->role != 1)
        {
          return response()->json(['status'=>'error', 'message'=>'you do not the privilege for this action',  'data' =>''],400);
        }
        $validator = Validator::make($request->all(),[
            'fullname' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'fullname is required',  'data' =>''],400);
        }

        $validator = Validator::make($request->all(),[
          'role' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'role is required',  'data' =>''],400);
        }

        $role =  $request->input('role');
        if($role != 2 &&  $role != 3)
        {
          return response()->json(['status'=>'error', 'message'=>'role must be 2 or 3',  'data' =>''],400);
        }

        $validator = Validator::make($request->all(),[
          'phone' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'phone is required',  'data' =>''],400);
        }


        $validator = Validator::make($request->all(),[
          'id' => 'required',
        ]);

        if($validator->fails())
        {
          return response()->json(['status'=>'error', 'message'=>'id is required',  'data' =>''],400);
        }



        $user = new User();
        $user = $user::where('id', $request->input('id'))->first();
        if($user == null)
        {
          return response()->json(['status'=>'error', 'message'=>'user not found',  'data' =>''],400);
        }
        $user->firstname = $request->input('fullname');
        $user->phone = $request->input('phone');
        $user->role = $role;
        $user->save();
        return response()->json(['status'=>'success', 'message'=>'team member fetched', 'data'=>$user],200);
      }


      public function fetchclientprojects(Request $request)
      {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;
        if($loggedinuser->role != 4)
        {
          return response()->json(['status'=>'error', 'message'=>'this action is allowed for clients alone',  'data' =>''],400);
        }
        $proj = new Project();
        $proj = $proj::where('clientuserid', $id)->get();
        return response()->json(['status'=>'success', 'message'=>'data fetched', 'data'=>$proj],200);
      }

      public function fetchclientdashboard()
      {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;

        if($loggedinuser->role != 4)
        {
          return response()->json(['status'=>'error', 'message'=>'this action is allowed for clients alone',  'data' =>''],400);
        }
        $client = new Client();
        $client = $client::where('userid', $id)->first();

        $proj = new Project();
        $proj = $proj::where('clientuserid', $id)->orderby('id','desc')->get();
        $data = array();
        $data["load"] = $client->load;
        $data["projects_count"] = count($proj);

        $newprod = array();
        foreach ($proj as $key) {
          $key->productObject = Product::where('id',$key->productid)->first();
          $newprod[] = $key;
        }
        $proj = $newprod;
        if($proj == null)
        {
          $data["current_product"] = null;
        }
        else{
          $data["current_product"] = $proj[0];
        }



        $data["projects"]= $proj;

        return response()->json(['status'=>'success', 'message'=>'data fetched',  'data' =>$data],200);
      }

      public function getloggedinclient()
      {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;

        if($loggedinuser->role != 4)
        {
          return response()->json(['status'=>'error', 'message'=>'this action is allowed for clients alone',  'data' =>''],400);
        }
        $client = new Client();
        $client =  $client::where('userid', $id)->first();
        $data["clientObject"]  = $client;
        $loggedinuser->phone = $client->phone;
        $data["userObject"]  =   $loggedinuser;

        return response()->json(['status'=>'success', 'message'=>'data fetched',  'data' =>$data],200);
      }


      public function fetchadmindashboard()
      {
          $data = array();
          $data["total_clients"] = Client::orderby('id','desc')->count();
          $data["total_projects"] = Project::orderby('id','desc')->count();
          $loc  =   DB::table('projects')->distinct()->get('lgaid');
          $data["total_locations"] = count($loc);
          $data["total_installed_capacities"] = count($loc);

          $proj = DB::table('projects')->distinct()->take(4)->get('productid');
          $topselling = array();
          foreach($proj  as $one)
          {
              $prod = new Product();
              $prod = $prod::where('id', $one->productid)->first();
              $prod->salesCount = Project::where('productid', $prod->id)->count();
              $topselling[] = $prod;
          }

          $data["bestselling"] = $topselling;

          $cli = DB::table('projects')->distinct()->take(4)->get('clientid');
          $topcl = array();
          foreach($cli  as $one)
          {
              $client = new Client();
              $client = $client::where('id', $one->clientid)->first();
              $topcl[] = $client;
          }

            $data["topclient"] = $topcl;

          return response()->json(['status'=>'success', 'message'=>'data fetched',  'data' =>$data],200);
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

        public function loggedinuseruploadprofilepicture(Request $request)
        {
                  $loggedinuser = auth()->guard('sanctum')->user();
                  $id = $loggedinuser->id;

                  if(!$request->hasFile('profilepicture')) {

                      return response()->json(['error'=>'Please select a file to upload'], 200);
                  }
                  $file = $request->file('profilepicture');
                  $name = $file->getClientOriginalName();
                  $name = explode(".", $name);
                  $count = count($name);
                  $name = "profilepicture".str_shuffle("56789").time() . ".". $name[count($name) -1 ];
                  $fileSize = $file->getSize();

                  if(!$file->isValid()) {
                      return response()->json(['error'=>'Invalid file upload'], 200);
                  }

                  if($file->move('./storage/profilepicture', $name))
                  {

                      $url = \Config::get('constants.uploadurl').'profilepicture/'.$name;

                      $user = new  User();
                      $user = $user::where('id', $id)->first();
                      $user->profilepicture = $name;
                      $user->save();

                      return response()->json(['success'=>'File uploaded successfully',   'filename'=>$name, 'url'=>$url], 200);

                  }
                  else
                  {
                      return response()->json(['error'=>'There was problem uploading file. Please try again'], 200);
                  }
        }


        public function postpicture(Request $request)
        {
          $loggedinuser = auth()->guard('sanctum')->user();
          $id = $loggedinuser->id;

          if(!$request->hasFile('profilepicture')) {

              return response()->json(['error'=>'Please select a file to upload'], 200);
          }
          $file = $request->file('profilepicture');
          $name = $file->getClientOriginalName();
          $name = explode(".", $name);
          $count = count($name);
          $name = "profilepicture".str_shuffle("56789").time() . ".". $name[count($name) -1 ];
          $fileSize = $file->getSize();

          if(!$file->isValid()) {
              return response()->json(['error'=>'Invalid file upload'], 200);
          }

          if($file->move('./storage/profilepicture', $name))
          {

              $url = \Config::get('constants.uploadurl').'profilepicture/'.$name;

              $user = new  User();
              $user = $user::where('id', $id)->first();
              $user->profilepicture = $name;
              $user->save();

              return response()->json(['success'=>'File uploaded successfully',   'filename'=>$name, 'url'=>$url], 200);

          }
          else
          {
              return response()->json(['error'=>'There was problem uploading file. Please try again'], 200);
          }
        }


}
