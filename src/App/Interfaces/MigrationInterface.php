<?php

namespace App\Interfaces;

interface MigrationInterface
{
    public function up(DatabaseInterface $db): void;
    public function down(DatabaseInterface $db): void;
}
