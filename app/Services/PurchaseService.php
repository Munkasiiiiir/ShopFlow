<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Purchase entry (FR-2.7, FR-2.8, FR-3.9). Stock in only via StockService. */
class PurchaseService
{
    public function __construct(private StockService $stock) {}

    /**
     * @param  array{supplier_id:int, purchase_date:string, paid_amount?:float, note?:string,
     *               items: array<int, array{product_id:int, quantity:int, unit_cost:float}>}  $data
     */
    public function createPurchase(array $data, User $user): Purchase
    {
        return DB::transaction(function () use ($data, $user) {
            $purchase = Purchase::create([
                'reference_no' => 'TMP-'.uniqid(),
                'supplier_id' => $data['supplier_id'],
                'user_id' => $user->id,
                'purchase_date' => $data['purchase_date'],
                'total_amount' => 0, 'paid_amount' => 0, 'due_amount' => 0,
                'note' => $data['note'] ?? null,
            ]);

            $total = 0;
            foreach ($data['items'] as $row) {
                $product = Product::findOrFail($row['product_id']);
                $subtotal = $row['quantity'] * $row['unit_cost'];
                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $row['quantity'],
                    'unit_cost' => $row['unit_cost'],
                    'subtotal' => $subtotal,
                ]);
                $this->stock->increase($product, (int) $row['quantity'], StockMovementType::Purchase, $purchase, $user);
                $product->update(['cost_price' => $row['unit_cost']]); // FR-2.8: latest cost
                $total += $subtotal;
            }

            $paid = (float) ($data['paid_amount'] ?? 0);
            if ($paid > $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Paid amount cannot be more than the purchase total.',
                ]);
            }

            $purchase->update([
                'reference_no' => 'PUR-'.str_pad((string) $purchase->id, 4, '0', STR_PAD_LEFT),
                'total_amount' => $total,
                'paid_amount' => $paid,
                'due_amount' => $total - $paid,
            ]);

            return $purchase->fresh(['items.product', 'supplier']);
        });
    }
}
