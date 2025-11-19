<?php
declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use App\Models\Car;

$DATA_FILE = __DIR__ . '/../storage/cars.json';

function readCars(string $file): array {
    if(!file_exists($file)) return [];
    $raw = file_get_contents($file);
    if ($raw === false || trim($raw) === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function writeCars(string $file, array $cars): void {
    file_put_contents($file, json_encode($cars, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$errors = [];
$okMsg  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $id    = (int)($_POST['id']    ?? 0);
    $make  = trim((string)($_POST['make']  ?? ''));
    $model = trim((string)($_POST['model'] ?? ''));
    $year  = (int)($_POST['year']  ?? 0);
    $vin   = trim((string)($_POST['vin']   ?? ''));
    $price = (float)($_POST['price'] ?? 0);

    $carsArray = readCars($DATA_FILE);

    foreach ($carsArray as $c) {
        if (isset($c['vin']) && $c['vin'] === $vin) {
            $errors[] = 'This VIN is already recorded. ' . htmlspecialchars($vin, ENT_QUOTES, 'UTF-8');
            break;
        }
    }
    if (!$errors) {
        try {
            $car = new Car($id, $make, $model, $year, $vin, $price);

          
            $carsArray[] = [
                'id'    => $car->getId(),
                'make'  => $car->getMake(),
                'model' => $car->getModel(),
                'year'  => $car->getYear(),
                'vin'   => $car->getVin(),
                'price' => $car->getPrice(),
                'status'=> $car->getStatus(), 
            ];

            writeCars($DATA_FILE, $carsArray);
            $okMsg = 'Araç eklendi: ' . $car->getMake() . ' ' . $car->getModel() . ' (' . $car->getYear() . ')';
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}
$total = count(readCars($DATA_FILE));
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Add car</title>
</head>
<body>
  <h1>Add car</h1>

  <p>Sum of recorded car: <strong><?= $total ?></strong></p>

  <?php if ($okMsg): ?>
    <p style="color:green;"><?= htmlspecialchars($okMsg, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <!-- Basit form: UI önemsiz dedik, sadece alanlar -->
  <form method="post" style="display:grid; gap:8px; max-width:320px;">
    <input type="number" name="id" placeholder="ID" required>
    <input type="text"   name="make" placeholder="Make" required>
    <input type="text"   name="model" placeholder="Model" required>
    <input type="number" name="year" placeholder="Year" required>
    <input type="text"   name="vin" placeholder="VIN" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <button type="submit">Add</button>
  </form>


  <p style="margin-top:16px;">
    <a href="add_car.php">Add car</a> |
    <a href="list_sales.php">List of sales</a>
  </p>
</body>
</html>