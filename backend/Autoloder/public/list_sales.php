<?php
declare(strict_types=1);



$SALES = __DIR__ . '/../storage/sales.json';


function jread(string $f): array {
  if (!is_file($f)) return [];
  $raw = file_get_contents($f);
  if ($raw === false || $raw === '') return [];
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}

$sales = jread($SALES);
$total = count($sales);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>List Sales</title>
  <style>
    table { border-collapse: collapse; width: 100%; max-width: 800px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h1>Sales List</h1>

  <p>Total sales: <strong><?= $total ?></strong></p>

  <?php if ($total === 0): ?>
    <p style="color:gray;">No sales recorded yet.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Car</th>
        <th>Customer</th>
        <th>Salesperson</th>
        <th>Price</th>
        <th>Payment</th>
        <th>Date</th>
      </tr>
      <?php foreach ($sales as $s): ?>
        <tr>
          <td><?= htmlspecialchars((string)($s['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['car'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['customer'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['salesRep'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td>₺<?= htmlspecialchars((string)($s['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['paid'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($s['createdAt'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <p style="margin-top:16px;">
    <a href="add_car.php">Add car</a> |
    <a href="add_customer.php">Add customer</a> |
    <a href="make_sale.php">Make sale</a> |
    <a href="list_sales.php">List sales</a>
  </p>
</body>
</html>