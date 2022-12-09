<?php
namespace App\ViewVariables;

class ErrorsViewVariables {
    public function getName():string
    {
        return 'errors';
    }

    public function getValue():array
    {
        return $_SESSION['errors'] ?? [];
    }
}