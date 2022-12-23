<?php
namespace App\Repositories;

use App\Database;

class MySQLUserRepository implements UserRepository
{
    private \Doctrine\DBAL\Query\QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = Database::getConnection()->createQueryBuilder();
    }

    public function findByUsername(string $username):array
    {
        $user=[];
        $this->queryBuilder->select('id', 'name', 'username', 'email')
            ->from('users')
            ->where('username = :username')
            ->setParameter('username', $username);
        (!$this->queryBuilder->fetchAssociative()) ?: $user = $this->queryBuilder->fetchAssociative();
        return $user;
    }

    public function findByName(string $name): array
    {
        $user=[];
        $this->queryBuilder->select('id', 'name', 'username', 'email')
            ->from('users')
            ->where('name = :name')
            ->setParameter('name', $name);
        (!$this->queryBuilder->fetchAssociative()) ?: $user = $this->queryBuilder->fetchAssociative();
        return $user;
    }

    public function findById(int $id): array
    {
        $this->queryBuilder->select('id', 'name', 'username', 'email')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $id);
        return $this->queryBuilder->fetchAssociative();
    }
}