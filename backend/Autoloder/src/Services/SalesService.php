<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\{Dealer, Car, Person, Employee, Sale, Payment};
use App\Models\Enums\CarStatus;

class SalesService
{

    private array $sales = [];     
    private int $saleSeq = 1;     

    public function createSale(
        Dealer $dealer,
        int $carId,
        Person $customer,
        Employee $salesperson,
        float $price
    ): Sale {
     
        $car = $dealer->findCarById($carId);
        if (!$car) {
            throw new \RuntimeException('Car not found.');
        }


        if ($car->getStatus() !== 'available') {
            throw new \RuntimeException('Car is not available.');
        }


        if ($price <= 0 || $price > $car->getPrice()) {
            throw new \RuntimeException('Price is unacceptable.');
        }

      
        $sale = new Sale(
            $this->saleSeq++,
            $car,
            $customer,
            $salesperson,
            $price
        );

  
        $car->markSold();
        $salesperson->incrementSoldedCars();


        $this->sales[] = $sale;

        return $sale;
    }

    public function takePayment(Sale $sale, Payment $payment): void
    {
        $sale->attachPayment($payment);
        $payment->markPaid();
    }


    public function listSales(): array
    {
        return $this->sales;
    }
}