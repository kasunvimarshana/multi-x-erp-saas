<?php

namespace Tests\Feature\Api;

use App\Models\Tenant;
use App\Modules\Inventory\Models\Product;
use Database\Factories\Inventory\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\FeatureTestCase;

/**
 * Product API Test
 * 
 * Tests the Product API endpoints.
 */
class ProductApiTest extends FeatureTestCase
{
    use RefreshDatabase;

    private string $baseUri = '/api/v1/products';

    /**
     * Setup the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure authenticated user for all tests
        $this->actingAsUserWithPermissions([
            'view-products',
            'create-products',
            'update-products',
            'delete-products',
        ]);
    }

    /** @test */
    public function it_can_list_products()
    {
        // Create products for this tenant
        Product::factory()
            ->count(3)
            ->forTenant($this->tenant)
            ->create();

        // Create products for another tenant (should not be returned)
        $otherTenant = Tenant::factory()->create();
        Product::factory()
            ->count(2)
            ->forTenant($otherTenant)
            ->create();

        $response = $this->getJson($this->baseUri);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'sku',
                            'name',
                            'type',
                            'buying_price',
                            'selling_price',
                            'is_active',
                        ],
                    ],
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $productData = [
            'sku' => 'SKU-12345',
            'name' => 'Test Product',
            'type' => 'inventory',
            'description' => 'Test product description',
            'buying_price' => 100.00,
            'selling_price' => 150.00,
            'mrp' => 160.00,
            'track_inventory' => true,
            'reorder_level' => 20,
            'min_stock_level' => 10,
            'max_stock_level' => 200,
            'is_active' => true,
        ];

        $response = $this->postJson($this->baseUri, $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'sku',
                    'name',
                    'type',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'sku' => 'SKU-12345',
                    'name' => 'Test Product',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'tenant_id' => $this->tenant->id,
            'sku' => 'SKU-12345',
            'name' => 'Test Product',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_product()
    {
        $response = $this->postJson($this->baseUri, []);

        $this->assertValidationErrors($response, [
            'sku',
            'name',
            'type',
        ]);
    }

    /** @test */
    public function it_can_show_a_single_product()
    {
        $product = Product::factory()
            ->forTenant($this->tenant)
            ->create();

        $response = $this->getJson("{$this->baseUri}/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'sku',
                    'name',
                    'type',
                    'buying_price',
                    'selling_price',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                ],
            ]);
    }

    /** @test */
    public function it_cannot_show_product_from_another_tenant()
    {
        $otherTenant = Tenant::factory()->create();
        $product = Product::factory()
            ->forTenant($otherTenant)
            ->create();

        $response = $this->getJson("{$this->baseUri}/{$product->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $product = Product::factory()
            ->forTenant($this->tenant)
            ->create();

        $updateData = [
            'name' => 'Updated Product Name',
            'selling_price' => 200.00,
            'is_active' => false,
        ];

        $response = $this->putJson("{$this->baseUri}/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => 'Updated Product Name',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_cannot_update_product_from_another_tenant()
    {
        $otherTenant = Tenant::factory()->create();
        $product = Product::factory()
            ->forTenant($otherTenant)
            ->create();

        $response = $this->putJson("{$this->baseUri}/{$product->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(404);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
            'name' => 'Hacked Name',
        ]);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::factory()
            ->forTenant($this->tenant)
            ->create();

        $response = $this->deleteJson("{$this->baseUri}/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    /** @test */
    public function it_cannot_delete_product_from_another_tenant()
    {
        $otherTenant = Tenant::factory()->create();
        $product = Product::factory()
            ->forTenant($otherTenant)
            ->create();

        $response = $this->deleteJson("{$this->baseUri}/{$product->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_filter_products_by_type()
    {
        Product::factory()
            ->forTenant($this->tenant)
            ->inventory()
            ->count(3)
            ->create();

        Product::factory()
            ->forTenant($this->tenant)
            ->service()
            ->count(2)
            ->create();

        $response = $this->getJson("{$this->baseUri}?type=inventory");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $product) {
            $this->assertEquals('inventory', $product['type']);
        }
    }

    /** @test */
    public function it_can_filter_active_products_only()
    {
        Product::factory()
            ->forTenant($this->tenant)
            ->count(3)
            ->create(['is_active' => true]);

        Product::factory()
            ->forTenant($this->tenant)
            ->count(2)
            ->inactive()
            ->create();

        $response = $this->getJson("{$this->baseUri}?active=1");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        foreach ($response->json('data') as $product) {
            $this->assertTrue($product['is_active']);
        }
    }

    /** @test */
    public function it_can_search_products_by_name()
    {
        Product::factory()
            ->forTenant($this->tenant)
            ->create(['name' => 'Laptop Computer']);

        Product::factory()
            ->forTenant($this->tenant)
            ->create(['name' => 'Desktop Computer']);

        Product::factory()
            ->forTenant($this->tenant)
            ->create(['name' => 'Mobile Phone']);

        $response = $this->getJson("{$this->baseUri}?search=computer");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function unauthenticated_users_cannot_access_products()
    {
        auth()->logout();

        $response = $this->getJson($this->baseUri);

        $response->assertStatus(401);
    }
}
