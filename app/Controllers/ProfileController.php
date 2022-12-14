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
                    'stock_symbol' => $trade['stock_symbol'],
                    'amount' => $trade['amount'],
                    'price' => $trade['price'],
                    'type' => $trade['type'],
                    'total' => $trade['amount'] * $trade['price']
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
            $uniqueTrades[$key]['total'] = number_format($trade['total'], 2);
            $uniqueTrades[$key]['currentValue']= number_format(($trade['amount'] * $currentPrice),2);
            $uniqueTrades[$key]['profit']= number_format(($uniqueTrades[$key]['currentValue'] - $uniqueTrades[$key]['total']),2);
        }
        return new Template('profile.twig', ['title' => 'Profile', 'trades' => $uniqueTrades, 'allTrades' =>$allTrades]);
    }
}