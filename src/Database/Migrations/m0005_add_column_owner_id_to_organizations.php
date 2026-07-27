<?php

use App\Interfaces\DatabaseInterface;
use App\Interfaces\MigrationInterface;

return new class implements MigrationInterface{
    public function up(DatabaseInterface $db): void
    {
        $sql = 'ALTER TABLE organizations ADD owner_id int';

        $db->execute($sql, []);

        $sqlKey = 'ALTER TABLE organizations 
        ADD CONSTRAINT fk_organizations_owner
        FOREIGN KEY (owner_id) REFERENCES users(id)
        ON DELETE CASCADE';

        $db->execute($sqlKey, []);
    }

    public function down(DatabaseInterface $db): void
    {
        $sqlKey = 'ALTER TABLE organizations DROP CONSTRAINT fk_organizations_owner';

        $db->execute($sqlKey, []);

        $sql = 'ALTER TABLE organizations DROP COLUMN owner_id';

        $db->execute($sql, []);
    }
};