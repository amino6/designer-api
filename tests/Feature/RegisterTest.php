<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register(): void
    {
        $this->postJson('/register', [
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name' => 'user',
            'email' => 'user@gmail.com'
        ]);
    }

    public function test_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'user@gmail.com']);

        $this->postJson('/register', [
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonFragment([
                "message" => "The email has already been taken."
            ]);
    }
}
