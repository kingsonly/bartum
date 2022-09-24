<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Stockaddition;

class InventoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_to_see_if_user_does_not_have_priv_to_edit()
    {
        $this->userpriveCreate();
        $id = $this->inventoryCreate();
        $response = $this->patchJson('/api/editbatchstock/'.$id->id);
        $response->assertStatus(400)->assertJsonStructure(
            [   "status",
                "message",
            ]
        )->assertJsonFragment(
            [
                "message" => "Sorry you do not have access to make an edit to this document.",
                "status" => "error"
            ]
        );
    }
    public function test_to_see_if_user_has_the_priv_to_edit()
    {
        $this->userCreate();
        $stock = $this->inventoryCreate();
        $data = [
            "subitemid" => 1,
            "itemid" => 1,
            "price" => 100,
            "name" => "test",
            "capacity" => "120 v",
            "rating" => "12 amp",
        ];
        $response = $this->patchJson('/api/editbatchstock/'.$stock->id,$data);
        $response->assertOk();
    }

    public function test_that_data_is_not_filled()
    {
        $this->userCreate();
        $stock = $this->inventoryCreate();
        $data = [
            "subitemid" => 1,
            "itemid" => 1,
            "price" => 100,
            "name" => "test",
            "capacity" => "120 v",
           
        ];
        $response = $this->patchJson('/api/editbatchstock/'.$stock->id,$data);
       // dd($response);
        $response->assertStatus(400)->assertJsonStructure(
            [   "status",
                "data",
                "message",
            ]
        )->assertJsonFragment(
            [
                "message" => "all fieled must have a value"
            ]
        );
    }

    public function test_if_content_has_been_updated_in_the_db(){
        $this->userCreate();
        $stock = $this->inventoryCreate();
        $data = [
            "subitemid" => 1,
            "itemid" => 1,
            "price" => 100,
            "name" => "new Updated test",
            "capacity" => "120 v",
            "rating" => "120 v",
           ];
        $response = $this->patchJson('/api/editbatchstock/'.$stock->id,$data)
        ->assertOk();
        //dd($response);
        $this->assertDatabaseHas('stockadditions', [
            'name' => 'new Updated test'
        ]);

    }
    public function test_if_content_multiple_content_can_be_updated(){
        $this->userCreate();
        $stock = Stockaddition::factory(4)->create(["batch_number" => "ABCDDEK"]);
        $data = [
            "subitemid" => 1,
            "itemid" => 1,
            "price" => 100,
            "name" => "new Updated for test 2",
            "capacity" => "120 v",
            "rating" => "1",
           ];
           //dd($stock[1]->id);
        $response = $this->patchJson('/api/editbatchstock/'.$stock[0]->id,$data)->assertOk();
        $allStocksWithExpectedChange = Stockaddition::where(["name" => "new Updated for test 2"])->count();
        $this->assertEquals(4, $allStocksWithExpectedChange);
        

    }
}
