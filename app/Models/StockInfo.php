<?php

namespace App\Models;
class StockInfo
{
    private float $currentPrice;
    private float $priceChange;
    private string $name;
    private string $symbol;

    public function __construct(string $symbol, string $name, float $currentPrice, float $priceChange)
    {
        $this->name = $name;
        $this->currentPrice = $currentPrice;
        $this->priceChange = $priceChange;
        $this->symbol = $symbol;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
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