<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class () implements MigrationInterface {
    public function up(DatabaseInterface $db): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS users (
            id int NOT NULL PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
            country VARCHAR(100),
            city VARCHAR(100),
            is_active boolean,
            gender VARCHAR(20),
            birth_date date,
            salary int,
            has_children boolean,
            family_status VARCHAR(100),
            registration_date date
        )';

        $db->execute($sql, []);
    }

    public function down(DatabaseInterface $db): void
    {
        $sql = 'DROP TABLE IF EXISTS users';

        $db->execute($sql, []);
    }
};
