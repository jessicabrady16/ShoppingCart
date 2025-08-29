<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>ShoppingCart — Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: ui-sans-serif, system-ui;
      margin: 2rem
    }

    .grid {
      display: grid;
      gap: 1rem;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr))
    }

    .card {
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 1rem
    }

    a.btn {
      display: inline-block;
      padding: .6rem 1rem;
      border-radius: 8px;
      background: #111;
      color: #fff;
      text-decoration: none
    }
  </style>
</head>

<body>
  <h1>ShoppingCart</h1>
  <p>Choose where to go:</p>
  <div class="grid">
    <div class="card">
      <h2>🛍️ Shop</h2>
      <p>Browse products and add to cart.</p>
      <a class="btn" href="/shop">Open Shop</a>
    </div>
    <div class="card">
      <h2>🧺 Cart</h2>
      <p>View/update your cart.</p>
      <a class="btn" href="/cart">Open Cart</a>
    </div>
    <div class="card">
      <h2>📚 API Docs</h2>
      <p>Endpoints, params, examples.</p>
      <a class="btn" href="/api-docs">Open API Docs</a>
    </div>
  </div>
</body>

</html>