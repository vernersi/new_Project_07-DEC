<?php

namespace App\Controllers;

use App\Database;
use App\Redirect;
use App\Services\RequestFromApiService;
use App\Template;
use Doctrine\DBAL\Query;

class TradeController
{
    private float $balance;
    private Query\QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = Database::getConnection()->createQueryBuilder();
        $this->queryBuilder->select('balance')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $balance = $this->queryBuilder->fetchAssociative();
        $this->balance = $balance['balance'];
    }

    public function buy(): Redirect
    {
        $stockSymbol = $_POST['stock_symbol'];
        $stock = new RequestFromApiService();
        $currentPrice = ($stock->getStockInfoFromApi($stockSymbol)->getStocksCollection()[0]->getCurrentPrice());
        $tradeValue = $currentPrice * $_POST['quantity'];
//get the balance of user from database with session id
        if ($this->balance < $tradeValue) {
            $_SESSION['errors']['notEnoughBalance'] = 'Sorry, you do not have enough balance to buy this stock!';
            return new Redirect('/home/search?stock=' . $stockSymbol);
        }
        $this->queryBuilder->update('users')
            ->set('balance', $this->balance - $tradeValue)
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $this->queryBuilder->executeQuery();

        $this->queryBuilder->insert('transactions')
            ->values([
                'stock_symbol' => ':stock_symbol',
                'user_id' => ':user_id',
                'amount' => ':quantity',
                'price' => ':price',
                'type' => ':type',
                'created_at' => ':created_at',
            ])
            ->setParameter('stock_symbol', $stockSymbol)
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('quantity', $_POST['quantity'])
            ->setParameter('price', $currentPrice)
            ->setParameter('type', 'buy')
            ->setParameter('created_at', date('Y-m-d H:i:s'));
        $this->queryBuilder->executeQuery();
        $_SESSION['errors']['successfulBuy'] =
            'You have successfully bought ' . $_POST['quantity'] . ' shares of ' . $stockSymbol . '!';
        return new Redirect('/profile');
    }


    public function sell(): Redirect
    {
        $stockSymbol = $_POST['stock_symbol'];
        $stock = new RequestFromApiService();
        $currentPrice = ($stock->getStockInfoFromApi($stockSymbol)->getStocksCollection()[0]->getCurrentPrice());
        $tradeValue = $currentPrice * $_POST['quantity'];
        $amount = $_POST['quantity'];
        //select from database the amount of stocks that user has
        $this->queryBuilder->select('user_id', 'stock_symbol', 'amount', 'price', 'type')
            ->from('transactions')
            ->where('user_id = :user_id', 'stock_symbol = :stock_symbol')
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('stock_symbol', $stockSymbol);
        $transactions = $this->queryBuilder->fetchAllAssociative();
        $stockAmount = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'buy' || $transaction['type'] === 'received') {
                $stockAmount += $transaction['amount'];
            } else {
                $stockAmount -= $transaction['amount'];
            }
        }
        if ($stockAmount < $amount) {
            $_SESSION['errors']['notEnoughStocks'] = 'Sorry, you do not have enough stocks to sell!';
            return new Redirect('/home/search?stock=' . $stockSymbol);
        }
        $this->queryBuilder->update('users')
            ->set('balance', $this->balance + $tradeValue)
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $this->queryBuilder->executeQuery();

        $this->queryBuilder->insert('transactions')
            ->values([
                'stock_symbol' => ':stock_symbol',
                'user_id' => ':user_id',
                'amount' => ':quantity',
                'price' => ':price',
                'type' => ':type',
                'created_at' => ':created_at',
            ])
            ->setParameter('stock_symbol', $stockSymbol)
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('quantity', $_POST['quantity'])
            ->setParameter('price', $currentPrice)
            ->setParameter('type', 'sell')
            ->setParameter('created_at', date('Y-m-d H:i:s'));
        $this->queryBuilder->executeQuery();
        $_SESSION['errors']['successfulSell'] =
            'You have successfully sold ' . $_POST['quantity'] . ' shares of ' . $stockSymbol . '!';
        return new Redirect('/profile');
    }

    public function short(): Redirect
    {
        $stockSymbol = $_POST['stock_symbol'];
        $stock = new RequestFromApiService();
        $currentPrice = ($stock->getStockInfoFromApi($stockSymbol)->getStocksCollection()[0]->getCurrentPrice());
        $tradeValue = $currentPrice * $_POST['quantity'];
        if ($this->balance < $tradeValue) {
            $_SESSION['errors']['notEnoughBalance'] = 'Sorry, you do not have enough balance to short this stock!';
            return new Redirect('/home/search?stock=' . $stockSymbol);
        }
        $this->queryBuilder->update('users')
            ->set('balance', $this->balance - $tradeValue)
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $this->queryBuilder->executeQuery();

        $this->queryBuilder->insert('transactions')
            ->values([
                'stock_symbol' => ':stock_symbol',
                'user_id' => ':user_id',
                'amount' => ':quantity',
                'price' => ':price',
                'type' => ':type',
                'created_at' => ':created_at',
            ])
            ->setParameter('stock_symbol', $stockSymbol)
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('quantity', $_POST['quantity'])
            ->setParameter('price', $currentPrice)
            ->setParameter('type', 'short')
            ->setParameter('created_at', date('Y-m-d H:i:s'));
        $this->queryBuilder->executeQuery();
        $_SESSION['errors']['successfulShort'] =
            'You have successfully shorted ' . $_POST['quantity'] . ' shares of ' . $stockSymbol . '!';
        return new Redirect('/profile');
    }

    //TODO Close short position
    public function closeShort(){
        $stockSymbol = $_POST['stock_symbol'];
        $stock = new RequestFromApiService();
        $currentPrice = ($stock->getStockInfoFromApi($stockSymbol)->getStocksCollection()[0]->getCurrentPrice());
        $tradeValue = $currentPrice * $_POST['quantity'];
        $amount = $_POST['quantity'];
        //select from database the amount of stocks that user has
        $this->queryBuilder->select('user_id', 'stock_symbol', 'amount', 'price', 'type')
            ->from('transactions')
            ->where('user_id = :user_id', 'stock_symbol = :stock_symbol')
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('stock_symbol', $stockSymbol);
        $transactions = $this->queryBuilder->fetchAllAssociative();
        $stockAmount = 0;
        $stockValue=0;
        foreach ($transactions as $transaction) {
            if ($transaction['type'] === 'short'){
                $stockAmount += $transaction['amount'];
                $stockValue += $transaction['amount'] * $transaction['price'];
            }
        $totalValue = $stockValue / $stockAmount;
            $tradeValue = $totalValue + $currentPrice;
        }

    }

}
