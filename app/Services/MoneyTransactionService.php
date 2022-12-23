<?php
namespace App\Services;
use App\Database;

class MoneyTransactionService{
    private \Doctrine\DBAL\Query\QueryBuilder $queryBuilder;
    private float $balance;

    public function __construct(){
        $this->queryBuilder = Database::getConnection()->createQueryBuilder();
        $this->queryBuilder->select('balance')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $this->balance = $this->queryBuilder->fetchAssociative()['balance'];
    }
    public function deposit($amount):bool
    {
        if ($amount <= 0) {
            return false;
        }
        $this->queryBuilder->update('users')
            ->set('balance', 'balance + :deposit')
            ->where('id = :id')
            ->setParameters([
                'deposit' => $amount,
                'id' => $_SESSION['auth_id'],
            ]);
       ($this->queryBuilder->executeQuery());
        return true;
    }

    public function withdraw($amount):bool{
        if ($this->balance < $amount) {
            return false;
        }
        $this->queryBuilder->update('users')
            ->set('balance', $this->balance - $amount)
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $this->queryBuilder->executeQuery();
        return true;
    }
}