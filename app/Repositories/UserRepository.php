<?php
namespace App\Repositories;

use App\Models\StockInfoCollection;

interface UserRepository
{
    public function findByUsername(string $username):array;
    public function findByName(string $name):array;
    public function findById(int $id):array;
}