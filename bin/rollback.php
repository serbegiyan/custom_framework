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

$arguments = isset($argv[1]) ? $argv[1] : null;
$steps = 1;
$isAll = false;

if ($arguments == '--all'){
    $isAll = true;
} elseif ($arguments !== null) {
    if((int)$arguments <= 0){       
        fwrite(STDERR, 'Ошибка аргумента. Шаг миграции не может быть <= 0');
        exit(1);
    }
    $steps = (int)$arguments; 
}

$env = $_ENV['APP_ENV'] ?? 'local';
if($env === 'production' and ($isAll === true or $steps > 1)){
    fwrite(STDOUT, "Вы собираетесь откатить несколько миграций сразу. Вы уверены7 д/н\n");
    $handle = fopen("php://stdin", "r");
    $input = fgets($handle); 
    $confirmation = trim($input);
    if(strtolower($confirmation) !== 'д'){
        fwrite(STDOUT, 'Rollback canceled');
        exit(0);
    }
}

$sql = "SELECT migration FROM migrations ORDER BY id DESC";
 
$migrations = $db->select($sql, []);

$plainMigrations = array_column($migrations, 'migration');

if(!$isAll){
    $plainMigrations = array_slice($plainMigrations, 0, $steps);
}

if (empty($plainMigrations)) {
    fwrite(STDOUT, "Нет миграций для отката.\n");
    exit(0);
}

$dir = __DIR__ . '/../database/Migrations/';

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