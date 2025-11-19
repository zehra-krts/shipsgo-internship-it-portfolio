<?php
declare(strict_types=1);

require __DIR__ . '/../autoload.php';

use App\Models\{Dealer, Car, Person, Employee, Payment};
use App\Services\SalesService;

// 1) Dealer ve SalesService oluştur
$dealer = new Dealer(1, 'Main Dealer');
$salesService = new SalesService();

// 2) Araba ekle
$dealer->addCar(new Car(1, 'Toyota', 'Corolla', 2020, 'VIN-001', 300000));

// 3) Müşteri ve çalışan oluştur
$customer = new Person('Ali Müşteri', 'ali@example.com', '555-000');
$salesperson = new Employee('Ayşe Satış', 'ayse@example.com', '555-111', 20000.0);

// 4) Satış yap
$sale = $salesService->createSale($dealer, 1, $customer, $salesperson, 280000);

echo "Satış yapıldı: "
   . $sale->getCar()->getMake() . " "
   . $sale->getCar()->getModel()
   . " - ₺" . $sale->getSalePrice() . PHP_EOL;

echo "Satış temsilcisi: "
   . $sale->getSalesperson()->getName()
   . ", toplam sattığı: "
   . $salesperson->getSoldedCars() . PHP_EOL;

// 5) Ödeme al
$payment = new Payment(280000, 'cash'); // enum yerine string
$salesService->takePayment($sale, $payment);

echo "Ödeme durumu: " . $payment->getStatus() . PHP_EOL;