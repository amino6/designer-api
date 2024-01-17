<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertSuccessful();

        $this->assertAuthenticatedAs($user);
    }

    public function test_can_fetch_current_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/api/user')
            ->assertSuccessful();
    }

    public function test_can_logout(): void
    {
        $this->postJson('/login', [
            'email' => User::factory()->create()->email,
            'password' => 'password',
        ])->assertSuccessful();
    
        $this->assertAuthenticated();
        $this->postJson("/logout")
            ->assertSuccessful();
    
        $this->assertGuest();
        $this->getJson("/api/user")
            ->assertStatus(401);
    }
}
