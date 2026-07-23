<?php

namespace App\Interfaces;

use App\Interfaces\DatabaseInterface;

interface MigrationInterface
{
    public function up(DatabaseInterface $db): void;
    public function down(DatabaseInterface $db): void;
}