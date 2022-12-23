<?php
namespace App\Controllers;

use App\Services\RequestFromApiService;
use App\Template;

class SearchController
{
    public function search(): Template
    {
        if (empty($_SESSION['auth_id'])) {
            header('Location: /');
        }
        $requestFromApi = new RequestFromApiService();
        $stocks = $requestFromApi->getStockInfoFromApi($_GET['stock']);
        $stock = $stocks->getStocksCollection();
        return new Template('/search.twig',
            ['title' => 'Stock View', 'stock' => $stock[0]]
        );
    }
}