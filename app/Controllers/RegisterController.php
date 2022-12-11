<?php
namespace App\Controllers;
use App\Redirect;
use App\Services\RegisterService;
use App\Services\RegisterServicesRequest;
use App\Template;
use App\ViewVariables\ErrorsViewVariables;

class RegisterController
{
    public function showForm(): Template
    {
        return new Template('register.twig', [
            new ErrorsViewVariables(),
        ]);
    }

    public function store(): Redirect
    {
        if ($_POST['password'] !== $_POST['password-repeat']) {
            $_SESSION['errors']['password'] = 'Passwords does not match!';
        }
        if (! empty($_SESSION['errors'])) {
            return new Redirect('/register');
        }
        $registerService = new RegisterService();
        $registerService->execute(
            new RegisterServicesRequest(
                $_POST['username'],
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
            )
        );
        return new Redirect('/');
    }
}