<?php

namespace App\ViewVariables;

use App\Database;

class AuthViewVariables
{

    public function __construct()
    {
        // TODO connect to database
        // TODO we have $session['auth_id']
    }

    public function getName(): string
    {
        return 'auth';
    }

    public function getValue(): array
    {
        if (!isset($_SESSION['auth_id'])) return [];
        // SELECT * FROM users WHERE id = $_SESSION['auth_id']
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $user = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $_SESSION['auth_id'])
            ->fetchAssociative();

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'balance' => $user['balance'],
        ];
    }

}