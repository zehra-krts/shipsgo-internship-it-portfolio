<?php
declare(strict_types=1);
namespace App\Models;

class Dealer {
    // car list - array and add, delete , search
    public int $id;
    public string $name;

    private array $carList = [];

    public function __construct(int $id, string $name){
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int{ return $this->id; }
    public function getName(): string{ return $this->name; }

    public function getInventory(): array{
        return $this->carList;
    }
// add car into inventory , controll VIN (unique)
public function addCar(Car $car): void
{
    foreach ($this->carList as $existing) {
        if ($existing->getVin() === $car->getVin()) {
            throw new \RuntimeException('Same VIN in inventory.');
        }
    }
    $this->carList[] = $car;
}

// find car with ID otherways its null
public function findCarById(int $id): ?Car{ // it can be car object or null(?)
    foreach ($this->carList as $car){
        if($car->getId() == $id){
            return $car;
        }
    }
    return null;
}

// find car with Vin otherways its null

public function findCarByVin(string $vin): ?Car
    {
        foreach ($this->carList as $car) {
            if ($car->getVin() === $vin) {
                return $car;
            }
        }
        return null;
    }

    // delate car in inventory with ID 
    public function removeCarById(int $id): bool
{
    foreach ($this->carList as $i => $car) {
        if ($car->getId() === $id) {
            array_splice($this->carList, $i, 1);
            return true;
        }
    }
    return false;
}
public function search(array $filters): array
{
    $results = [];

    foreach ($this->carList as $car) {
        // make
        if (isset($filters['make']) && $filters['make'] !== '') {
            if (stripos($car->getMake(), $filters['make']) === false) continue;
        }
        // model
        if (isset($filters['model']) && $filters['model'] !== '') {
            if (stripos($car->getModel(), $filters['model']) === false) continue;
        }
        // year
        if (isset($filters['yearMin']) && $car->getYear() < (int)$filters['yearMin']) continue;
        if (isset($filters['yearMax']) && $car->getYear() > (int)$filters['yearMax']) continue;

        // price
        if (isset($filters['priceMin']) && $car->getPrice() < (float)$filters['priceMin']) continue;
        if (isset($filters['priceMax']) && $car->getPrice() > (float)$filters['priceMax']) continue;

        $results[] = $car;
    }

    return $results;
}

}