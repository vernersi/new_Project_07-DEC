<?php
namespace App\Models;
class StockInfo
{
    private float $currentPrice;
    private float $priceChange;
    private string $name;

    public function __construct(string $name, float $currentPrice, float $priceChange)
    {
        $this->name = $name;
        $this->currentPrice = $currentPrice;
        $this->priceChange = $priceChange;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrentPrice(): float
    {
        return $this->currentPrice;
    }

    public function getPriceChange(): float
    {
        return $this->priceChange;
    }
}