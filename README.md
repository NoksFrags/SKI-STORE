# Ski Store

A complete, production-shaped alpine equipment webshop built with **plain PHP 8** and **MySQL/MariaDB**.
No Composer, no framework, no build step: drop it into XAMPP, import one SQL file and it runs.

---

## 1. What is included

**Storefront**
- Home page with hero, categories, featured gear, workshop promo and new arrivals
- Catalogue with category filters, keyword search, sorting and pagination
- Product detail pages with stock state, sale pricing, wishlist and quantity picker
- Guest shopping cart (session based), merged automatically into the account cart on sign in
- Free-delivery progress bar, delivery estimate and 30-day return notes
- Responsive layout, accessibility-minded markup and generated SVG artwork (works offline)

**Accounts**
- Registration, sign in, sign out with CSRF protection and login throttling
- Customer dashboard: order count, lifetime spend, wishlist and cart summary
- Order history and single-order view, with online cancellation while pending
- Wishlist page with add-to-cart and remove
- Profile editing (name, email, phone, address) and password change
- Order confirmation page with full receipt

**Checkout**
- Delivery form pre-filled from the profile, five supported countries
- Three payment methods: card on delivery, cash on delivery, bank transfer
- Transactional order placement with `SELECT ... FOR UPDATE` stock locking
- Stock is decremented on purchase and restored when an order is cancelled

**Administration** (`role = admin` users only)
- Dashboard: revenue, orders, customers, average order, 6-month revenue chart,
  order pipeline, latest orders, low stock, best sellers
- Products: create, edit, delete, image upload, sale pricing, feature/visibility toggles
- Categories: create, edit, delete (blocked while products are attached), icon + order
- Orders: search, status filter, detail view, status workflow that restocks cancellations
- Customers & staff: search, promote/demote administrators, activate/deactivate accounts
- Guards: you cannot demote or deactivate yourself, and at least one admin must remain

**Security**
- PDO prepared statements everywhere
- CSRF token on every POST, validated globally by the router
- `password_hash` / `password_verify` (bcrypt)
- Session regeneration on login/logout
- Output escaping helper `e()` used across all views
- Upload validation (MIME type, size, random filename) and `.htaccess` denying script execution in uploads
- Role middleware for every `/admin` route; ownership checks for customer orders

---

## 2. Requirements

- PHP 8.0+ (tested on 8.2.12) with `pdo_mysql`, `mbstring`, `fileinfo`
- MySQL 5.7+ or MariaDB 10.3+ (XAMPP's MariaDB 10.4 is fine)
- Apache with `mod_rewrite` for the pretty URLs, **or** PHP's built-in server for local use

---

## 3. Installation

### Step 1 — Put the files in place
Copy the project to `C:\xampp\htdocs\SKI-STORE` (the existing folder already contains it).

### Step 2 — Create the database
The complete schema, indexes, foreign keys and demo data live in **`database/schema.sql`**.

**Option A — phpMyAdmin**
1. Start **Apache** and **MySQL** in the XAMPP control panel.
2. Open <http://localhost/phpmyadmin>.
3. Go to the **Import** tab, choose `database/schema.sql`, press **Import**.

**Option B — terminal**
```
C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\SKI-STORE\database\schema.sql
```

Either way the script creates the `skistore` database, all eight tables and the demo content
(6 categories, 18 products, 2 users, 3 orders, wishlist entries).

The catalogue uses real 2025/26 models (Atomic, Rossignol, Salomon, Burton, Jones,
Capita, Tecnica, Arc'teryx, Patagonia, The North Face, Smith, Oakley, Hestra, Dakine,
Leki, Swix). Prices are approximate BAM retail conversions of US MSRP, not live feeds.

> The script drops and recreates the shop tables, so importing it again resets the demo data.

### Step 3 — Configure the environment
Copy `.env.example` to `.env` and adjust if needed:

```
APP_NAME="Ski Store"
APP_ENV=local
APP_DEBUG=true
APP_URL=
TIMEZONE=Europe/Sarajevo

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=skistore
DB_USER=root
DB_PASS=

CURRENCY=KM
CURRENCY_DECIMALS=2
SHIPPING_FLAT=9.90
FREE_SHIPPING_FROM=200.00
```

`APP_URL` is intentionally empty: the app detects whether it runs at the domain root or in a
subfolder such as `/SKI-STORE`. Set it only if you want to force an absolute base URL.

### Step 4 — Open the shop
- With Apache: <http://localhost/SKI-STORE/>
- Without Apache, from the project folder:
  ```
  C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public public\router.php
  ```
  then open <http://127.0.0.1:8080>

---

## 4. Demo accounts

| Role     | Email                    | Password      |
|----------|--------------------------|---------------|
| Admin    | `admin@skistore.test`    | `Admin123!`   |
| Customer | `customer@skistore.test` | `Customer123!`|

The admin account sees the **Admin panel** link in the account menu and can reach `/admin`.
Customers who open `/admin` receive a 403 page.

To make another user an administrator, sign in as an admin and use
**Admin → Customers & staff → Role**.

---

## 5. Project structure

```
SKI-STORE/
├── app/
│   ├── Controllers/            Storefront, account and Admin/ controllers
│   ├── Core/                   Router, View, Database, Auth, Csrf, Flash, Validator
│   ├── Helpers/functions.php   env(), config(), url(), money(), icon(), ...
│   ├── Middleware/             AuthMiddleware, AdminMiddleware, GuestMiddleware, CsrfMiddleware
│   ├── Models/                 User, Category, Product, Cart, Wishlist, Order
│   ├── Services/               CheckoutService, UploadService
│   ├── Views/                  layouts/, partials/, home/, shop/, cart/, checkout/, account/, admin/
│   └── bootstrap.php           env loading, constants, autoloader, session
├── config/                     app.php, database.php
├── database/schema.sql         complete importable schema + demo data
├── public/
│   ├── assets/css/             app.css, store.css, admin.css, pages.css
│   ├── assets/js/app.js        navigation, dropdowns, steppers, confirmations
│   ├── assets/img/             hero + generated product illustrations
│   ├── uploads/products/       admin-uploaded product images
│   ├── index.php               front controller
│   └── router.php              router for PHP's built-in server
├── routes/web.php              every route and its middleware
├── storage/logs/               writable log directory
├── .env / .env.example         configuration
├── .htaccess                   root rewrite to public/index.php
└── README.md
```

### Request lifecycle
1. Apache rewrites every request to `public/index.php` (or `router.php` serves static files).
2. `app/bootstrap.php` loads `.env`, defines paths, registers the autoloader and starts the session.
3. `routes/web.php` registers routes on `App\Core\Router`.
4. Middleware runs first (CSRF for POST, auth/guest/admin guards), then the controller.
5. Controllers query models, then render a view inside a layout.

---

## 6. Store settings

| Setting | Where | Default |
|---|---|---|
| Currency label | `CURRENCY` in `.env` | `KM` |
| Decimal places | `CURRENCY_DECIMALS` | `2` |
| Flat delivery fee | `SHIPPING_FLAT` | `9.90` |
| Free delivery threshold | `FREE_SHIPPING_FROM` | `200.00` |
| Low-stock threshold | `config/app.php` → `low_stock_threshold` | `5` |
| Catalogue page size | `config/app.php` → `per_page` | `9` |
| Upload limits | `config/app.php` → `uploads` | JPG/PNG/WebP, 3 MB |

---

## 7. Main routes

| Method | URI | Purpose |
|---|---|---|
| GET | `/` | Home |
| GET | `/shop` | Catalogue (`q`, `category`, `sort`, `page`) |
| GET | `/product/{slug}` | Product detail |
| GET/POST | `/cart`, `/cart/add`, `/cart/update`, `/cart/remove`, `/cart/clear` | Cart |
| GET/POST | `/login`, `/register`, `/logout` | Authentication |
| GET/POST | `/checkout` | Checkout |
| GET | `/checkout/success/{id}` | Confirmation |
| GET/POST | `/wishlist` | Wishlist |
| GET | `/account`, `/account/orders`, `/account/orders/{id}` | Customer area |
| GET/POST | `/account/profile`, `/account/password` | Profile |
| GET | `/admin` | Dashboard |
| — | `/admin/products`, `/admin/categories`, `/admin/orders`, `/admin/users` | Management |

---

## 8. Notes

- Product illustrations are generated SVGs committed under `public/assets/img/products/`
  (hue-graded to the pine/brass palette, so they are illustrations rather than photographs);
  an admin upload replaces them with real photography.
- Uploaded files are written to `public/uploads/products/` with random names; the folder ships
  with an `.htaccess` that disables PHP execution.
- Images are the only writable asset path; the rest of the app is read-only at runtime.
- Demo orders intentionally reference the demo customer so the dashboard has data on first run.
