<?php

namespace App\Core;

use PDO;

class Database
{
    public function connect(): PDO
    {
        return new PDO('pgsql:host=db;dbname=my_database', 'db_user', 'secret_password');
    }
}
