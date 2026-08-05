<?php

use App\Core\Container;
use App\Core\Database;
use App\Interfaces\DatabaseInterface;
use App\Interfaces\ContainerInterface;
use App\Interfaces\MigrationInterface;

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

$container = new Container();
$container->set(DatabaseInterface::class, function(ContainerInterface $c){
    $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    return new Database($dsn, $user, $password);
});

$db = $container->get(DatabaseInterface::class);

$sql = 'CREATE TABLE IF NOT EXISTS migrations (
    id int NOT NULL PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    migration VARCHAR(100))';

$db->execute($sql, []);

$oldMigrations = 'SELECT migration FROM migrations';

$migrations = $db->select($oldMigrations, []);
$plainMigrations = array_column($migrations, 'migration');

$dir = 'src/Database/Migrations/';
$files = scandir($dir);
$issetFiles = array_diff($files, ['.', '..']);

$migrationsForRunning = array_diff($issetFiles, $plainMigrations);

foreach($migrationsForRunning as $oneMigration){
    try{
        $db->beginTransaction();
        $migration = require $dir . $oneMigration;
        if ($migration instanceof MigrationInterface) {
            $migration->up($db);
        }
        $sqlIns = "INSERT INTO migrations (migration) VALUES (?)";
        $db->execute($sqlIns, [$oneMigration]);
        $db->commit();
        fwrite(STDOUT, "\033[32m[Успешно]\033[0m Миграция $oneMigration применена.\n");
    }catch(\Throwable $e){
        $db->rollback();

        $errorMessage = sprintf(
            "\n\033[31m[Ошибка миграции]\033[0m %s (Файл: %s, Строка: %d)\n",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        fwrite(STDERR, $errorMessage);

        exit(1);
    }    
}