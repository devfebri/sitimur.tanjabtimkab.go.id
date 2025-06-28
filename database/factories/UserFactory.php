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
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'role' => $this->faker->randomElement(['admin', 'ppk', 'verifikator', 'user']),
            'password' => bcrypt('password'), // default password
            'nip' => $this->faker->unique()->numerify('197#########'),
            'nik' => $this->faker->unique()->numerify('32##############'),
            'pangkat' => $this->faker->randomElement(['Penata', 'Pembina', 'Pengatur']),
            'jabatan' => $this->faker->jobTitle(),
            'nohp' => $this->faker->phoneNumber(),
            'jk' => $gender,
            'akses' => $this->faker->randomElement(['1', '0']), // 1 for active, 0 for inactive
            'avatar' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
