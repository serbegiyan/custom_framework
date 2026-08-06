<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class () implements MigrationInterface {
    public function up(DatabaseInterface $db): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS users(
            id int NOT NULL PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL
        )';

        $db->execute($sql, []);

        $password = password_hash('12345678', PASSWORD_BCRYPT);

        $sqlAdmin = "INSERT INTO users (name, email, password_hash) 
        VALUES ('admin', 'admin@admin.com', ?)";

        $db->execute($sqlAdmin, [$password]);
    }

    public function down(DatabaseInterface $db): void
    {
        $sql = "DROP TABLE IF EXISTS users";

        $db->execute($sql, []);
    }
};
