<?php
namespace App\Services;

use App\Database;

class UsersInteractionService{
    private \Doctrine\DBAL\Query\QueryBuilder $queryBuilder;

public function __construct()
    {
        $this->queryBuilder = Database::getConnection()->createQueryBuilder();
    }

    public function getUserByUsername(string $username):array
    {
        $userRequest= new \App\Repositories\MySQLUserRepository();
        return $userRequest->findByUsername($username);
    }

    public function getUserByName(string $name):array
    {
        $userRequest= new \App\Repositories\MySQLUserRepository();
        return $userRequest->findByName($name);
    }

    public function transferStock(int $targetUserId, string $stockName, int $quantity):void
    {
        $stock = new RequestFromApiService();
        $currentPrice = ($stock->getStockInfoFromApi($stockName)->getStocksCollection()[0]->getCurrentPrice());
        //TODO check if user has enough stocks to transfer
        $this->queryBuilder->insert('transactions')
            ->values([
                'user_id' => ':user_id',
                'stock_symbol' => ':stock_symbol',
                'amount' => ':amount',
                'price' => ':price',
                'type' => ':type',
                'created_at' => ':created_at'
            ])
            ->setParameter('user_id', $_SESSION['auth_id'])
            ->setParameter('stock_symbol', $stockName)
            ->setParameter('amount', $quantity)
            ->setParameter('price', $currentPrice)
            ->setParameter('type', 'sell')
            ->setParameter('created_at', date('Y-m-d H:i:s'));
        $this->queryBuilder->executeStatement();
        $this->queryBuilder->insert('transactions')
            ->values([
                'user_id' => ':user_id',
                'stock_symbol' => ':stock_symbol',
                'amount' => ':amount',
                'price' => ':price',
                'type' => ':type',
                'created_at' => ':created_at'
            ])
            ->setParameter('user_id', $targetUserId)
            ->setParameter('stock_symbol', $stockName)
            ->setParameter('amount', $quantity)
            ->setParameter('price', $currentPrice)
            ->setParameter('type', 'buy')
            ->setParameter('created_at', date('Y-m-d H:i:s'));
        $this->queryBuilder->executeStatement();
    }

}