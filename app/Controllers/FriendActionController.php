<?php

namespace App\Controllers;

use App\Database;
use App\Redirect;
use App\Services\RequestFromApiService;
use App\Template;

class FriendActionController
{
    public function viewUserProfile()
    {
        $userRequest= new \App\Services\UsersInteractionService();
        $user=$userRequest->getUserByUsername($_GET['name']);
        if (empty($user)) {
            $user = $userRequest->getUserByName($_GET['name']);
        } if (empty($user)) {
            $_SESSION['errors']['userNotFound'] = 'Sorry, we could not find a user with that name or username!';
            return new Redirect('/profile');
        }
        return new Template('/viewUser.twig',
            ['title' => $user['name'], 'user' => $user]
        );
    }

    public function transferStock():Redirect
    {
        $targetUserId = $_POST['userId'];
        $targetUserName = $_POST['userName'];
        $stockName = $_POST['stockName'];
        $quantity = $_POST['quantity'];
        if($quantity < 1){
            $_SESSION['errors']['invalidQuantity'] = 'Sorry, you must transfer at least 1 share!';
            return new Redirect('/viewUser?name=' . $targetUserName);
        }
        $transferRequest= new \App\Services\UsersInteractionService();
        $transferRequest->transferStock($targetUserId, $stockName, $quantity);
        $_SESSION['errors']['successfulTransfer'] = 'You have successfully transferred ' . $quantity . ' shares of ' . $stockName . ' to ' . $targetUserName . '!';
        return new Redirect('/profile');
    }

}