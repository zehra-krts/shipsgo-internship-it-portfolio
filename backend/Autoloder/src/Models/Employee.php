<?php
declare(strict_types=1);
namespace App\Models;

class Employee extends Person {
    protected int $soldedCars = 0;
    protected float $salary = 0.0;

    public function __construct(string $name,
    ?string $email = null,
    ?string $phone = null,
    float $salary = 0.0) {
        parent::__construct($name, $email, $phone);
        $this->salary = $salary;
    }
    //getters
    public function getSalary(): float {
        return $this->salary;
    }
    public function getSoldedCars(): int {
        return $this->soldedCars;
}
//setters

public function setSalary(float $salary): self {
    if ($salary < 0) {
        throw new \InvalidArgumentException('Salary cant be less than 0.');
    }
    $this->salary = $salary;
    return $this;
}
public function incrementSoldedCars(): void
    {
        $this->soldedCars++;
    }

    public function resetSoldedCars(): void
    {
        $this->soldedCars = 0;
    }
}