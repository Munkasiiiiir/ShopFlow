# ShopFlow — Development Notes

Laravel-based Inventory & POS system for small retail businesses (SE-331 Capstone, Group G1, Section 42_H).
Design source of truth: `ShopFlow_Lab_Performance_S1-S4.docx` (S1–S4) in the Capstone folder.
FR references below are from Lab Assessment 1 (`Lab_Assessment1_G1_H2.docx`).

## Setup

Requirements: PHP 8.2+, Composer, Node.js. Proposal target is MySQL 8 (XAMPP/Laragon);
migrations use Laravel's schema builder, so they run unchanged on MySQL. For quick local
dev without MySQL, the default `.env` uses SQLite (`database/database.sqlite`).

```bash
composer install
cp .env.example .env        # set APP_NAME=ShopFlow; for MySQL set DB_CONNECTION=mysql etc.
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Demo accounts (all passwords: `password`):

| Role    | Email                 |
|---------|-----------------------|
| Admin   | admin@shopflow.test   |
| Manager | manager@shopflow.test |
| Cashier | cashier@shopflow.test |

Seed data: shop settings, 3 categories, 2 brands, 4 units, 1 supplier, walk-in + 1 customer,
4 products with opening stock entered through a real purchase (`PUR-0001`) via StockService.

## Architecture rules (agreed in S1/S2)

- Routes → Middleware (`auth`, `approved`, `role:...`) → Form Requests → Controllers (thin)
  → Services → Eloquent Models → Database. Views: Blade + Tailwind + Alpine.js.
- **Only `App\Services\StockService` may change `products.stock`** (FR-1.8). It locks the
  product row (`lockForUpdate`), rejects negative stock (FR-1.9), and logs every change in
  `stock_movements` (FR-1.10). Purchases/sales/returns/adjustments must call it inside a
  DB transaction (NFR-10).
- Price/cost snapshots are stored on `sale_items` / `purchase_items`; never edit history.

## Status

**Implemented & integrated (all 5 modules)** — `php artisan test`: 41 passing.

- **M1 (Munkasir):** auth + pending approval, staff page, settings page, StockService,
  stock movement history, stock adjustments, full/partial sales returns.
- **M2 (Rahat):** categories/brands/units/products CRUD, image upload (2MB), auto SKU,
  product search/filters + details, multi-row purchases via PurchaseService,
  purchase history, low-stock list.
- **M3 (Anas):** suppliers/customers CRUD + search + details/history, active/inactive,
  current stock list with status, supplier payments (due tracking), ledger + PDF.
- **M4 (Tanzil):** POS screen (AJAX search, Alpine cart), checkout via SaleService
  (transaction + stock lock + invoice number), sales list, invoice page + PDF.
- **M5 (Mahbub):** role-based dashboard (stat cards, Chart.js daily/monthly, top 5,
  recent sales), sales/top-products/stock-valuation/profit/purchase reports,
  PDF + CSV export.

Role rules: reports/purchases/catalog-write/returns/adjustments = Admin+Manager;
staff/settings = Admin; POS/sales = all roles (cashiers see only their own sales).

**Known simplifications:** local dev runs on SQLite (migrations are MySQL-ready);
report export is PDF + CSV (proposal allows "Excel/CSV"); Chart.js loads from CDN.
