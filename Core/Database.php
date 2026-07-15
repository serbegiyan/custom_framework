<?php

namespace Core;

class Database
{
    public function connect() 
    {
        return new \PDO('pgsql:host=db;dbname=my_database', 'db_user', 'secret_password');
    }
}


