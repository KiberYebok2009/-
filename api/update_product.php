<?php
    // Подключение к базе данных и другие необходимые файлы
    include_once 'config/database_skins.php';
    include_once 'objects/product.php';
    
    // Получение подключения к базе данных
    $database = new Database();
    $db = $database->getConnection();
    
    // Создание объекта Product
    $product = new Product($db);
    
    // Получение данных из POST-запроса
    $product->id = $_POST['id'];
    $product->name = $_POST['name'];
    $product->price = $_POST['price'];
    
    // Обновление продукта
    if($product->update_product()) {
        // Установка кода ответа - 200 OK
        http_response_code(200);
    
        // Сообщение пользователю
        echo json_encode(array("message" => "Продукт был обновлен."));
    } else {
        // Если не удается обновить продукт, сообщить пользователю
        // Установка кода ответа - 503 Сервис недоступен
        http_response_code(503);
    
        // Сообщение пользователю
        echo json_encode(array("message" => "Невозможно обновить продукт."));
    }
?>