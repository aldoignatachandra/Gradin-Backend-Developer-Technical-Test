<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CourierControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    // ==========================================
    // INDEX TESTS
    // ==========================================

    public function test_can_list_couriers_with_pagination(): void
    {
        Courier::factory(20)->create();

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone', 'level', 'address', 'is_active', 'registered_at'],
                ],
                'links',
                'total',
                'current_page',
                'last_page',
            ]);
    }

    public function test_couriers_sorted_by_name_by_default(): void
    {
        Courier::factory()->create(['name' => 'Zeta']);
        Courier::factory()->create(['name' => 'Alpha']);

        $response = $this->getJson('/api/couriers');

        $data = $response->json('data');
        $this->assertEquals('Alpha', $data[0]['name']);
        $this->assertEquals('Zeta', $data[1]['name']);
    }

    public function test_can_sort_by_registered_at(): void
    {
        $old = Courier::factory()->create(['registered_at' => now()->subYear()]);
        $new = Courier::factory()->create(['registered_at' => now()]);

        $response = $this->getJson('/api/couriers?sort=registered_at&direction=asc');

        $data = $response->json('data');
        $this->assertEquals($old->id, $data[0]['id']);
    }

    public function test_can_search_courier_by_name(): void
    {
        Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
        Courier::factory()->create(['name' => 'John Doe']);

        $response = $this->getJson('/api/couriers?search=budi+agung');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Budiono Hadi Agung');
    }

    public function test_can_filter_by_level(): void
    {
        Courier::factory()->create(['level' => 2]);
        Courier::factory()->create(['level' => 3]);
        Courier::factory()->create(['level' => 5]);

        $response = $this->getJson('/api/couriers?level=2,3');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    // ==========================================
    // SHOW TESTS
    // ==========================================

    public function test_can_show_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->getJson("/api/couriers/{$courier->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => $courier->name]);
    }

    public function test_show_returns_404_for_nonexistent_courier(): void
    {
        $response = $this->getJson('/api/couriers/999');

        $response->assertNotFound();
    }

    // ==========================================
    // STORE TESTS
    // ==========================================

    public function test_can_create_courier(): void
    {
        $data = [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'level' => 3,
            'address' => 'Jakarta',
            'is_active' => true,
            'registered_at' => now()->toISOString(),
        ];

        $response = $this->postJson('/api/couriers', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Budi Santoso']);

        $this->assertModelExists(Courier::where('email', 'budi@example.com')->first());
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/couriers', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'level']);
    }

    public function test_store_validates_unique_email(): void
    {
        Courier::factory()->create(['email' => 'budi@example.com']);

        $response = $this->postJson('/api/couriers', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'level' => 3,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_validates_level_range(): void
    {
        $response = $this->postJson('/api/couriers', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'level' => 6,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['level']);
    }

    // ==========================================
    // UPDATE TESTS
    // ==========================================

    public function test_can_update_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->putJson("/api/couriers/{$courier->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);

        $courier->refresh();
        $this->assertEquals('Updated Name', $courier->name);
    }

    public function test_update_validates_unique_email(): void
    {
        Courier::factory()->create(['email' => 'taken@example.com']);
        $courier = Courier::factory()->create(['email' => 'budi@example.com']);

        $response = $this->putJson("/api/couriers/{$courier->id}", [
            'email' => 'taken@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // DESTROY TESTS
    // ==========================================

    public function test_can_delete_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson("/api/couriers/{$courier->id}");

        $response->assertNoContent();

        $this->assertModelMissing($courier);
    }

    public function test_destroy_returns_404_for_nonexistent_courier(): void
    {
        $response = $this->deleteJson('/api/couriers/999');

        $response->assertNotFound();
    }
}
