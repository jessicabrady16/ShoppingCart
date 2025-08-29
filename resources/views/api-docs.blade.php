<!-- resources/views/api-docs.blade.php -->
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>ShoppingCart API Docs</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    body {
      font-family: ui-sans-serif, system-ui;
      line-height: 1.5;
      margin: 2rem;
      max-width: 900px
    }

    code,
    pre {
      font-family: ui-monospace, Menlo, Consolas, monospace;
      font-size: 0.95rem
    }

    pre {
      background: #0b1020;
      color: #e6e6e6;
      padding: 1rem;
      border-radius: 10px;
      overflow: auto
    }

    h1,
    h2 {
      margin: .2rem 0
    }

    .pill {
      display: inline-block;
      background: #eef;
      padding: .15rem .5rem;
      border-radius: 999px;
      font-size: .85rem
    }

    table {
      border-collapse: collapse;
      width: 100%
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: .5rem
    }

    th {
      text-align: left;
      background: #fafafa
    }
  </style>
</head>

<body>
  <h1>ShoppingCart API</h1>
  <p><span class="pill">base URL</span> <code>http://localhost:8080</code></p>

  <h2>Auth & Session</h2>
  <p>This API uses a <strong>session cookie</strong>. Keep cookies between requests (see curl’s <code>-c</code>/<code>-b</code> below).</p>

  <h2>Endpoints</h2>
  <table>
    <thead>
      <tr>
        <th>Method</th>
        <th>Path</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>GET</td>
        <td><code>/api/cart?tax_rate=&lt;float&gt;&amp;discount=&lt;float&gt;</code></td>
        <td>Get cart. Discount applied <em>before</em> tax.</td>
      </tr>
      <tr>
        <td>POST</td>
        <td><code>/api/cart/items</code></td>
        <td>Add item: <code>{ product_id, name, price, quantity }</code></td>
      </tr>
      <tr>
        <td>PATCH</td>
        <td><code>/api/cart/items/{productId}</code></td>
        <td>Update quantity: <code>{ quantity }</code> (0 removes)</td>
      </tr>
      <tr>
        <td>DELETE</td>
        <td><code>/api/cart/items/{productId}</code></td>
        <td>Remove an item</td>
      </tr>
      <tr>
        <td>DELETE</td>
        <td><code>/api/cart</code></td>
        <td>Clear cart</td>
      </tr>
    </tbody>
  </table>

  <h2>Response shape</h2>
  <pre>{
  "items": [
    {
      "product": { "id": 42, "name": "Notebook", "price": 3.5 },
      "quantity": 2,
      "lineTotal": 7.0
    }
  ],
  "subtotal": 7.0,
  "discount": 0.3,
  "tax_rate": 0.0725,
  "tax": 0.49,
  "total": 7.19,
  "checkout": "https://example.com/checkout"
}</pre>

  <h2>Quick start with curl (cookie jar)</h2>
  <pre># create a cookie jar file for the session
touch cookies.txt

# 1) read empty cart
curl -s -c cookies.txt -b cookies.txt \
  "http://localhost:8080/api/cart"

# 2) add an item
curl -s -X POST -c cookies.txt -b cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"product_id":42,"name":"Notebook","price":3.5,"quantity":2}' \
  "http://localhost:8080/api/cart/items"

# 3) update quantity
curl -s -X PATCH -c cookies.txt -b cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"quantity":3}' \
  "http://localhost:8080/api/cart/items/42"

# 4) read with discount + tax
curl -s -c cookies.txt -b cookies.txt \
  "http://localhost:8080/api/cart?discount=0.30&tax_rate=0.0725"

# 5) remove an item
curl -s -X DELETE -c cookies.txt -b cookies.txt \
  "http://localhost:8080/api/cart/items/42"

# 6) clear cart
curl -s -X DELETE -c cookies.txt -b cookies.txt \
  "http://localhost:8080/api/cart"</pre>

  <h2>Validation</h2>
  <ul>
    <li><code>price</code> ≥ 0.01, <code>quantity</code> ≥ 1 (POST) / ≥ 0 (PATCH)</li>
    <li><code>tax_rate</code> ≥ 0, <code>discount</code> ≥ 0</li>
  </ul>

  <p>Discount is a flat amount applied before tax. All math lives in <code>App\Domain\Cart\Cart::totalsBreakdown()</code>.</p>

  <p style="margin-top:2rem">
    <a href="/">↩ Back to UI</a>
  </p>
</body>

</html>