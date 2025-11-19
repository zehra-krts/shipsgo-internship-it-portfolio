<?php
declare(strict_types=1);


require __DIR__ . '/../autoload.php';

use App\Models\Person;


$DATA_FILE = __DIR__ . '/../storage/customers.json';


function readCustomers(string $file): array {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    if ($raw === false || trim($raw) === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}


function writeCustomers(string $file, array $rows): void {
    file_put_contents($file, json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$errors = [];
$okMsg  = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = (int)($_POST['id'] ?? 0);                // ID’yi araçta olduğu gibi elle alıyoruz (öğrenme amaçlı)
    $name  = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));


    $rows = readCustomers($DATA_FILE);


    if ($id <= 0) {
        $errors[] = 'ID pozitif bir sayı olmalı.';
    }
    if ($name === '') {
        $errors[] = 'İsim boş olamaz.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email formatı geçersiz.';
    }

    // 4) Benzersizlik kontrolleri
    foreach ($rows as $c) {
        if (isset($c['id']) && (int)$c['id'] === $id) {
            $errors[] = 'Bu ID zaten kayıtlı: ' . htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8');
            break;
        }
    }
    if ($email !== '') {
        foreach ($rows as $c) {
            if (!empty($c['email']) && strcasecmp($c['email'], $email) === 0) {
                $errors[] = 'Bu email zaten kayıtlı: ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                break;
            }
        }
    }


    if (!$errors) {
        try {
            $person = new Person($name, $email !== '' ? $email : null, $phone !== '' ? $phone : null);

   
            $rows[] = [
                'id'    => $id,
                'name'  => $person->getName(),
                'email' => $person->getEmail(),
                'phone' => $person->getPhone(),
            ];

            writeCustomers($DATA_FILE, $rows);
            $okMsg = 'Müşteri eklendi: ' . $person->getName();
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}


$total = count(readCustomers($DATA_FILE));
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Add customer</title>
</head>
<body>
  <h1>Add customer</h1>

  <p>Sum of recorded customer: <strong><?= $total ?></strong></p>

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

  <form method="post" style="display:grid; gap:8px; max-width:320px;">
    <input type="number" name="id" placeholder="ID" required>
    <input type="text"   name="name" placeholder="Name" required>
    <input type="email"  name="email" placeholder="Email (optional)">
    <input type="text"   name="phone" placeholder="Phone (optional)">
    <button type="submit">Add</button>
  </form>

  <p style="margin-top:16px;">
    <a href="add_car.php">Add car</a> |
    <a href="add_customer.php">Add customer</a> |
    <a href="make_sale.php">Make sale</a> |
    <a href="list_sales.php">List of sales</a>
  </p>
</body>
</html>