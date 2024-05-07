<?php
// Подключение к базе данных и другие необходимые файлы
include_once '../../config/database_skins.php';
include_once '../../objects/product.php';

// Получение подключения к базе данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта Product
$product = new Product($db);

// Получение ID продукта из запроса
$product_id = isset($_GET['id']) ? $_GET['id'] : die("Product ID not specified.");

// Получение данных о продукте
$product_data = $product->getProducts($product_id);

// Проверка, найден ли продукт
if($product_data) {
    // Установка кода ответа 200 OK
    http_response_code(200);

    // Вывод данных о продукте в формате JSON
    echo json_encode($product_data);
} else {
    // Установка кода ответа 404 Not Found
    http_response_code(404);

    // Сообщение пользователю, что продукт не найден
    echo json_encode(array("message" => "Product does not exist."));
}
?>