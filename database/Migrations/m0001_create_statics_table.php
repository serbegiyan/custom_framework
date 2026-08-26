<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class () implements MigrationInterface {
    public function up(DatabaseInterface $db): void
    {
        $db->execute("CREATE TYPE gender_enum AS ENUM ('male', 'female')", []);
        $db->execute("CREATE TYPE family_status_enum AS ENUM ('single', 'married', 'divorced')", []);

        $sql = "CREATE TABLE IF NOT EXISTS statics (
            id int NOT NULL PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
            country VARCHAR(100),
            city VARCHAR(100),
            is_active boolean,
            gender gender_enum,
            birth_date date,
            salary int,
            has_children boolean,
            family_status family_status_enum,
            registration_date date
        )";

        $db->execute($sql, []);
    }

    public function down(DatabaseInterface $db): void
    {
        $sql = 'DROP TABLE IF EXISTS statics';

        $db->execute($sql, []);
    }
};
