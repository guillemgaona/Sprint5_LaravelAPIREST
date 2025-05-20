<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sesion;
use Laravel\Passport\Passport;
use App\Http\Resources\SesionResource;

class SesionTest extends TestCase
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

    
    public function authenticated_user_can_list_own_sessions()
    {
       
        Passport::actingAs($this->user);
        Sesion::factory(3)->create(['user_id' => $this->user->id]);
        Sesion::factory(2)->create(['user_id' => $this->otherUser->id]);
        $response = $this->getJson("/api/users/{$this->user->id}/sessions");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function admin_can_list_any_user_sessions()
    {
     
        Passport::actingAs($this->adminUser);
        Sesion::factory(2)->create(['user_id' => $this->user->id]);
        Sesion::factory(1)->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson("/api/users/{$this->user->id}/sessions");

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

   
    public function user_cannot_list_other_user_sessions()
    {
        
        Passport::actingAs($this->user);
        Sesion::factory(2)->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson("/api/users/{$this->otherUser->id}/sessions");

        $response->assertStatus(403);
    }

    public function authenticated_user_can_get_own_session_details()
    {
       
        Passport::actingAs($this->user);
        $sesion = Sesion::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/sessions/{$sesion->id_sesion}");

        $response->assertStatus(200)
                 ->assertJson(['data' => (new SesionResource($sesion->loadMissing('user', 'series')))->resolve()]);
    }

    public function user_cannot_get_other_user_session_details()
    {
        Passport::actingAs($this->user);
        $otherSesion = Sesion::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson("/api/sessions/{$otherSesion->id_sesion}");

        $response->assertStatus(403);
    }

    public function admin_can_get_any_session_details()
    {
        Passport::actingAs($this->adminUser);
        $otherSesion = Sesion::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson("/api/sessions/{$otherSesion->id_sesion}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $otherSesion->id_sesion);
    }

    
    public function authenticated_user_can_create_session()
    {
        Passport::actingAs($this->user);
        $sessionData = [
            'fecha' => now()->format('Y-m-d'),
            'nota' => 'Nota de prueba de sesión creada',
        ];

        $response = $this->postJson('/api/sessions', $sessionData);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nota', 'Nota de prueba de sesión creada');

        $this->assertDatabaseHas('Sesion', [
            'user_id' => $this->user->id,
            'nota' => 'Nota de prueba de sesión creada'
         ]);
    }

    public function authenticated_user_can_update_own_session()
    {
        Passport::actingAs($this->user);
        $sesion = Sesion::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['nota' => 'Nota de sesión actualizada correctamente'];

        $response = $this->putJson("/api/sessions/{$sesion->id_sesion}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonPath('data.nota', 'Nota de sesión actualizada correctamente');
   
        $this->assertDatabaseHas('Sesion', [
            'id_sesion' => $sesion->id_sesion,
            'nota' => 'Nota de sesión actualizada correctamente'
        ]);
    }

    public function user_cannot_update_other_user_session()
    {
        Passport::actingAs($this->user);
        $otherSesion = Sesion::factory()->create(['user_id' => $this->otherUser->id]);
        $updateData = ['nota' => 'Intentando actualizar nota ajena'];

        $response = $this->putJson("/api/sessions/{$otherSesion->id_sesion}", $updateData);

        $response->assertStatus(403);
    }

    public function authenticated_user_can_delete_own_session()
    {
        Passport::actingAs($this->user);
        $sesion = Sesion::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/sessions/{$sesion->id_sesion}");

        $response->assertStatus(200); 
        $this->assertDatabaseMissing('Sesion', ['id_sesion' => $sesion->id_sesion]);
    }

    public function user_cannot_delete_other_user_session()
    {
        Passport::actingAs($this->user);
        $otherSesion = Sesion::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->deleteJson("/api/sessions/{$otherSesion->id_sesion}");

        $response->assertStatus(403);
    }
}
