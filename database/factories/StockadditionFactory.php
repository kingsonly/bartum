<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StockadditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "subitemid" => $this->faker->numerify('#'),
            "itemid" => $this->faker->numerify('#'),
            "batch_number" => $this->faker->biasedNumberBetween($min = 10000, $max = 100000),
            "price" => $this->faker->biasedNumberBetween($min = 100, $max = 200),
            "name" => $this->faker->text($maxNbChars = 40),
            "capacity" => $this->faker->randomDigit() ,
            "rating" => $this->faker->randomDigit(),
        ];
    }
}
