<?php

namespace Tests\Feature\ShopFlow;

use App\Enums\AdjustmentType;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private StockService $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stock = app(StockService::class);
        $this->product = Product::create([
            'category_id' => Category::create(['name' => 'Test Cat'])->id,
            'unit_id' => Unit::create(['name' => 'Piece', 'short_name' => 'pcs'])->id,
            'name' => 'Test Product', 'sku' => 'TST-001',
            'cost_price' => 10, 'sale_price' => 15, 'stock' => 0, 'alert_qty' => 5,
        ]);
        $this->actingAs(User::factory()->create());
    }

    public function test_increase_adds_stock_and_writes_movement(): void
    {
        $movement = $this->stock->increase($this->product, 20, StockMovementType::Purchase);

        $this->assertSame(20, $this->product->fresh()->stock);
        $this->assertSame(20, $movement->quantity);
        $this->assertSame(20, $movement->stock_after);
    }

    public function test_decrease_reduces_stock(): void
    {
        $this->stock->increase($this->product, 20, StockMovementType::Purchase);
        $this->stock->decrease($this->product, 7, StockMovementType::Sale);

        $this->assertSame(13, $this->product->fresh()->stock);
        $this->assertSame(-7, StockMovement::latest('id')->first()->quantity);
    }

    public function test_decrease_more_than_stock_is_rejected_and_stock_unchanged(): void
    {
        $this->stock->increase($this->product, 5, StockMovementType::Purchase);

        try {
            $this->stock->decrease($this->product, 6, StockMovementType::Sale);
            $this->fail('Expected InsufficientStockException');
        } catch (InsufficientStockException $e) {
            $this->assertSame(5, $e->available);
        }

        $this->assertSame(5, $this->product->fresh()->stock);
        $this->assertSame(1, StockMovement::count()); // only the increase was logged
    }

    public function test_adjust_records_reason_and_movement(): void
    {
        $this->stock->increase($this->product, 10, StockMovementType::Purchase);
        $adjustment = $this->stock->adjust($this->product, -2, AdjustmentType::Damaged, 'Broken in store');

        $this->assertSame(8, $this->product->fresh()->stock);
        $this->assertSame('Broken in store', $adjustment->reason);
        $this->assertSame(StockMovementType::Adjustment, StockMovement::latest('id')->first()->type);
    }
}
