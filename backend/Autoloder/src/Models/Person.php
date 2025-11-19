<?php

namespace App\Models;

class Person {
    protected string $name;
    protected ?string $email;
    protected ?string $phone;
    // email and phone are optioanl
    public function __construct(string $name, ?string $email = null, ?string $phone = null) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }
    //getter
    public function getName() : string {
        return $this->name;
    }
    public function getEmail() : ?string {
        return $this->email;
    }
    public function getPhone() : ?string {
        return $this->phone;
    }
    //setter
    public function setName(string $name) : self {
        $name = trim($name);
        // trim > Delete leading and trailing spaces
        if ($name === '') {
            throw new \InvalidArgumentException('Name cant be empty.');
        }
        $this->name = $name;
        return $this;
    }
    public function setEmail(?string $email): self
    {
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email format is invalid.');
        }
        $this->email = $email;
        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone !== null ? trim($phone) : null;
        return $this;

    }
}
