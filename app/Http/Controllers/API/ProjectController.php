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


class ProjectController extends Controller
{
     public function addproject(Request $request)
     {
        $loggedinuser = auth()->guard('sanctum')->user();
        $id = $loggedinuser->id;

        $validator = Validator::make($request->all(),[
            'projectname' => 'required|unique:projects',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'projectname  is required and projectname must not repeat' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'projecttype' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'projecttype  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'solarsystemsize' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'solarsystemsize  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofbatteries' => 'required|integer|min:0',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofbatteries  is required, must be a number' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'productid' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'productid  is required' , 'data'=>''],400);
        }


        $validator = Validator::make($request->all(),[
            'installationtype' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'installationtype  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'status' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'status  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'clientid' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'clientid  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'lgaid' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'lgaid  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'price' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'Price  is required' , 'data'=>''],400);
        }

        // $validator = Validator::make($request->all(),[
        //     'numberofinverters' => 'required|integer|min:0',
        // ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofinverters  is required, must be a number' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'numberofpanels' => 'required|integer|min:0',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required, must be a number' , 'data'=>''],400);
        }

        // $validator = Validator::make($request->all(),[
        //     'batterytypeid' => 'required|integer|min:1',
        // ]);

        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'batterytypeid  is required' , 'data'=>''],400);
        }

        // $validator = Validator::make($request->all(),[
        //     'invertertypeid' => 'required|integer|min:1',
        // ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'invertertypeid  is required' , 'data'=>''],400);
        }

        // $validator = Validator::make($request->all(),[
        //     'solarpaneltypeid' => 'required|integer|min:1',
        // ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'solarpaneltypeid  is required' , 'data'=>''],400);
        }


        $lga = $lga2 = Lga::where('lgaid',$request->input('lgaid'))->first();

        if($lga == null)
        {
            return response()->json(['status' => 'error' , 'message'=>'Lga is not found' , 'data'=>''],400);
        }


        $cl  =  Client::where('id',$request->clientid)->first();
        if($cl == null)
        {
            return response()->json(['status' => 'error' , 'message'=>'Client is not found' , 'data'=>''],400);
        }

        // $batterytype = Subitem::where('id', $request->input('batterytypeid'))->first();
        // if($request->input('numberofbatteries') > $batterytype->quantity)
        // {
        //   $message = $batterytype->name. " has only". $batterytype->quantity ." left";
        //   return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
        // }

        // $solarpaneltype = Subitem::where('id', $request->input('solarpaneltypeid'))->first();
        // if($request->input('numberofpanels') > $solarpaneltype->quantity)
        // {
        //   $message = $solarpaneltype->name. " has only". $solarpaneltype->quantity ." left";
        //   return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
        // }


        // $invertertype = Subitem::where('id', $request->input('invertertypeid'))->first();
        // if($request->input('numberofinverters') > $invertertype->quantity)
        // {
        //   $message = $invertertype->name. " has only". $invertertype->quantity ." left";
        //   return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
        // }


        $lga = $lga->lganame;

        $state = $state2 =  State::where('stateid', $lga2->stateid)->first();
        $state = $state->sname;

        $project = new Project();
        $project->projectname =  $request->input('projectname');
        $project->projecttype =  $request->input('projecttype');
        $project->solarsystemsize =  $request->input('solarsystemsize');
        $project->numberofpanels =  $request->input('numberofpanels');

        $project->numberofbatteries =  $request->input('numberofbatteries');
        $project->description =  $request->input('description');
        $project->productid =  $request->input('productid');
        $project->installationtype =  $request->input('installationtype');


        $project->status =  $request->input('status');
        $project->clientid =  $request->input('clientid');
        $project->lgaid =  $request->input('lgaid');
        $project->price =  $request->input('price');

        $project->lga =  $lga;
        $project->state =  $state;
        $project->stateid =  $state2->stateid;
        $project->projectcode =  str_shuffle("123456789ABC");
        $client  =  Client::where('id',$request->clientid)->first();
        $project->clientuserid =  $client->userid;
        $project->addedby =  $id;
        //$project->numberofinverters =  $request->input('numberofinverters');
        // $project->batterytypeid =  $request->input('batterytypeid');
        // $project->invertertypeid =  $request->input('invertertypeid');
        // $project->solarpaneltypeid =  $request->input('solarpaneltypeid');

        if($project->save())
        {
            $st = new Stockaddition();
            // $st->itemid = $batterytype->itemid;
            $st->subitemid = 1 ; //$batterytype->id;
            $st->quantity = $request->input('numberofbatteries');
            $st->userid = $id;
            $st->transactiontype = "sold";
            $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
            $st->projecttid = $project->id;
            if($st->quantity > 0){
            $st->save();
             }
            //$batterytype->quantity = $batterytype->quantity - $request->input('numberofbatteries');
            //$batterytype->save();

            $st = new Stockaddition();
            // $st->itemid = $solarpaneltype->itemid;
            $st->subitemid = 1;//$solarpaneltype->id;
            $st->quantity = $request->input('numberofpanels');
            $st->userid = $id;
            $st->transactiontype = "sold";
            $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
            $st->projecttid = $project->id;
              if($st->quantity > 0){
                $st->save();
              }
            //$solarpaneltype->quantity = $solarpaneltype->quantity - $request->input('numberofpanels');
            //$solarpaneltype->save();

            $st = new Stockaddition();
            // $st->itemid = $invertertype->itemid;
            $st->subitemid = 1;//$invertertype->id;
            $st->quantity = $request->input('numberofinverters');
            $st->userid = $id;
            $st->transactiontype = "sold";
            $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
            $st->projecttid = $project->id;
            if($st->quantity > 0){
                $st->save();
             }
            //$invertertype->quantity = $invertertype->quantity - $request->input('numberofinverters');
            //$invertertype->save();

            $statement = "Added Project  with name ". $project->projectname;

            $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $project);


             // send email to client for paymet, note the type or form of payment.
             try{

                Mail::to($client->email)->send(New ProjectPaymentRequest($project));
           }
           catch(\Exception $e){

           }
            return response()->json(['status'=>'success', 'message'=>'project saved successfully', 'data'=>$project],200);
        }
        else{
            return response()->json(['status'=>'error', 'message'=>'could not add project', 'data'=>''],400);
        }

    }//ends function


    public function fetchprojects()
    {
        $array = Project::where('trashed',0)->orderby('id','desc')->get();
        $pr = array();
        foreach($array as $one)
        {
               $cl = new Client();
               $client =  $cl::where('id', $one->clientid)->first();
               $one->client = $client;
               $pr[] = $one;
        }
        return response()->json(['status'=>'success', 'message'=>'projects fetched successfully', 'data'=>$pr],200);
    }

    public function getprojectbyid($id)
    {
        $pr = Project::where('id',$id)->first();
        if($pr == null)
        {
            return response()->json(['status'=>'error', 'message'=>'project not found', 'data'=>''],400);
        }
        else{
            $cl = new Client();
            $client =  $cl::where('id', $pr->clientid)->first();
            $pr->client = $client;
            $pr->product =  Product::where('id',$pr->productid)->first();
            return response()->json(['status'=>'success', 'message'=>'project fetched successfully', 'data'=>$pr],200);
        }
    }//ends function


    public function fetchavailableprojectstatus()
    {
        $arrayofstatus = array("None","Completed", "Ongoing");
        return response()->json(['status'=>'success', 'message'=>'status fetched successfully', 'data'=>$arrayofstatus],200);

    }

    public function fetchavailableprojecttypes()
    {
        $arrayofstatus = array("Residential","Commercial", "Energy storage systems", "Mini grids");
        return response()->json(['status'=>'success', 'message'=>'types fetched successfully', 'data'=>$arrayofstatus],200);

    }

    public function fetchreport(Request $request)
    {
        $alldata = array();

        $proj = DB::table('projects')->where('trashed',0)->distinct()->take(4)->get('productid');
        $topselling = array();
        foreach($proj  as $one)
        {
            $prod = new Product();
            $prod = $prod::where('id', $one->productid)->first();
            $prod->salesCount = Project::where('productid', $prod->id)->count();
            $topselling[] = $prod;
        }

        $totalselling = Project::where('id', '>', 0)->count();
        $alldata["total_intalled_systems"] = $totalselling;
        $alldata["top_selling"] = $topselling;
        $pie_chart_data   = DB::table('projects')->where('stateid','11120')->distinct()->take(4)->get('lgaid');
        $holder = array();
         foreach($pie_chart_data as $one)
         {
               $array = array();
               $array["Label"] = Project::where('lgaid', $one->lgaid)->first()->lga;
               $array["Series"] = Project::where('lgaid', $one->lgaid)->count();
               $holder[] = $array;

         }
         $alldata["pie_chat_data"] = $holder;


         $lastmonths = $this->getpreviousmonths(12);
         $line_graph = array();
         foreach($lastmonths as $on)
         {

            $count = $this->countSalesInMonth($on["monthdate"]);
            $array = array("Label"=>$on["monthdate"], "Series"=>$count);
            $line_graph[] = $array;

         }
         $alldata["line_graph_data"] = $line_graph;

        return response()->json(['status'=>'success', 'message'=>'reports fetched successfully', 'data'=>$alldata],200);

    }

    public function fetchmonthlylinechart()
    {
      $lastmonths = $this->getpreviousmonths(12); // change 12 to current month of that year 
      $line_graph = array();
      foreach($lastmonths as $on)
      {

         $count = $this->countSalesInMonth($on["monthdate"]);
         $array = array("Label"=>$on["monthdate"], "Series"=>$count);
         $line_graph[] = $array;

      }


     return response()->json(['status'=>'success', 'message'=>'reports fetched successfully', 'data'=>$line_graph],200);
    }

    private function countSalesInMonth($monthdate)
    {
      $query = DB::table('projects')
     ->where('created_at', 'like', '%'.$monthdate .'%')
     ->count();
     return $query;
    }


    public function fetchsalesbystateid($id)
    {
        $pie_chart_data   = DB::table('projects')->where('stateid', $id)->distinct()->take(4)->get('lgaid');
        $holder = array();
         foreach($pie_chart_data as $one)
         {
               $array = array();
               $array["Label"] = Project::where('lgaid', $one->lgaid)->first()->lga;
               $array["Series"] = Project::where('lgaid', $one->lgaid)->count();
               $holder[] = $array;

         }
         return response()->json(['status'=>'success', 'message'=>'sales fetched successfully', 'data'=>$holder],200);
    }

    private function getpreviousmonths($number)
    {
      $months = array();
      $currentMonth = date("Y-m");
      //$first = $this->spellingGetter($currentMonth);
      $months[] = array('monthdate'=>$currentMonth);
      for ($i = 1; $i <= $number; $i++)
      {
         $monthspelling = "";
         $monthdate = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
         $months[]  = array('month' => $monthspelling, 'monthdate'=>$monthdate);
      }
      return array_reverse($months);
    }



    public function fetchweeklylinechart()
    {


        $number_of_days = 90;
        $data = $this->computeDifferentWeeklyRangesForAGivenNumberOfDays($number_of_days);

        $fullGraphdata = array();
        foreach ($data as $keym)
         {
          $count = $this->computeAdminAmountBidsWithinDates($keym["newstart"], $keym["newend"]);
          $one = array('Series'=>$count, 'Label'=>$keym["end"]);
          $fullGraphdata[] = $one;
        }

        return response()->json(['status'=>'success', 'message'=>'success', 'data'=>$fullGraphdata],200);
    }


    private function computeDifferentWeeklyRangesForAGivenNumberOfDays($number_of_days)
    {
      $number_of_days = $number_of_days." "."days";
      $date = date_create(date('Y-m-d'));
      date_sub($date, date_interval_create_from_date_string($number_of_days));
      $first = date_format($date, 'Y-m-d');

      $start_date = $first;
      $end_Date = date("Y-m-d");

      $date1 = new \DateTime($start_date);
      $date2 = new \DateTime($end_Date);
      $interval = $date1->diff($date2);
      $myarray = array();
      $weeks = floor(($interval->days) / 7) + 1;

      for($i = 1; $i <= $weeks; $i++){

          $week = $date1->format("W");
          $date1->add(new \DateInterval('P7D'));
          $newEnd=  $date1->format('Y-m-d')." "."23:59:59";
          $newStart = $start_date." "."00:00:00";
          $oneArray = array("start"=>$start_date, "end"=>$date1->format('Y-m-d'), "newend"=>$newEnd, "newstart"=>$newStart);
          $myarray[] = $oneArray;

          $date1->add(new \DateInterval('P1D'));
          $start_date = $date1->format('Y-m-d');
      }
      return $myarray;
    }//end of funtion


    private function computeAdminAmountBidsWithinDates( $start, $end)
    {
      $count = DB::table('projects')
      ->whereBetween('created_at', [$start, $end])
      ->count();
      return $count;
    }

    public function fetchyearlylinechart()
    {


        $data = array("2021", "2022", "2023", "2024", "2025", "2026", "2027", "2028", "2029", "2030", "2031", "2032");
        foreach ($data as $keym)
         {
          $count = $this->computeAdminChartWithinDates($keym);
          $one = array('Series'=>$count, 'Label'=>$keym);
          $fullGraphdata[] = $one;
        }

        return response()->json(['status'=>'success', 'message'=>'success', 'data'=>$fullGraphdata],200);
    }


    private function computeAdminChartWithinDates($year)
    {

      $count = DB::table('projects')
      ->where('created_at', 'like', '%'.$year .'%')
      ->count();
      return $count;
    }

    public function editproject(Request $request)
    {
      $loggedinuser = auth()->guard('sanctum')->user();
      $id = $loggedinuser->id;

      $validator = Validator::make($request->all(),[
          'projectname' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'projectname  is required and projectname must not repeat' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'projecttype' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'projecttype  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'solarsystemsize' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'solarsystemsize  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'numberofbatteries' => 'required|integer|min:0',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'numberofbatteries  is required, must be greater than 0' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'productid' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'productid  is required' , 'data'=>''],400);
      }


      $validator = Validator::make($request->all(),[
          'installationtype' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'installationtype  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'status' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'status  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'clientid' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'clientid  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'lgaid' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'lgaid  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'price' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'Price  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'numberofinverters' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'numberofinverters  is required, must be greater than 0' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'numberofpanels' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'numberofpanels  is required, must be greater than 0' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'batterytypeid' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'batterytypeid  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'invertertypeid' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'invertertypeid  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'solarpaneltypeid' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'solarpaneltypeid  is required' , 'data'=>''],400);
      }

      $validator = Validator::make($request->all(),[
          'id' => 'required|integer|min:1',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'id  is required' , 'data'=>''],400);
      }



      $lga = $lga2 = Lga::where('lgaid',$request->input('lgaid'))->first();

      if($lga == null)
      {
          return response()->json(['status' => 'error' , 'message'=>'Lga is not found' , 'data'=>''],400);
      }


      $cl  =  Client::where('id',$request->clientid)->first();
      if($cl == null)
      {
          return response()->json(['status' => 'error' , 'message'=>'Client is not found' , 'data'=>''],400);
      }

      $oldproject = Project::where('id', $request->input('id'))->first();

      if($oldproject == null)
      {
        return response()->json(['status' => 'error' , 'message'=>'project not found' , 'data'=>''],400);
      }

      $project = Project::where('id', $request->input('id'))->first();

      $batterytype = Subitem::where('id', $request->input('batterytypeid'))->first();
      $oldbatterytype = Subitem::where('id', $request->input('batterytypeid'))->first();
      if($project->batterytypeid != $request->input('batterytypeid'))
      {

        if($request->input('numberofbatteries') > $batterytype->quantity)
        {
          $message = $batterytype->name. " has only". $batterytype->quantity ." left, so you cannot edit";
          return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
        }
      }
      if($project->batterytypeid == $request->input('batterytypeid'))
      {
          $difference = $request->input('numberofbatteries') - $project->numberofbatteries;

          if($difference > $batterytype->quantity)
          {

              $message = $batterytype->name. " has only". $batterytype->quantity ." left, so you cannot edit";
              return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
          }
      }


            $solarpaneltype = Subitem::where('id', $request->input('solarpaneltypeid'))->first();
            $oldsolarpaneltype = Subitem::where('id', $request->input('solarpaneltypeid'))->first();
            if($project->solarpaneltypeid != $request->input('solarpaneltypeid'))
            {

              if($request->input('numberofpanels') > $solarpaneltype->quantity)
              {
                $message = $solarpaneltype->name. " has only". $solarpaneltype->quantity ." left, so you cannot edit";
                return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
              }
            }
            if($project->solarpaneltypeid == $request->input('solarpaneltypeid'))
            {
                $difference = $request->input('numberofpanels') - $project->numberofpanels;

                if($difference > $solarpaneltype->quantity)
                {

                    $message = $solarpaneltype->name. " has only". $solarpaneltype->quantity ." left, so you cannot edit";
                    return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
                }
            }



            $invertertype = Subitem::where('id', $request->input('invertertypeid'))->first();
            $oldinvertertype = Subitem::where('id', $request->input('invertertypeid'))->first();
            if($project->invertertypeid != $request->input('invertertypeid'))
            {

              if($request->input('numberofinverters') > $invertertype->quantity)
              {
                $message = $invertertype->name. " has only". $invertertype->quantity ." left, so you cannot edit";
                return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
              }
            }
            if($project->invertertypeid == $request->input('invertertypeid'))
            {
                $difference = $request->input('numberofinverters') - $project->numberofinverters;

                if($difference > $invertertype->quantity)
                {

                    $message = $invertertype->name. " has only". $invertertype->quantity ." left, so you cannot edit";
                    return response()->json(['status' => 'error' , 'message'=>$message , 'data'=>''],400);
                }
            }



      $lga = $lga->lganame;

      $state = $state2 =  State::where('stateid', $lga2->stateid)->first();
      $state = $state->sname;


      $project->projectname =  $request->input('projectname');
      $project->projecttype =  $request->input('projecttype');
      $project->solarsystemsize =  $request->input('solarsystemsize');
      $project->numberofpanels =  $request->input('numberofpanels');

      $project->numberofbatteries =  $request->input('numberofbatteries');
      $project->description =  $request->input('description');
      $project->productid =  $request->input('productid');
      $project->installationtype =  $request->input('installationtype');


      $project->status =  $request->input('status');
      $project->clientid =  $request->input('clientid');
      $project->lgaid =  $request->input('lgaid');
      $project->price =  $request->input('price');

      $project->lga =  $lga;
      $project->state =  $state;
      $project->stateid =  $state2->stateid;
      $client  =  Client::where('id',$request->clientid)->first();
      $project->clientuserid =  $client->userid;

      $project->numberofinverters =  $request->input('numberofinverters');
      $project->batterytypeid =  $request->input('batterytypeid');
      $project->invertertypeid =  $request->input('invertertypeid');
      $project->solarpaneltypeid =  $request->input('solarpaneltypeid');

      if($project->save())
      {
        if($oldproject->batterytypeid == $request->input('batterytypeid'))
        {
            $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->batterytypeid])->first();
            $st->quantity = $request->input('numberofbatteries');
            $st->save();

            $diff = $request->input('numberofbatteries') - $oldproject->numberofbatteries;


               $subitem = Subitem::where('id', $st->subitemid)->first();
               $subitem->quantity =  $subitem->quantity - $diff;
               $subitem->save();

        }
        if($oldproject->batterytypeid != $request->input('batterytypeid'))
        {
            $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->batterytypeid])->first();
            if($st != null){ $st->delete();}

            $batterytype = Subitem::where('id', $request->input('batterytypeid'))->first();
            $st = new Stockaddition();

            $st->itemid = $batterytype->itemid;
            $st->subitemid = $batterytype->id;
            $st->quantity = $request->input('numberofbatteries');
            $st->userid = $id;
            $st->transactiontype = "sold";
            $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
            $st->projecttid = $project->id;
            if($st->quantity > 0){
            $st->save();
             }

               $subitemtorestore = Subitem::where('id', $oldproject->batterytypeid)->first();
               $subitemtorestore->quantity =  $subitemtorestore->quantity  + $oldproject->numberofbatteries ;
               $subitemtorestore->save();

               $subitemtoreduce = Subitem::where('id', $request->input('batterytypeid'))->first();
               $subitemtoreduce->quantity = $subitemtoreduce->quantity  - $request->input('numberofbatteries') ;
               $subitemtoreduce->save();
         }




           if($oldproject->invertertypeid == $request->input('invertertypeid'))
           {
               $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->invertertypeid])->first();
               $st->quantity = $request->input('numberofinverters');
               $st->save();

               $diff = $request->input('numberofinverters') - $oldproject->numberofinverters;


                  $subitem = Subitem::where('id', $st->subitemid)->first();
                  $subitem->quantity =  $subitem->quantity - $diff;
                  $subitem->save();

           }
           if($oldproject->invertertypeid != $request->input('invertertypeid'))
           {
               $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->invertertypeid])->first();
               if($st != null){ $st->delete();}

               $invertertype = Subitem::where('id', $request->input('invertertypeid'))->first();
               $st = new Stockaddition();

               $st->itemid = $invertertype->itemid;
               $st->subitemid = $invertertype->id;
               $st->quantity = $request->input('numberofinverters');
               $st->userid = $id;
               $st->transactiontype = "sold";
               $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
               $st->projecttid = $project->id;
               if($st->quantity > 0){
               $st->save();
                }

                  $subitemtorestore = Subitem::where('id', $oldproject->invertertypeid)->first();
                  $subitemtorestore->quantity =  $subitemtorestore->quantity  + $oldproject->numberofinverters ;
                  $subitemtorestore->save();

                  $subitemtoreduce = Subitem::where('id', $request->input('invertertypeid'))->first();
                  $subitemtoreduce->quantity = $subitemtoreduce->quantity  - $request->input('numberofinverters') ;
                  $subitemtoreduce->save();

            }



              if($oldproject->solarpaneltypeid == $request->input('solarpaneltypeid'))
              {
                  $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->solarpaneltypeid])->first();
                  $st->quantity = $request->input('numberofpanels');
                  $st->save();

                  $diff = $request->input('numberofpanels') - $oldproject->numberofpanels;


                     $subitem = Subitem::where('id', $st->subitemid)->first();
                     $subitem->quantity =  $subitem->quantity - $diff;
                     $subitem->save();

              }
              if($oldproject->solarpaneltypeid != $request->input('solarpaneltypeid'))
              {
                  $st =  Stockaddition::where(['projecttid'=>$oldproject->id, 'subitemid'=>$oldproject->solarpaneltypeid])->first();
                  if($st != null){ $st->delete();}

                  $solarpaneltype = Subitem::where('id', $request->input('solarpaneltypeid'))->first();
                  $st = new Stockaddition();

                  $st->itemid = $solarpaneltype->itemid;
                  $st->subitemid = $solarpaneltype->id;
                  $st->quantity = $request->input('numberofpanels');
                  $st->userid = $id;
                  $st->transactiontype = "sold";
                  $st->tracking = substr(str_shuffle("1234567890"),-6).substr(str_shuffle("ABCDFEGHIJKLMNPQRSTUVWZYX"),-2);
                  $st->projecttid = $project->id;
                  if($st->quantity > 0){
                  $st->save();
                   }

                     $subitemtorestore = Subitem::where('id', $oldproject->solarpaneltypeid)->first();
                     $subitemtorestore->quantity =  $subitemtorestore->quantity  + $oldproject->numberofpanels ;
                     $subitemtorestore->save();

                     $subitemtoreduce = Subitem::where('id', $request->input('solarpaneltypeid'))->first();
                     $subitemtoreduce->quantity = $subitemtoreduce->quantity  - $request->input('numberofpanels') ;
                     $subitemtoreduce->save();

               }









          $statement = "Edited Project  with name ". $project->projectname;
          $changes =  json_encode($project->getChanges());
          $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $changes);

          return response()->json(['status'=>'success', 'message'=>'project edited successfully', 'data'=>$project],200);
      }
      else{
          return response()->json(['status'=>'error', 'message'=>'could not edit project', 'data'=>''],400);
      }

    }


    public function deleteproject(Request $request)
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
      return response()->json(['status' => 'error' , 'message'=>'id  is required ' , 'data'=>''],400);
      }

      $id =  $request->input('id');
      $project = new Project();
      $project = $project::where('id',$id)->first();

      if($project == null)
      {
        return response()->json(['status'=>'error', 'message'=>'project not found',  'data' =>''],400);
      }

      $project->trashed = 1;
      $project->save();

      $statement = "Deleted Project  with name ". $project->projectname;
      $this->logAudit($loggedinuser->email, $statement, $request->ip(), $request->server('HTTP_USER_AGENT'), $project);

return response()->json(['status'=>'success', 'message'=>'project deleted successfully', 'data'=>''],200);

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






}
