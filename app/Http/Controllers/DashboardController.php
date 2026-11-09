<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Role-based dashboard (FR-5.1 to FR-5.4). */
class DashboardController extends Controller
{
    public function __invoke(Request $request, ReportService $reports): View
    {
        $user = $request->user();

        return view('dashboard', [
            'stats' => $reports->dashboardStats($user),
            'daily' => $reports->dailySales($user),
            'monthly' => $reports->monthlySales($user),
            'topProducts' => $reports->topProducts($user, 5),
            'recentSales' => $reports->recentSales($user, 10),
            'lowStockProducts' => Product::whereColumn('stock', '<=', 'alert_qty')
                ->orderBy('stock')
                ->limit(5)
                ->get(['id', 'name', 'sku', 'stock', 'alert_qty']),
        ]);
    }
}
