<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //TODO make seeder
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'tax_identification' => rand(10000000000, 1000000000),
            'wallet' => rand(1, 500),
            'isShopkeeper' => mt_rand(0, 1),
        ];
    }
}
