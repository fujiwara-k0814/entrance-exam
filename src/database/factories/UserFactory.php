<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DeliveryAddress;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ja_JP');

        $isVerifyComplete = $faker->boolean(); //true:済 false:未

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => $isVerifyComplete ? now() : null,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'postal_code' => $isVerifyComplete ? $faker->postcode() : null,
            'address' => $isVerifyComplete ? $faker->prefecture() . $faker->city() . $faker->streetAddress() : null, 
            'building' => $isVerifyComplete ? $faker->randomElement(['', $faker->secondaryAddress()]) : null,
        ];
    }

    //delivery_address_tableとの連携
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if ($user->postal_code && $user->address) {
                DeliveryAddress::create([
                    'user_id' => $user->id,
                    'postal_code' => $user->postal_code,
                    'address' => $user->address,
                    'building' => $user->building,
                ]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
