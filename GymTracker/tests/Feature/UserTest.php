<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport; 
use App\Http\Resources\UserResource; 

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->adminUser = User::factory()->admin()->create();
    }

    public function authenticated_user_can_get_own_information_from_dedicated_route()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/user'); 

        $response->assertStatus(200)
                 ->assertJson((new UserResource($this->user))->jsonSerialize());
    }

    public function authenticated_user_can_get_own_information_by_id()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
                 ->assertJson((new UserResource($this->user))->jsonSerialize());
    }

    public function admin_can_get_other_user_information_by_id()
    {
        Passport::actingAs($this->adminUser);

        $response = $this->getJson("/api/users/{$this->otherUser->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $this->otherUser->id)
                 ->assertJsonPath('data.username', $this->otherUser->username);
    }

    public function non_admin_user_cannot_get_other_user_information_by_id()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson("/api/users/{$this->otherUser->id}");

        $response->assertStatus(403);
    }

    public function unauthenticated_user_cannot_get_user_information()
    {
        $response = $this->getJson("/api/users/{$this->user->id}");
        $response->assertStatus(401); 

        $responseGuest = $this->getJson('/api/user');
        $responseGuest->assertStatus(401);
    }
}