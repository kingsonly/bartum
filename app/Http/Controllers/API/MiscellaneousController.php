<?php
//5
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as Transactions;
use App\Models\Miscellaneous;
use URL;
use DB;
use Illuminate\Support\Facades\Auth;


class MiscellaneousController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $loggedinuser = auth()->guard('sanctum')->user();
        if($loggedinuser->role != 1 && $loggedinuser->role != 2){
            return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
        }
        $model =  Miscellaneous::all();
        
        if($model){
            return response()->json(['status'=>'success', 'message'=>'Lis of all Miscellaneous items', 'data'=>$model],200);
        }
        return response()->json(['status'=>'error', 'message'=>'Could not get a feedback from creation of a new Miscellaneous ', 'data'=>$model],400);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loggedinuser = auth()->guard('sanctum')->user();
        if($loggedinuser->role != 1 && $loggedinuser->role != 2){
            return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
        }
        $model = new Miscellaneous();
        
        $model->title = $request->input("title");
        $model->status = 1;
        if($model->save()){
            return response()->json(['status'=>'success', 'message'=>'New Miscellaneous was added successfully', 'data'=>$model],200);
        }
        return response()->json(['status'=>'error', 'message'=>'Could not get a feedback from creation of a new Miscellaneous ', 'data'=>$model],400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         //
         $loggedinuser = auth()->guard('sanctum')->user();
         if($loggedinuser->role != 1 && $loggedinuser->role != 2){
             return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
         }
         $model =  Miscellaneous::where("id",$id)->first();

         if($model){
             return response()->json(['status'=>'success', 'message'=>'Lis of all Miscellaneous items', 'data'=>$model],200);
         }
         return response()->json(['status'=>'error', 'message'=>'Could not get a feedback from creation of a new Miscellaneous '],400);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $loggedinuser = auth()->guard('sanctum')->user();
        if($loggedinuser->role != 1 && $loggedinuser->role != 2){
            return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
        }
        $model =  Miscellaneous::where("id",$id)->first();
        $model->title = $request->input("title");
        
        if($model->save()){
            return response()->json(['status'=>'success', 'message'=>'Lis of all Miscellaneous items', 'data'=>$model],200);
        }
        return response()->json(['status'=>'error', 'message'=>'Could not get a feedback from creation of a new Miscellaneous '],400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        //
        $loggedinuser = auth()->guard('sanctum')->user();
        if($loggedinuser->role != 1 && $loggedinuser->role != 2){
            return response()->json(['status'=>'error', 'message'=>'you dont have write and edit access',  'data' =>''],400);
        }
        $model =  Miscellaneous::where("id",$id)->first();
        if($model){
            if($model->delete()){
                return response()->json(['status'=>'success', 'message'=>'We could not Delete the miscellaneous with id '.$id, 'data'=>'We could not Delete the miscellaneous with id '.$id],200);
            }else{

            }
        }else{
            return response()->json(['status'=>'error', 'message'=>'There was no miscellaneous with the id', 'data'=>"There was no miscellaneous with the id ".$id],400);
        }
        
        
    }
}
