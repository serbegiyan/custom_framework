<?php

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

use App\Core\Container;
use App\Core\Database;
use App\Interfaces\DatabaseInterface;
use App\Interfaces\ContainerInterface;
use App\Interfaces\MigrationInterface;

$container = new Container();
$container->set(DatabaseInterface::class, function(ContainerInterface $c){
    $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    return new Database($dsn, $user, $password);
});

$db = $container->get(DatabaseInterface::class);

$steps = isset($argv[1]) ? (int)$argv[1] : 0;

$sql = "SELECT migration FROM migrations ORDER BY id DESC";
 
$migrations = $db->select($sql, []);

$plainMigrations = array_column($migrations, 'migration');
if($steps > 0){
    $plainMigrations = array_slice($plainMigrations, 0, $steps);
}

$dir = 'src/Database/Migrations/';

foreach($plainMigrations as $oneMigration){
    try{
        $db->beginTransaction();
        $migration = require $dir . $oneMigration;
        if ($migration instanceof MigrationInterface) {
            $migration->down($db);
        }
        $sqlIns = "DELETE FROM migrations WHERE migration = (?)";
        $db->execute($sqlIns, [$oneMigration]);
        $db->commit();
        fwrite(STDOUT, "\033[32m[Успешно]\033[0m Миграция $oneMigration откатилась.\n");
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