<?php
declare(strict_types=1);

namespace App\Models;

class Sale
{
    private int $id;
    private Car $car;
    private Person $customer;
    private Employee $salesperson;
    private float $salePrice;
    private \DateTimeImmutable $date;
    private ?Payment $payment = null;

    public function __construct(
        int $id,
        Car $car,
        Person $customer,
        Employee $salesperson,
        float $salePrice
    ) {
        if ($salePrice <= 0) {
            throw new \InvalidArgumentException('Sale price must be > 0.');
        }
        $this->id          = $id;
        $this->car         = $car;
        $this->customer    = $customer;
        $this->salesperson = $salesperson;
        $this->salePrice   = $salePrice;
        $this->date        = new \DateTimeImmutable('now');
    }

    // GETTERS
    public function getId(): int { return $this->id; }
    public function getCar(): Car { return $this->car; }
    public function getCustomer(): Person { return $this->customer; }
    public function getSalesperson(): Employee { return $this->salesperson; }
    public function getSalePrice(): float { return $this->salePrice; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function getPayment(): ?Payment { return $this->payment; }

    // ACTIONS
    public function attachPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }
}