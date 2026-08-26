<?php

use Core\Database;
use App\Validators\CsvValidator;
use App\Services\CsvReader;
use App\Repositories\StatisticRepository;
use App\Services\ImporterService;
use App\ValueObjects\OrganizationId;

require_once __DIR__ . '/vendor/autoload.php';

// Инициализируем подключение к БД
$dsn = "pgsql:host=db;port=5432;dbname=my_database";
$user = "db_user";
$password = "secret_password";
$db = new Database($dsn, $user, $password);

echo "=== 1. Очистка базы данных ===\n";
$db->execute('TRUNCATE TABLE statics RESTART IDENTITY CASCADE', []);
echo "Таблица statics успешно очищена.\n\n";

echo "=== 2. Прогон одной строки через Валидатор и DTO ===\n";
$validator = new CsvValidator();

// Берем заголовки и первую валдиную строку из вашего файла
$headers = ['country', 'city', 'is_active', 'gender', 'birth_date', 'salary', 'has_children', 'family_status', 'registration_date', 'organization_id'];
$row = ['Brazil', 'Okeyview', 'false', 'female', '2014-10-28', '80930', 'false', 'divorced', '2022-07-18', '1'];

try {
    $dto = $validator->validate($row, $headers);
    echo "Валидация успешна! Объект CsvRow создан.\n";
    
    $batch = $dto->toDatabaseArray();
    echo "Результат метода toDatabaseArray():\n";
    print_r($batch);
    
    echo "\n=== 3. Попытка записи в репозиторий ===\n";
    $repo = new StatisticRepository($db);
    $orgId = new OrganizationId(1);
    
    // Передаем чанк из одной строки
    $repo->insertBatch([$batch], $orgId->orgId);
    echo "Успех! Данные успешно записаны в PostgreSQL.\n";

} catch (\Throwable $e) {
    echo "❌ ПРОИЗОШЛА ОШИБКА:\n";
    echo "Тип: " . get_class($e) . "\n";
    echo "Сообщение: " . $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . " на строке " . $e->getLine() . "\n";
}
