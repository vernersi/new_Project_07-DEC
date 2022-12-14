<?php

namespace App\Controllers;
use App\Redirect;

class LogoutController
{
    public function execute(): Redirect
    {
        if (isset($_SESSION['auth_id'])) {
            session_destroy();
            $_SESSION = array();
        }
        return new Redirect('/');
    }
}