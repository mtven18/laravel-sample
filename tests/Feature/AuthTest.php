<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_login_success()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure([
            'token',
        ]);

        $this->getJson('/api/auth/user', [
            'Authorization' => "Bearer {$response['token']}"
        ])->assertJson([
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }

    public function test_login_invalid_data()
    {
        $this->postJson('/api/auth/login', [
            'username' => null,
            'password' => null,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'username',
                'password',
            ]);
    }

    public function test_login_failed()
    {
        /*** @var User $user */
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'username' => $this->faker->freeEmail,
            'password' => 'password',
        ])->assertUnauthorized();

        $this->postJson('/api/auth/login', [
            'username' => $user->email,
            'password' => $this->faker->text(5),
        ])->assertUnauthorized();
    }

    public function test_logout()
    {
        /*** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->postJson('/api/user/logout')->assertNoContent();
    }
}
