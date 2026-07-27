<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class () implements MigrationInterface {
    public function up(DatabaseInterface $db): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS organizations (
        id int NOT NULL PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
        name VARCHAR(100) NOT NULL,
        created_at date, 
        updated_at date
        )';

        $db->execute($sql, []);
    }

    public function down(DatabaseInterface $db): void
    {
        $sql = 'DROP TABLE IF EXISTS organizations';

        $db->execute($sql, []);
    }
};
