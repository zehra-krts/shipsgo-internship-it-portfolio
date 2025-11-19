<?php
declare(strict_types=1);

namespace App\Models;

class Payment
{
    private float $amount;
    private string $method; // 'cash' | 'card' | 'transfer'
    private \DateTimeImmutable $date;
    private string $status; // 'pending' | 'paid' | 'failed'

    public function __construct(float $amount, string $method)
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be > 0.');
        }
        $this->amount = $amount;
        $this->method = $method;
        $this->date   = new \DateTimeImmutable('now');
        $this->status = 'pending';
    }

    // GETTERS
    public function getAmount(): float { return $this->amount; }
    public function getMethod(): string { return $this->method; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function getStatus(): string { return $this->status; }

    // ACTIONS
    public function markPaid(): void   { $this->status = 'paid'; }
    public function markFailed(): void { $this->status = 'failed'; }
}