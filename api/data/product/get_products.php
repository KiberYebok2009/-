<?php
    // Подключение к базе данных и другие необходимые файлы
    include_once '../../config/database_skins.php';
    include_once '../../objects/product.php';
    
    // Получение подключения к базе данных
    $database = new Database();
    $db = $database->getConnection();
    
    // Создание объекта Product
    $product = new Product($db);
    
    // Получение параметров фильтрации из запроса
    $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : null;
    $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : null;
    $categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

    // Получение списка товаров с учетом фильтрации
    $products_arr = $product->getProducts(null, $minPrice, $maxPrice, $categoryId);
    
    // Установка заголовка Content-Type для ответа
    header('Content-Type: application/json');
    
    // Проверка, найдены ли товары
    if(count($products_arr) > 0) {
        // Установка кода ответа 200 OK
        http_response_code(200);
    
        // Вывод товаров в формате JSON
        echo json_encode($products_arr);
    } else {
        // Установка кода ответа 404 Not Found
        http_response_code(404);
    
        // Сообщение пользователю, что товары не найдены
        echo json_encode(array("message" => "Товары не найдены."));
    }
?>