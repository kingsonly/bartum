<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Miscellaneous;
use DB;
use Illuminate\Support\Facades\DB as Transactions;
use App\Http\Controllers\Controller;

class miscellaneousController extends Controller
{
    //
    public function store(Request $request){
        if($loggedinuser->role != 1 && $loggedinuser->role != 2)
      {
        return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
      }
    }
}
