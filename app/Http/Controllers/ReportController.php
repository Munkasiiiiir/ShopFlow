<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Reports + PDF/CSV export (FR-5.5 to FR-5.10). Admin/Manager. */
class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(): View
    {
        return view('reports.index');
    }

    public function sales(Request $request): View
    {
        $filters = $request->only(['from', 'to', 'user_id', 'payment_method']);
        $sales = $this->reports->salesReport($filters, $request->user())->paginate(25)->withQueryString();
        $total = $this->reports->salesReport($filters, $request->user())->sum('grand_total');

        return view('reports.sales', ['sales' => $sales, 'total' => $total, 'filters' => $filters,
            'cashiers' => User::orderBy('name')->get()]);
    }

    public function salesPdf(Request $request)
    {
        $filters = $request->only(['from', 'to', 'user_id', 'payment_method']);
        $sales = $this->reports->salesReport($filters, $request->user())->get();

        return Pdf::loadView('pdf.sales-report', compact('sales', 'filters'))->download('sales-report.pdf');
    }

    public function salesCsv(Request $request): StreamedResponse
    {
        $filters = $request->only(['from', 'to', 'user_id', 'payment_method']);
        $sales = $this->reports->salesReport($filters, $request->user())->get();

        return response()->streamDownload(function () use ($sales) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice', 'Date', 'Customer', 'Cashier', 'Payment', 'Total']);
            foreach ($sales as $s) {
                fputcsv($out, [$s->invoice_no, $s->sale_date, $s->customer?->name, $s->user?->name, $s->payment_method->value, $s->grand_total]);
            }
            fclose($out);
        }, 'sales-report.csv', ['Content-Type' => 'text/csv']);
    }

    public function topProducts(Request $request): View
    {
        $products = $this->reports->topProducts($request->user(), 20, $request->from, $request->to);

        return view('reports.top-products', ['products' => $products, 'filters' => $request->only(['from', 'to'])]);
    }

    public function stock(): View
    {
        $products = $this->reports->stockValuation();

        return view('reports.stock', ['products' => $products, 'totalValue' => $products->sum('value')]);
    }

    public function profit(Request $request): View
    {
        $summary = $this->reports->profitSummary($request->from, $request->to);

        return view('reports.profit', ['summary' => $summary, 'filters' => $request->only(['from', 'to'])]);
    }

    public function purchases(Request $request): View
    {
        $filters = $request->only(['from', 'to', 'supplier_id']);
        $purchases = $this->reports->purchaseReport($filters)->paginate(25)->withQueryString();
        $totals = $this->reports->purchaseReport($filters)
            ->selectRaw('SUM(total_amount) as t, SUM(paid_amount) as p, SUM(due_amount) as d')->first();

        return view('reports.purchases', ['purchases' => $purchases, 'totals' => $totals, 'filters' => $filters,
            'suppliers' => Supplier::orderBy('name')->get()]);
    }
}
