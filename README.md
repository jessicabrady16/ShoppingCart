# ShoppingCart — Laravel + Vue (Interview Prep)

Session-backed shopping cart with a clean OO domain, JSON API, and a Vue 3 UI.  
Built to practice senior-level full‑stack patterns (Laravel 12, PHP 8.3, Vite, Vue 3) and to discuss design/trade‑offs in interviews.

## Highlights
- **Domain**: `App/Domain/Cart/{Cart, CartItem, Product, CartInterface}` (clear interfaces, single source of truth for money math).
- **API**: JSON endpoints with **session persistence** (`/api/cart`, `/api/cart/items`).
- **Frontend**: Minimal Vue 3 app (SFC) compiled by Vite.
- **Dockerized**: Nginx, PHP‑FPM, MySQL 8, Node (Vite). One‑command up.
- **Tests (Pest)**: Unit tests for the domain, feature tests for API.
- **Interview‑friendly**: Simple surface area, strong separation of concerns.

## Stack
Laravel 12 • PHP 8.3 • MySQL 8 • Vue 3 • Vite • Pest • Docker (nginx + php‑fpm + node)

---

## Quick Start (Docker)
```bash
# 1) Build & start services
docker compose up -d --build

# 2) App bootstrap (inside containers)
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force   # db not required for cart, but app boots cleanly

# 3) Frontend
# Dev (HMR):
docker compose exec node bash -lc 'npm i && npm run dev -- --host 0.0.0.0 --port 5173'
# OR Production build:
docker compose exec node bash -lc 'npm i && npm run build'
docker compose exec app bash -lc 'rm -f public/hot && php artisan optimize:clear'
```

Open the app: **http://localhost:8080**  
API example: **GET http://localhost:8080/api/cart**

---

## API (Session‑backed)
```
GET     /api/cart                          -> show (query: tax_rate?, discount?)
POST    /api/cart/items                    -> {product_id, name, price, quantity}
PATCH   /api/cart/items/{productId}        -> {quantity}  # 0 removes
DELETE  /api/cart/items/{productId}        -> remove item
DELETE  /api/cart                          -> clear cart
```
Notes:
- Discount is a **flat** amount applied **before** tax.
- All money arithmetic is centralized in `Cart::totalsBreakdown()`.

### Sample
```bash
curl -s http://localhost:8080/api/cart
curl -s -X POST http://localhost:8080/api/cart/items \
  -H 'Content-Type: application/json' \
  -d '{"product_id":42,"name":"Notebook","price":3.5,"quantity":2}'
```

---

## Project Layout
```
app/Domain/Cart/
  Cart.php  CartItem.php  Product.php  CartInterface.php
app/Services/CartService.php            # session load/save + façade for controller
app/Http/Controllers/CartController.php
app/Http/Requests/{Store,Update}CartItemRequest.php
routes/{api.php, web.php}
bootstrap/app.php                       # enables API routing (api + apiPrefix)
resources/js/app.js                     # mounts Vue
resources/js/components/CartApp.vue     # UI
resources/views/cart.blade.php
docker-compose.yml, .docker/
```

---

## Dev Notes
- **API routing** enabled in `bootstrap/app.php` with:
  ```php
  ->withRouting(
      web: __DIR__.'/../routes/web.php',
      api: __DIR__.'/../routes/api.php',
      apiPrefix: 'api',
      commands: __DIR__.'/../routes/console.php',
      health: '/up',
  )
  ```
- **Sessions for API** via middleware: `EncryptCookies`, `AddQueuedCookiesToResponse`, `StartSession` (see `routes/api.php`).
- **Vite** uses `@vitejs/plugin-vue`. For production: `npm run build` and ensure `public/hot` is absent.

---

## Tests (Pest)
Run all tests:
```bash
docker compose exec app php artisan test
# or with more detail:
docker compose exec app ./vendor/bin/pest -vv
```

### Coverage
- **Unit**: domain math, validation guards, snapshot/restore (`toStorageArray()`).
- **Feature**: add/update/remove flows, discount‑before‑tax behavior, request validation.

---

## Discussion Topics (for interview)
- Why session‑backed cart vs DB/Redis; how to switch to persistent carts.
- Object design trade‑offs (value objects for money; product immutability; CartItem as aggregate part).
- Rounding strategy & currency (integers for cents vs floats; current demo uses floats for brevity).
- API shape & idempotency; optimistic updates on UI.
- Test strategy (unit vs feature vs e2e), and CI (GitHub Actions) ideas.

## Future Extensions
- Percentage discounts & coupon codes.
- Persist carts for authenticated users (DB/Redis), merge guest → user.
- Product catalog in DB with Eloquent resources.
- Rate limiting, observability (request/SQL timings), and SLOs.
- Cypress E2E and GitHub Actions CI pipeline.

---

## License
Proprietary License — Evaluation Only
Copyright (c) 2025 Jessica Brady. All rights reserved.

Permission is granted to recruiters, hiring managers, and interviewers to:
  • View this repository and its contents.
  • Clone the repository and run the software locally for the sole purpose of evaluating the author’s skills.

All other rights are reserved. Without prior written permission from the copyright holder, you may NOT:
  • Copy, reproduce, or redistribute this software or any portion of it.
  • Modify, adapt, translate, or create derivative works.
  • Use any part of this software in commercial or non-commercial products or services.
  • Re-license, sub-license, or incorporate this software into other projects.
  • Publicly host or provide access to compiled or uncompiled versions of this software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED.
