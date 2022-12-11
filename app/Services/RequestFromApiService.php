<?php

namespace App\Services;
use App\Models\StockInfoCollection;
use App\Repositories\FinhubStockRepository;

class RequestFromApiService
{
    public function getStockInfoFromApi(string $stockName): StockInfoCollection
    {
        $request = new FinhubStockRepository();
        return $request->findBySymbol($stockName);
    }



}
