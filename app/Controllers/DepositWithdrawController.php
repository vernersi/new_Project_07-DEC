<?php
namespace App\Controllers;

use App\Database;
use App\Redirect;

class DepositWithdrawController{
    public function deposit():Redirect{
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $queryBuilder->select('balance')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $balance = $queryBuilder->fetchAssociative();
        $queryBuilder->update('users')
            ->set('balance', $balance['balance'] + $_POST['deposit'])
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $queryBuilder->executeQuery();
        $_SESSION['errors']['successfulDeposit'] = 'You have successfully deposited $' . $_POST['deposit'] . '!';
        return new Redirect('/profile');
    }
    public function withdraw():Redirect{
        $queryBuilder = Database::getConnection()->createQueryBuilder();
        $queryBuilder->select('balance')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $balance = $queryBuilder->fetchAssociative();
        if ($balance['balance'] < $_POST['withdraw']) {
            $_SESSION['errors']['notEnoughBalance'] = 'Sorry, you do not have enough balance to withdraw this amount!';
            return new Redirect('/profile');
        }
        $queryBuilder->update('users')
            ->set('balance', $balance['balance'] - $_POST['withdraw'])
            ->where('id = :id')
            ->setParameter('id', $_SESSION['auth_id']);
        $queryBuilder->executeQuery();
        $_SESSION['errors']['successfulWithdraw'] = 'You have successfully withdrawn $' . $_POST['withdraw'] . '!';
        return new Redirect('/profile');
    }
}