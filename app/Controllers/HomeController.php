<?php
namespace App\Controllers;
use App\Services\RequestFromApiService;
use App\Template;

class HomeController
{
    public function showForm(): Template
    {
        $stocks = [];
        $requestFromApi = new RequestFromApiService();
        $tenStocks = ['TSLA', 'AAPL', 'AMZN', 'NVDA', 'NFLX', 'MSFT'];
        foreach ($tenStocks as $stock){
           $stock=$requestFromApi->getStockInfoFromApi($stock);
              $stocks []= $stock->getStocksCollection()[0];
        }
        return new Template('/home.twig',
            ['title' => 'home', 'stocks' => $stocks]
        );
    }

}