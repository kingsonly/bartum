<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;


class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_that_params_validation_works()
    {
        $this->userCreate();
        
        //Create a new project and then make a payment to the new project .
        $projectModel = new Project();
        $projectModel->projectname =  "1234";
        $projectModel->projecttype =  "1234";
        $projectModel->solarsystemsize =  "1234";
        $projectModel->numberofpanels =  "1234";

        $projectModel->numberofbatteries =  "1234";
        $projectModel->description =  "1234";
        $projectModel->productid =  "1234";
        $projectModel->installationtype =  "1234";


        $projectModel->status =  "1";
        $projectModel->clientid =  "1";
        $projectModel->lgaid = "1";
        $projectModel->price =  "1000";
        $projectModel->stateid =  "10";
        $projectModel->projectcode =  str_shuffle("1234567890ABC");
        $projectModel->addedby =  1;
        if($projectModel->save()){
            // payment details params
            $params =[

            ]; 
            $response = $this->postJson("/api/backdoorpayment/".$projectModel->id,$params);
            $response->assertStatus(400);
        }
    }

    public function test_that_project_exist()
    {
        $this->userCreate();
        // payment details params
        $params =[
            "amount" => "233",
            "actual_amount" => "233",
            "typeofpayment" => "233",
            "modeofpayment" => "233",
            "paymentduration" => "233",
        ]; 
        $response = $this->postJson("/api/backdoorpayment/0",$params);
        $response->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'project does not exist');
    }

    public function test_that_payment_was_a_success()
    {
        $this->userCreate();
        //Create a new project and then make a payment to the new project .
        $projectModel = new Project();
        $projectModel->projectname =  "1234";
        $projectModel->projecttype =  "1234";
        $projectModel->solarsystemsize =  "1234";
        $projectModel->numberofpanels =  "1234";

        $projectModel->numberofbatteries =  "1234";
        $projectModel->description =  "1234";
        $projectModel->productid =  "1234";
        $projectModel->installationtype =  "1234";


        $projectModel->status =  "1";
        $projectModel->clientid =  "1";
        $projectModel->lgaid = "1";
        $projectModel->price =  "1000";
        $projectModel->stateid =  "10";
        $projectModel->projectcode =  str_shuffle("1234567890ABC");
        $projectModel->addedby =  1;
        if($projectModel->save()){
            // payment details params
            $params =[
                "amount" => "233",
                "actual_amount" => "233",
                "typeofpayment" => "233",
                "modeofpayment" => "233",
                "paymentduration" => "233",
            ]; 
            $response = $this->postJson("/api/backdoorpayment/".$projectModel->id,$params);
            $response->assertStatus(200)->assertJsonStructure(
                ["data" => 
                    [
                        "id",
                        "project_id",
                        "amount",
                        "actual_amount",
                        "order_id",
                        "status",
                        "type_of_payment",

                    ]
                ]
            );
        }
    }

    public function test_if_its_first_project_payment_to_update_duration_and_mode()
    {
        $this->userCreate();
        //Create a new project and then make a payment to the new project .
        $projectModel = new Project();
        $projectModel->projectname =  "1234";
        $projectModel->projecttype =  "1234";
        $projectModel->solarsystemsize =  "1234";
        $projectModel->numberofpanels =  "1234";

        $projectModel->numberofbatteries =  "1234";
        $projectModel->description =  "1234";
        $projectModel->productid =  "1234";
        $projectModel->installationtype =  "1234";
        $projectModel->status =  "1";
        $projectModel->clientid =  "1";
        $projectModel->lgaid = "1";
        $projectModel->price =  "1000";
        $projectModel->stateid =  "10";
        $projectModel->projectcode =  str_shuffle("1234567890ABC");
        $projectModel->addedby =  1;
        if($projectModel->save()){
            // payment details params
            $params =[
                "amount" => "233",
                "actual_amount" => "233",
                "typeofpayment" => "233",
                "modeofpayment" => "233",
                "paymentduration" => "233",
            ]; 
            $response = $this->postJson("/api/backdoorpayment/".$projectModel->id,$params);
            $response->assertStatus(200)->assertJsonStructure(
                ["data" => 
                    [
                        "duration",
                        "mode_of_payment",
                    ]
                ]
            );
        }
    }

    public function test_if_user_is_loggedin(){
        $this->userCreate();
        $projectModel = new Project();
        $projectModel->projectname =  "1234";
        $projectModel->projecttype =  "1234";
        $projectModel->solarsystemsize =  "1234";
        $projectModel->numberofpanels =  "1234";

        $projectModel->numberofbatteries =  "1234";
        $projectModel->description =  "1234";
        $projectModel->productid =  "1234";
        $projectModel->installationtype =  "1234";
        $projectModel->status =  "1";
        $projectModel->clientid =  "1";
        $projectModel->lgaid = "1";
        $projectModel->price =  "1000";
        $projectModel->stateid =  "10";
        $projectModel->projectcode =  str_shuffle("1234567890ABC");
        $projectModel->addedby =  1;
        if($projectModel->save()){
            $params =[
                "amount" => "233",
                "actual_amount" => "233",
                "typeofpayment" => "233",
                "modeofpayment" => "233",
                "paymentduration" => "233",
            ]; 
            $response = $this->postJson("/api/backdoorpayment/".$projectModel->id,$params);
        
            $response->assertOk();
        }
    }
    
    public function test_if_user_has_access_to_enter_payment(){
       $this->userpriveCreate();
      
        $params =[
            "amount" => "233",
            "actual_amount" => "233",
            "typeofpayment" => "233",
            "modeofpayment" => "233",
            "paymentduration" => "233",
        ]; 
        $response = $this->postJson("/api/backdoorpayment/1",$params);
    
        $response->assertJsonFragment(['message'=>'User does not have priv to call this route ']);
        
    }

    public function test_if_user_is_not_loggedin_when_creating_a_new_project(){
        
        $response = $this->postJson("/api/createproject/");
    
        $response->assertStatus(401)->assertJsonFragment(
            [
                "message" => "Unauthenticated."
            ]);
         
     }

    public function test_that_validation_of_request_data_is_functional_when_creating_a_new_project(){
        $this->userCreate();
        $data = [];
        $this->postJson("/api/createproject/",$data)->assertStatus(400)->assertJsonFragment(
            [
                "message" => "You cant leave any field empty."
            ]);
         ;
    }


}
