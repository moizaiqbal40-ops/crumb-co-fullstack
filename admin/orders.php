<?php
require_once __DIR__ . "/../config/db.php";

// Handle status update (simple inline action)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["order_id"], $_POST["status"])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST["status"], $_POST["order_id"]]);
    header("Location: orders.php");
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
foreach ($orders as &$order) {
    $itemsStmt->execute([$order["id"]]);
    $order["items"] = $itemsStmt->fetchAll();
}
unset($order);

$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total),0) AS t FROM orders WHERE status != 'cancelled'")->fetch()["t"];
$totalOrders = count($orders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Crumb & Co — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, "Segoe UI", Roboto, sans-serif;
    background: #fff6ef;
    color: #3f2e29;
    padding: 32px;
  }
  h1 { font-size: 1.6rem; margin-bottom: 4px; }
  .subtitle { color: #6b5850; font-size: 0.9rem; margin-bottom: 28px; }
  .stats { display: flex; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
  .stat-card {
    background: #fff; border-radius: 14px; padding: 18px 24px;
    box-shadow: 0 4px 14px rgba(63,46,41,0.08); min-width: 160px;
  }
  .stat-card strong { display: block; font-size: 1.6rem; color: #9c3f4c; }
  .stat-card span { font-size: 0.8rem; color: #6b5850; text-transform: uppercase; letter-spacing: 0.04em; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(63,46,41,0.08); }
  th, td { padding: 12px 16px; text-align: left; font-size: 0.88rem; border-bottom: 1px solid #f0e4da; vertical-align: top; }
  th { background: #fbe9dd; color: #5e6d54; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em; }
  tr:last-child td { border-bottom: none; }
  .item-line { font-size: 0.82rem; color: #6b5850; margin-bottom: 3px; }
  .status-badge {
    display: inline-block; padding: 3px 10px; border-radius: 100px; font-size: 0.74rem; font-weight: 600;
  }
  .status-pending { background: #f6e3d3; color: #8a5a2b; }
  .status-confirmed { background: #dcecd8; color: #3f7a3f; }
  .status-delivered { background: #d8e3ec; color: #2f5a7a; }
  select, button {
    font-family: inherit; font-size: 0.8rem; padding: 5px 8px; border-radius: 8px;
    border: 1px solid #e0d3c8; background: #fff;
  }
  button { background: #9c3f4c; color: #fff; border: none; cursor: pointer; margin-left: 6px; }
  .empty { padding: 40px; text-align: center; color: #6b5850; }
  a.back { display: inline-block; margin-bottom: 16px; color: #9c3f4c; text-decoration: none; font-size: 0.85rem; }
</style>
</head>
<body>
  <a class="back" href="../index.php">← Back to site</a>
  <h1>Orders Dashboard</h1>
  <p class="subtitle">Crumb & Co — internal admin view (not linked from the public site)</p>

  <div class="stats">
    <div class="stat-card">
      <strong><?= $totalOrders ?></strong>
      <span>Total Orders</span>
    </div>
    <div class="stat-card">
      <strong>Rs <?= number_format($totalRevenue) ?></strong>
      <span>Total Revenue</span>
    </div>
  </div>

  <?php if (empty($orders)): ?>
    <div class="empty">No orders yet. Once someone checks out on the site, it'll show up here.</div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>Items</th>
        <th>Payment</th>
        <th>Total</th>
        <th>Status</th>
        <th>Placed</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
      <tr>
        <td>#<?= $order["id"] ?></td>
        <td>
          <strong><?= htmlspecialchars($order["customer_name"] ?? "—") ?></strong><br>
          <span style="color:#6b5850;"><?= htmlspecialchars($order["customer_phone"] ?? "") ?></span><br>
          <span style="color:#6b5850; font-size:0.78rem;"><?= htmlspecialchars($order["delivery_address"] ?? "") ?></span>
        </td>
        <td>
          <?php foreach ($order["items"] as $item): ?>
            <div class="item-line">
              <?= $item["quantity"] ?>× <?= htmlspecialchars($item["product_name"]) ?>
              <?php if ($item["is_custom"]): ?>
                (<?= htmlspecialchars($item["custom_base"]) ?>, <?= htmlspecialchars($item["custom_decor"]) ?><?php if ($item["custom_message"]): ?>, "<?= htmlspecialchars($item["custom_message"]) ?>"<?php endif; ?>)
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </td>
        <td><?= strtoupper($order["payment_method"] ?? "COD") ?></td>
        <td>Rs <?= number_format($order["total"]) ?></td>
        <td>
          <span class="status-badge status-<?= $order["status"] ?>"><?= ucfirst($order["status"]) ?></span>
          <form method="post" style="margin-top:8px;">
            <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
            <select name="status">
              <option value="pending" <?= $order["status"] === "pending" ? "selected" : "" ?>>Pending</option>
              <option value="confirmed" <?= $order["status"] === "confirmed" ? "selected" : "" ?>>Confirmed</option>
              <option value="delivered" <?= $order["status"] === "delivered" ? "selected" : "" ?>>Delivered</option>
            </select>
            <button type="submit">Update</button>
          </form>
        </td>
        <td><?= date("d M, g:i A", strtotime($order["created_at"])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</body>
</html>
