<?php
namespace App\Services;

class RegisterServicesRequest
{
    public string $username;

    public string $name;
    public string $email;
    public string $password;

    public function __construct(string $username, string $name, string $email, string $password)
    {
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function getName(): string
    {
        return $this->name;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function getPassword(): string
    {
        return password_hash($this->password,PASSWORD_DEFAULT);
    }
}
