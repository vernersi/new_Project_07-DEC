<?php
namespace App\Controllers;

use App\Database;
use App\Redirect;
use App\Services\MoneyTransactionService;

class DepositWithdrawController{
    public function deposit():Redirect{
    $transaction = new MoneyTransactionService();
    $validation = ($transaction->deposit($_POST['deposit']));
        if ($validation) {
            $_SESSION['errors']['successfulDeposit'] = 'You have successfully deposited $' . $_POST['deposit'] . '!';
        }  else {
            $_SESSION['errors']['notEnoughBalance'] = 'Sorry, the amount you entered is invalid!';
        }
        return new Redirect('/profile');
    }
    public function withdraw():Redirect{
        $transaction = new MoneyTransactionService();
        $validation = ($transaction->withdraw($_POST['withdraw']));
        if ($validation) {
            $_SESSION['errors']['successfulWithdraw'] = 'You have successfully withdrawn $' . $_POST['withdraw'] . '!';
        }  else {
            $_SESSION['errors']['notEnoughBalance'] = 'Sorry, the amount you entered is invalid!';
        }
        return new Redirect('/profile');
    }
}