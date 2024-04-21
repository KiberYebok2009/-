<?php
// Подключение к базе данных и другие необходимые файлы
include_once '../../config/database.php';
include_once '../../objects/product.php';

// Получение подключения к базе данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта Product
$product = new Product($db);

// Получение ID товара из запроса
$product->id = isset($_POST['id']) ? $_POST['id'] : die('ID товара не указан.');

// Удаление товара
if ($product->delete()) {
    echo json_encode(array("message" => "Товар был удален."));
} else {
    echo json_encode(array("message" => "Не удалось удалить товар."));
}
?>