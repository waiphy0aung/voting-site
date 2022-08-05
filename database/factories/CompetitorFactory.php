<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CompetitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $arr = ['performance','prince','princess'];
        $role = Arr::random($arr);
        return [
            'name' => $this->faker->name(),
            'role' => $role,
        ];
    }
}
