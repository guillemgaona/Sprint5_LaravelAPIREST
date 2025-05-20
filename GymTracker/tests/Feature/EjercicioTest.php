<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Ejercicio;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Http\Resources\EjercicioResource;
use App\Http\Resources\EjercicioCollection;


class EjercicioTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->admin()->create();
        $this->regularUser = User::factory()->create();
    }

    public function anyone_can_list_exercises()
    {
        Ejercicio::factory(5)->create();
        $response = $this->getJson('/api/exercises');

        $ejercicios = Ejercicio::orderBy('id_ejercicio')->get(); 

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'nombre', 'grupo_muscular', 'descripcion', 'imagen_demo']
                    ],
                    'links' => [], 'meta' => [] 
                 ]);
 
    }

    public function anyone_can_view_exercise_details()
    {
        $ejercicio = Ejercicio::factory()->create();
        $response = $this->getJson("/api/exercises/{$ejercicio->id_ejercicio}");

        $response->assertStatus(200)
                 ->assertJson(['data' => (new EjercicioResource($ejercicio))->resolve()]);
    }

    public function admin_can_create_exercise()
    {
        Passport::actingAs($this->adminUser);
        $ejercicioData = Ejercicio::factory()->make()->toArray();

        $response = $this->postJson('/api/exercises', $ejercicioData);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nombre', $ejercicioData['nombre']);
        $this->assertDatabaseHas('Ejercicio', ['nombre' => $ejercicioData['nombre']]);
    }

    public function non_admin_cannot_create_exercise()
    {
        Passport::actingAs($this->regularUser);
        $ejercicioData = Ejercicio::factory()->make()->toArray();

        $response = $this->postJson('/api/exercises', $ejercicioData);
        $response->assertStatus(403); 
    }

    public function unauthenticated_user_cannot_create_exercise()
    {
         $ejercicioData = Ejercicio::factory()->make()->toArray();
         $response = $this->postJson('/api/exercises', $ejercicioData);
         $response->assertStatus(401);
    }

    public function admin_can_update_exercise()
    {
         Passport::actingAs($this->adminUser);
         $ejercicio = Ejercicio::factory()->create();
         $updateData = ['nombre' => 'Nuevo Nombre Ejercicio', 'grupo_muscular' => 'pecho'];

         $response = $this->putJson("/api/exercises/{$ejercicio->id_ejercicio}", $updateData);

         $response->assertStatus(200)
                  ->assertJsonPath('data.nombre', 'Nuevo Nombre Ejercicio');
         $this->assertDatabaseHas('Ejercicio', ['id_ejercicio' => $ejercicio->id_ejercicio, 'nombre' => 'Nuevo Nombre Ejercicio']);
    }

    public function non_admin_cannot_update_exercise()
    {
        Passport::actingAs($this->regularUser);
        $ejercicio = Ejercicio::factory()->create();
        $updateData = ['nombre' => 'Intento Fallido'];

        $response = $this->putJson("/api/exercises/{$ejercicio->id_ejercicio}", $updateData);
        $response->assertStatus(403);
    }


    public function admin_can_delete_exercise()
    {
        Passport::actingAs($this->adminUser);
        $ejercicio = Ejercicio::factory()->create();

        $response = $this->deleteJson("/api/exercises/{$ejercicio->id_ejercicio}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('Ejercicio', ['id_ejercicio' => $ejercicio->id_ejercicio]);
    }

    public function non_admin_cannot_delete_exercise()
    {
        Passport::actingAs($this->regularUser);
        $ejercicio = Ejercicio::factory()->create();

        $response = $this->deleteJson("/api/exercises/{$ejercicio->id_ejercicio}");
        $response->assertStatus(403);
    }
}