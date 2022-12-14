<?php

namespace App\Services;

use App\Database;
use App\Redirect;

class LoginService
{
    public function execute(LoginServicesRequest $request): Redirect
    {
        $userName = $request->getUsername();
        $password = $request->getPassword();
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $queryBuilder->select('id', 'username', 'password', 'balance')
            ->from('users')
            ->where('username = :username')
            ->setParameter('username', $userName);
        $dbResult = $queryBuilder->fetchAssociative();
        if ($dbResult === false) {
            $_SESSION['errors']['details'] = 'Password / username incorrect!';
        }
        if (! empty($_SESSION['errors'])){
            return new Redirect('/');
        }
        if (password_verify($password,$dbResult['password'])){
            $_SESSION['auth_id']=$dbResult['id'];
            return new Redirect('/home');
        } else {
            return new Redirect('/');
        }

    }
}