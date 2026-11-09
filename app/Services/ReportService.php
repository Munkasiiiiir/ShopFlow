<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Dashboard statistics and reports (FR-5.1 to FR-5.9). Read-only queries. */
class ReportService
{
    /** Cashiers only ever see their own figures (FR-5.4). */
    private function salesFor(User $user)
    {
        $q = Sale::query();
        if ($user->role === UserRole::Cashier) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    public function dashboardStats(User $user): array
    {
        $sales = $this->salesFor($user);

        return [
            'today_sales' => (clone $sales)->whereDate('sale_date', today())->sum('grand_total'),
            'today_count' => (clone $sales)->whereDate('sale_date', today())->count(),
            'total_revenue' => (clone $sales)->sum('grand_total'),
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'alert_qty')->count(),
        ];
    }

    /** @return Collection<int, object{day:string, total:float}> last $days days, oldest first */
    public function dailySales(User $user, int $days = 7): Collection
    {
        $rows = $this->salesFor($user)
            ->where('sale_date', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('DATE(sale_date) as day, SUM(grand_total) as total')
            ->groupBy('day')->pluck('total', 'day');

        return collect(range($days - 1, 0))
            ->map(fn ($i) => (object) [
                'day' => now()->subDays($i)->format('d M'),
                'total' => (float) ($rows[now()->subDays($i)->toDateString()] ?? 0),
            ]);
    }

    /** @return Collection<int, object{month:string, total:float}> Jan..Dec of current year */
    public function monthlySales(User $user): Collection
    {
        // Grouped in PHP so the query stays portable between SQLite (dev) and MySQL 8 (target).
        $rows = $this->salesFor($user)
            ->whereYear('sale_date', now()->year)
            ->get(['sale_date', 'grand_total'])
            ->groupBy(fn ($s) => (int) $s->sale_date->format('n'))
            ->map(fn ($g) => $g->sum('grand_total'));

        return collect(range(1, 12))->map(fn ($m) => (object) [
            'month' => now()->month($m)->format('M'),
            'total' => (float) ($rows[$m] ?? 0),
        ]);
    }

    public function topProducts(User $user, int $limit = 5, ?string $from = null, ?string $to = null): Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->when($user->role === UserRole::Cashier, fn ($q) => $q->where('sales.user_id', $user->id))
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->selectRaw('products.name, products.sku, SUM(sale_items.quantity) as qty, SUM(sale_items.subtotal) as amount')
            ->orderByDesc('qty')->limit($limit)->get();
    }

    public function recentSales(User $user, int $limit = 10)
    {
        return $this->salesFor($user)->with(['customer', 'user'])->latest('sale_date')->limit($limit)->get();
    }

    public function salesReport(array $filters, User $user)
    {
        return $this->salesFor($user)->with(['customer', 'user'])
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('sale_date', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('sale_date', '<=', $v))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['payment_method'] ?? null, fn ($q, $v) => $q->where('payment_method', $v))
            ->latest('sale_date');
    }

    /** Stock valuation (FR-5.7): quantity x cost price per product. */
    public function stockValuation(): Collection
    {
        return Product::with(['category', 'unit'])
            ->selectRaw('*, stock * cost_price as value')
            ->orderByDesc('value')->get();
    }

    /** Profit (FR-5.8): revenue minus COGS from sale_items cost snapshots. */
    public function profitSummary(?string $from = null, ?string $to = null): array
    {
        $items = DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->when($from, fn ($q) => $q->whereDate('sales.sale_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.sale_date', '<=', $to));

        $revenue = (clone $items)->sum(DB::raw('sale_items.unit_price * (sale_items.quantity - sale_items.returned_qty)'));
        $cogs = (clone $items)->sum(DB::raw('sale_items.cost_price * (sale_items.quantity - sale_items.returned_qty)'));

        return ['revenue' => (float) $revenue, 'cogs' => (float) $cogs, 'profit' => (float) ($revenue - $cogs)];
    }

    public function purchaseReport(array $filters)
    {
        return Purchase::with(['supplier', 'user'])
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('purchase_date', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('purchase_date', '<=', $v))
            ->when($filters['supplier_id'] ?? null, fn ($q, $v) => $q->where('supplier_id', $v))
            ->latest('purchase_date');
    }
}
