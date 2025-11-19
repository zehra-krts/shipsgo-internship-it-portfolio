<?php
declare(strict_types=1);

namespace App\Models;

class Car {
    // car info 
    private int $id;     
    private string $make;
    private string $model;
    private int $year;  // year of production
    private string $vin; //identification number
    private float $price;
    private string $status;


    public function __construct(
        int $id,
        string $make,
        string $model,
        int $year,
        string $vin,
        float $price
    ) {
        if ($price <= 0) throw new \InvalidArgumentException('Price must be > 0.');
        $this->id    = $id;
        $this->make  = $make;
        $this->model = $model;
        $this->year  = $year;
        $this->vin   = $vin;
        $this->price = $price;
        $this->status = 'available';
    }

    //getter
    public function getId(): int { return $this->id; }
    public function getMake(): string { return $this->make; }
    public function getModel(): string { return $this->model; }
    public function getYear(): int { return $this->year; }
    public function getVin(): string { return $this->vin; }
    public function getPrice(): float { return $this->price; }
    public function getStatus(): string { return $this->status; }
    //setter
    public function setPrice(float $price): self  
    {
        if ($price <= 0) {
            throw new \InvalidArgumentException('Price must be more than 0.');
        }
        $this->price = $price;
        return $this; // method chaining için self döner.
    }
    public function applyDiscount(float $percent): void
    {
        if ($percent <= 0 || $percent >= 100) {
            throw new \InvalidArgumentException('Percent must be between 0-100.');
        }
        $this->price = round($this->price * (1 - $percent / 100), 2);
    }
    public function markSold(): void      { $this->status = 'sold'; }
    public function markReserved(): void  { $this->status = 'reserved'; }
    public function markAvailable(): void { $this->status = 'available'; }
}