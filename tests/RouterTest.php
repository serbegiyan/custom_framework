<?php

// 1. Включаем отображение ошибок и ассерты
ini_set('zend.assertions', 1);
assert_options(ASSERT_EXCEPTION, 1);
error_reporting(E_ALL);

// 2. Подключаем тестируемый класс
require_once 'core/Router.php';

echo "Запуск тестов...\n";

try {
    $router = new Router();

    // Тест 1: Проверка сложения положительных чисел
    assert($router->getPath('https://github.com/serbegiyan/petProject_Innowise/example.php') === 'serbegiyan/petProject_Innowise/example.php', "Тест провален");
   
    echo "Все тесты успешно пройдены! ✅\n";

} catch (AssertionError $e) {
    echo "❌ Тест упал: " . $e->getMessage() . "\n";
    exit(1); // Возвращаем код ошибки для консоли
}