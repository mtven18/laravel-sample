<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_register_success()
    {
        $username = $this->faker->userName;
        $email = $this->faker->email;

        $this->postJson('/api/user/register', [
            'username' => $username,
            'email' => $email,
            'password' => 'Aa1#password',
        ])->assertCreated()->assertJsonPath('data.email', $email);

        $this->assertDatabaseHas('users', compact('username', 'email'));
    }

    public function test_register_invalid_data()
    {
        $this->postJson('/api/user/register', [
            'username' => null,
            'email' => 'fake',
            'password' => null,
        ])->assertStatus(422)->assertJsonValidationErrors([
            'username',
            'email',
            'password',
        ]);
    }

    public function test_register_unique_user()
    {
        /*** @var User $user */
        $user = User::factory()->create();

        $this->postJson('/api/user/register', [
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(422)->assertJsonValidationErrors([
            'username',
            'email',
        ]);
    }
}
