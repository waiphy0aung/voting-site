<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class VoterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'voter_id' => $this->faker->numberBetween(1000,2000),
            'password' => Hash::make($this->faker->realText(20)), // password'
        ];
    }
}
