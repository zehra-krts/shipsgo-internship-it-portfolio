<?php
declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use App\Models\{Car, Person, Employee, Dealer, Payment};
use App\Services\SalesService;

$CARS  = __DIR__ . '/../storage/cars.json';
$CUSTS = __DIR__ . '/../storage/customers.json';
$SALES = __DIR__ . '/../storage/sales.json';


function jread(string $f): array {
  if (!is_file($f)) return [];
  $raw = file_get_contents($f);
  if ($raw === false || $raw === '') return [];
  $d = json_decode($raw, true);
  return is_array($d) ? $d : [];
}
function jwrite(string $f, array $a): void {
  file_put_contents($f, json_encode($a, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

$errors = [];
$ok = null;

// Data for the form
$cars = jread($CARS);             // each: id, make, model, year, vin, price, status
$customers = jread($CUSTS);       // each: id, name, email, phone
$availableCars = array_values(array_filter($cars, fn($c) => ($c['status'] ?? 'available') === 'available'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 1) Read form
  $carId      = (int)($_POST['car_id'] ?? 0);
  $customerId = (int)($_POST['customer_id'] ?? 0);
  $price      = (float)($_POST['price'] ?? 0);
  $payMethod  = trim((string)($_POST['payment_method'] ?? 'cash'));

  // minimal salesperson (only name for demo)
  $spName = trim((string)($_POST['sp_name'] ?? 'Sales Rep'));

  // 2) Minimal checks
  if ($carId <= 0)      $errors[] = 'Please select a car.';
  if ($customerId <= 0) $errors[] = 'Please select a customer.';
  if ($price <= 0)      $errors[] = 'Price must be greater than 0.';

  // 3) Find chosen rows
  $carRow = null;
  foreach ($cars as $r) if ((int)$r['id'] === $carId) { $carRow = $r; break; }
  if (!$carRow) $errors[] = 'Selected car not found.';

  $custRow = null;
  foreach ($customers as $r) if ((int)$r['id'] === $customerId) { $custRow = $r; break; }
  if (!$custRow) $errors[] = 'Selected customer not found.';

  // 4) Build Dealer from JSON (only what we need)
  if (!$errors) {
    try {
      $dealer = new Dealer(1, 'Main Dealer');
      foreach ($cars as $c) {
        $dealer->addCar(new Car(
          (int)$c['id'], (string)$c['make'], (string)$c['model'],
          (int)$c['year'], (string)$c['vin'], (float)$c['price']
        ));
        // mirror sold state
        if (($c['status'] ?? 'available') === 'sold') {
          $dealer->findCarById((int)$c['id'])?->markSold();
        }
      }

      // 5) Domain objects
      $customer    = new Person((string)$custRow['name'], $custRow['email'] ?? null, $custRow['phone'] ?? null);
      $salesperson = new Employee($spName); // keep it minimal

      // 6) Service: sale + payment
      $svc  = new SalesService();
      $sale = $svc->createSale($dealer, $carId, $customer, $salesperson, $price);

      $payment = new Payment($price, $payMethod ?: 'cash');
      $svc->takePayment($sale, $payment);

      // 7) Persist minimal updates
      // cars.json -> mark as sold
      foreach ($cars as &$row) {
        if ((int)$row['id'] === $carId) { $row['status'] = 'sold'; break; }
      }
      unset($row);
      jwrite($CARS, $cars);

      // sales.json -> append simple record
      $sales = jread($SALES);
      $sales[] = [
        'id'        => $sale->getId(),
        'car'       => $sale->getCar()->getMake() . ' ' . $sale->getCar()->getModel(),
        'price'     => $sale->getSalePrice(),
        'customer'  => $sale->getCustomer()->getName(),
        'salesRep'  => $sale->getSalesperson()->getName(),
        'paid'      => $payment->getStatus(), // 'paid'
        'createdAt' => $sale->getDate()->format('Y-m-d H:i:s'),
      ];
      jwrite($SALES, $sales);

      $ok = 'Sale completed: ' . $sale->getCar()->getMake() . ' ' . $sale->getCar()->getModel() .
            ' - ₺' . $sale->getSalePrice() . ' | Payment: ' . $payment->getStatus();
    } catch (\Throwable $e) {
      $errors[] = $e->getMessage();
    }
  }
}

// Simple counters for header
$cntAvail = count($availableCars);
$cntCust  = count($customers);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Make Sale</title>
</head>
<body>
  <h1>Make Sale</h1>

  <p>Available cars: <strong><?= $cntAvail ?></strong> |
     Registered customers: <strong><?= $cntCust ?></strong></p>

  <?php if ($ok): ?>
    <p style="color:green;"><?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post" style="display:grid; gap:8px; max-width:420px;">
    <!-- Car -->
    <label>
      Car
      <select name="car_id" required>
        <option value="">-- select car --</option>
        <?php foreach ($availableCars as $c): ?>
          <option value="<?= (int)$c['id'] ?>">
            <?= htmlspecialchars($c['make'].' '.$c['model'].' ('.$c['year'].') - ₺'.$c['price'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <!-- Customer -->
    <label>
      Customer
      <select name="customer_id" required>
        <option value="">-- select customer --</option>
        <?php foreach ($customers as $cu): ?>
          <option value="<?= (int)$cu['id'] ?>">
            <?= htmlspecialchars($cu['name'].' '.($cu['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <!-- Price and payment -->
    <input type="number" step="0.01" name="price" placeholder="Sale price" required>
    <input type="text" name="payment_method" placeholder="Payment method (cash/card/transfer)" value="cash">

    <!-- Minimal salesperson -->
    <input type="text" name="sp_name" placeholder="Salesperson name" value="Sales Rep" required>

    <button type="submit">Create sale</button>
  </form>

  <p style="margin-top:16px;">
    <a href="add_car.php">Add car</a> |
    <a href="add_customer.php">Add customer</a> |
    <a href="make_sale.php">Make sale</a> |
    <a href="list_sales.php">List sales</a>
  </p>
</body>
</html>