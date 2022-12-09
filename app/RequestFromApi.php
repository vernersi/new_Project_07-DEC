<?php

namespace App;
use App\Models\StockInfoCollection;
use Finnhub;
use GuzzleHttp;
use App\Models\StockInfo;

class RequestFromApi
{
    private Finnhub\Api\DefaultApi $apiConfig;
    private StockInfoCollection $stocks;
    public function __construct()
    {
        $this->stocks=new StockInfoCollection();
    }

    public function getStockInfoFromApi(string $stockName):void
    {
        $apiKey = $_ENV['apiToken'];
        $config = Finnhub\Configuration::getDefaultConfiguration()->setApiKey('token', $apiKey);
        $this->apiConfig = new Finnhub\Api\DefaultApi(
            new GuzzleHttp\Client(),
            $config
        );
        $symbolLookup = $this->apiConfig->symbolSearch($stockName);
        $stockSymbol = $symbolLookup['result'][0]['display_symbol'];
        $stockOfficialName = $symbolLookup['result'][0]['description'];
        $stockQuote = $this->apiConfig->quote($stockSymbol);
        $stockPrice = $stockQuote['c'];
        $priceChange= $stockQuote['d'];
        $this->stocks->addStockToCollection(new StockInfo($stockOfficialName,$stockPrice,$priceChange));
    }

    public function getStockCollection(): array
    {
        return $this->stocks->getStocksInfo();
    }

}
