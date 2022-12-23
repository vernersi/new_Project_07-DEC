<?php
namespace App\Controllers;

use App\Database;
use App\Services\RequestFromApiService;
use App\Template;

class ProfileController{
    public function showForm():Template{
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $queryBuilder->select('stock_symbol', 'amount', 'price', 'type')
            ->from('transactions')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $_SESSION['auth_id']);
        $allTrades = $queryBuilder->fetchAllAssociative();
        $uniqueTrades=[];
        foreach ($allTrades as $trade){
            if (!array_key_exists($trade['stock_symbol'], $uniqueTrades)){
                $uniqueTrades[$trade['stock_symbol']] = [
                    'stock_symbol' =>(string) $trade['stock_symbol'],
                    'amount' =>(int) $trade['amount'],
                    'price' =>(float) $trade['price'],
                    'type' =>(string) $trade['type'],
                    'total' =>(int) $trade['amount'] * (float) $trade['price']
                ];
        }elseif ($trade['type'] === 'buy'){
                $uniqueTrades[$trade['stock_symbol']]['amount'] += $trade['amount'];
                $uniqueTrades[$trade['stock_symbol']]['total'] += $trade['amount'] * $trade['price'];
            } else {
                $uniqueTrades[$trade['stock_symbol']]['amount'] -= $trade['amount'];
                $uniqueTrades[$trade['stock_symbol']]['total'] -= $trade['amount'] * $trade['price'];
            }
        }
        foreach ($uniqueTrades as $key => $trade){
            if ($trade['amount'] === 0){
                unset($uniqueTrades[$key]);
            }
        }
        foreach ($uniqueTrades as $key => $trade){
            $stock = new RequestFromApiService();
            $currentPrice = ($stock->getStockInfoFromApi($trade['stock_symbol'])->getStocksCollection()[0]->getCurrentPrice());
            $uniqueTrades[$key]['currentValue']= $trade['amount'] * $currentPrice;
            $uniqueTrades[$key]['total'] = $trade['total'];
            $uniqueTrades[$key]['profit']= $uniqueTrades[$key]['currentValue'] - $uniqueTrades[$key]['total'];
            $uniqueTrades[$key]['profitPercentage']= $uniqueTrades[$key]['profit'] / $uniqueTrades[$key]['total'] * 100;
            $uniqueTrades[$key]['profit'] = number_format($uniqueTrades[$key]['profit'], 2);
            $uniqueTrades[$key]['profitPercentage'] = number_format($uniqueTrades[$key]['profitPercentage'], 2);
            $uniqueTrades[$key]['total'] = number_format($uniqueTrades[$key]['total'], 2);
            $uniqueTrades[$key]['currentValue'] = number_format($uniqueTrades[$key]['currentValue'], 2);
        }
        return new Template('profile.twig', ['title' => 'Profile', 'trades' => $uniqueTrades, 'allTrades' =>$allTrades]);
    }
}