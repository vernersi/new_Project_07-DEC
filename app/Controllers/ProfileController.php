<?php
namespace App\Controllers;
use App\RequestFromApi;
use App\Template;

class ProfileController
{
    public function showForm(): Template
    {
        $requestFromApi = new RequestFromApi();
        $tenStocks = ['Tesla', 'Rocketlab', 'Virgin Galactic', 'NIO', 'AAPL', 'Xpeng', 'CLNE'];
        foreach ($tenStocks as $stock){
            $requestFromApi->getStockInfoFromApi($stock);
        }
        $stocks = $requestFromApi->getStockCollection();
        return new Template('/profile.twig',
            ['title' => 'Profile', 'stocks' => $stocks]
        );
    }
}