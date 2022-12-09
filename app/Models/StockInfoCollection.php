<?php
namespace App\Models;
use App\Models\StockInfo;

class StockInfoCollection
{
private array $stocks = [];

public function addStockToCollection(StockInfo $stockInfo):void
{
    $this->stocks []= $stockInfo;
}

    public function getStocksInfo():array
    {
        return $this->stocks;
    }
}