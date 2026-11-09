<?php

namespace App\Services;

use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Full / partial sales return against an invoice (FR-1.12). Restores stock via StockService. */
class ReturnService
{
    public function __construct(private StockService $stock) {}

    /**
     * @param  array<int, array{sale_item_id:int, quantity:int}>  $items
     */
    public function processReturn(Sale $sale, array $items, ?string $reason, User $user): SaleReturn
    {
        return DB::transaction(function () use ($sale, $items, $reason, $user) {
            $return = SaleReturn::create([
                'return_no' => 'TMP-'.uniqid(),
                'sale_id' => $sale->id,
                'user_id' => $user->id,
                'return_date' => now(),
                'total_refund' => 0,
                'reason' => $reason,
            ]);

            $totalRefund = 0;
            foreach ($items as $row) {
                if ($row['quantity'] <= 0) {
                    continue;
                }
                $saleItem = $sale->items()->findOrFail($row['sale_item_id']);
                if ($row['quantity'] > $saleItem->returnableQty()) {
                    throw ValidationException::withMessages([
                        'items' => "Cannot return {$row['quantity']} of {$saleItem->product->name}; only {$saleItem->returnableQty()} returnable.",
                    ]);
                }
                $refund = $saleItem->unit_price * $row['quantity'];
                $return->items()->create([
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'quantity' => $row['quantity'],
                    'refund_amount' => $refund,
                ]);
                $saleItem->increment('returned_qty', $row['quantity']);
                $this->stock->increase($saleItem->product, (int) $row['quantity'], StockMovementType::SalesReturn, $return, $user);
                $totalRefund += $refund;
            }

            if ($totalRefund <= 0) {
                throw ValidationException::withMessages(['items' => 'Select at least one item to return.']);
            }

            $return->update([
                'return_no' => 'RET-'.str_pad((string) $return->id, 4, '0', STR_PAD_LEFT),
                'total_refund' => $totalRefund,
            ]);

            $fresh = $sale->fresh(['items']);
            $allReturned = $fresh->items->every(fn ($i) => $i->returned_qty >= $i->quantity);
            $sale->update(['status' => $allReturned ? SaleStatus::Returned : SaleStatus::PartiallyReturned]);

            return $return->fresh(['items.product', 'sale']);
        });
    }
}
