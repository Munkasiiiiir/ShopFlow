<?php

namespace App\Services;

use App\Enums\AdjustmentType;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Central stock engine (FR-1.8, FR-1.9, FR-1.10).
 * This is the ONLY class allowed to change products.stock.
 * Every change runs in a DB transaction with a row lock and is
 * written to stock_movements, so stock can never go negative.
 */
class StockService
{
    public function increase(Product $product, int $quantity, StockMovementType $type, ?Model $reference = null, ?User $user = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        return $this->apply($product, $quantity, $type, $reference, $user);
    }

    /** @throws InsufficientStockException */
    public function decrease(Product $product, int $quantity, StockMovementType $type, ?Model $reference = null, ?User $user = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }

        return $this->apply($product, -$quantity, $type, $reference, $user);
    }

    /** Manual correction (FR-1.11). $quantity is signed (+ adds, - removes). */
    public function adjust(Product $product, int $quantity, AdjustmentType $type, string $reason, ?User $user = null): StockAdjustment
    {
        if ($quantity === 0) {
            throw new \InvalidArgumentException('Adjustment quantity cannot be zero.');
        }

        return DB::transaction(function () use ($product, $quantity, $type, $reason, $user) {
            $adjustment = StockAdjustment::create([
                'product_id' => $product->id,
                'user_id' => ($user ?? auth()->user())?->id,
                'type' => $type, 'quantity' => $quantity, 'reason' => $reason,
            ]);
            $this->apply($product, $quantity, StockMovementType::Adjustment, $adjustment, $user);

            return $adjustment;
        });
    }

    private function apply(Product $product, int $signedQuantity, StockMovementType $type, ?Model $reference, ?User $user): StockMovement
    {
        return DB::transaction(function () use ($product, $signedQuantity, $type, $reference, $user) {
            $locked = Product::whereKey($product->getKey())->lockForUpdate()->firstOrFail();
            $newStock = $locked->stock + $signedQuantity;
            if ($newStock < 0) {
                throw new InsufficientStockException($locked, abs($signedQuantity), $locked->stock);
            }
            $locked->update(['stock' => $newStock]);
            $product->stock = $newStock;

            return StockMovement::create([
                'product_id' => $locked->id,
                'user_id' => ($user ?? auth()->user())?->id,
                'type' => $type, 'quantity' => $signedQuantity, 'stock_after' => $newStock,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
            ]);
        });
    }
}
