<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name'               => fake()->name(),
            'last_name'                => fake()->lastName(),
            'email'                    => fake()->unique()->email(),
            'phone'                    => fake()->unique()->numberBetween(6000000000, 9999999999),
            'email_verified_at'        => now(),
            'password'                 => Hash::make(12345678),
            'remember_token'           => Str::random(10),
            'email_verification_token' => '',
            'profile_picture'          => fake()->image(),
            'type'                     => 'Admin',
            'billable_name'            => fake()->name(),
            'address1'                 => fake()->address(),
            'address2'                 => fake()->address(),
            'zip_code'                 => fake()->numberBetween(111111, 999999),
            'city_id'                  => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
