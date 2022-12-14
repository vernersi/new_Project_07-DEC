<?php
namespace App\Repositories;

use App\Models\StockInfoCollection;

interface StocksRepository
{
    public function findBySymbol(string $stockName): StockInfoCollection;
}