<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class () implements MigrationInterface {
    public function up(DatabaseInterface $db): void
    {
        $sqlAdd = 'ALTER TABLE statics ADD organization_id INT NULL';
        $sqlInsert = "INSERT INTO organizations (name, created_at, updated_at)
        VALUES ('Innowise', NOW(), NOW())";
        $sqlUpdate = "UPDATE statics SET organization_id = (
        SELECT id FROM organizations WHERE name = 'Innowise' limit 1)";

        $db->execute($sqlAdd, []);
        $db->execute($sqlInsert, []);
        $db->execute($sqlUpdate, []);
    }

    public function down(DatabaseInterface $db): void
    {
        $dropAdd = 'ALTER TABLE statics DROP COLUMN organization_id';
        $dropIns = "DELETE FROM organizations WHERE name = 'Innowise'";

        $db->execute($dropAdd, []);
        $db->execute($dropIns, []);
    }
};
