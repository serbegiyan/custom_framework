<?php

spl_autoload_register(function (string $class) {
    // 1. Задаем карту соответствия: "Пространство имен" => "Папка"
    $prefixes = [
        'Core\\' => __DIR__ . '/Core/',
        'App\\'  => __DIR__ . '/App/',
    ];

    // 2. Проходим по карте и проверяем, с какого префикса начинается имя класса
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);

        // Если вызываемый класс не относится к текущему префиксу, пропускаем
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        // Получаем относительное имя класса (отсекаем "Core\" или "App\")
        $relativeClass = substr($class, $len);

        // Заменяем обратные слэши `\` в пространствах имен на системные разделители путей `/`
        // Пример: "Database" -> "Database.php" или "SubDir\Class" -> "SubDir/Class.php"
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        // Если файл существует — подключаем его
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});