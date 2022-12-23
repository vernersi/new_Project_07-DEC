<?php
namespace App\Controllers;
use App\Redirect;
use App\Services\LoginService;
use App\Services\LoginServicesRequest;
use App\Template;

class IndexController
{
    public function index(): Template
    {
        return new Template('layout.twig', ['title' => 'Login']);
    }

    public function login():Redirect
    {
        return (new LoginService())->execute(
            new LoginServicesRequest(
                $_POST['username'],
                $_POST['password']
            )
        );
    }
}