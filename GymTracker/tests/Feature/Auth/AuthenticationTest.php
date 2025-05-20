<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase; 

    
    public function user_can_register()
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123', 
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Usuario registrado con éxito.']);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com'
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }


    public function user_cannot_register_with_invalid_data()
    {
         $response = $this->postJson('/api/register', [
            'username' => 'test', 
            'email' => 'notanemail',
            'password' => 'short',
            'password_confirmation' => 'short'
         ]);

         $response->assertStatus(422)
                  ->assertJsonValidationErrors(['email', 'password']);
         
    }

    
    public function user_cannot_register_with_existing_username_or_email()
    {
        User::factory()->create(['username' => 'existinguser', 'email' => 'existing@example.com']);

        $response1 = $this->postJson('/api/register', [
            'username' => 'existinguser', 
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response1->assertStatus(422)->assertJsonValidationErrors(['username']);

        $response2 = $this->postJson('/api/register', [
            'username' => 'newuser',
            'email' => 'existing@example.com', 
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response2->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }


    public function user_cannot_login_with_incorrect_credentials()
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}