<?php

namespace App\Repositories;

use App\Models\StockInfo;
use App\Models\StockInfoCollection;
use Finnhub;
use GuzzleHttp;

class FinhubStockRepository implements StocksRepository
{
    private StockInfoCollection $stocks;

    public function __construct()
    {
        $this->stocks = new StockInfoCollection();
    }

    public function findBySymbol(string $stockName): StockInfoCollection
    {
        $apiKey = $_ENV['apiToken'];
        $config = Finnhub\Configuration::getDefaultConfiguration()->setApiKey('token', $apiKey);
        $apiConfig = new Finnhub\Api\DefaultApi(
            new GuzzleHttp\Client(),
            $config
        );
        $symbolLookup = $apiConfig->symbolSearch($stockName);
        $stockSymbol = $symbolLookup['result'][0]['display_symbol'];
        $stockOfficialName = $symbolLookup['result'][0]['description'];
        $stockQuote = $apiConfig->quote($stockSymbol);
        $stockPrice = $stockQuote['c'];
        $priceChange = $stockQuote['d'];
        $this->stocks->addStockToCollection(new StockInfo(
            $stockSymbol,
            $stockOfficialName,
            $stockPrice,
            $priceChange));
        return $this->stocks;
    }

}