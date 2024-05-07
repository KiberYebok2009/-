<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    // Создание нового объекта Database и установка соединения
    $database = new Database();
    $db = $database->getConnection();
    
    // Получаем данные из тела запроса
    $data = json_decode(file_get_contents("php://input"));
    
    // Проверяем, что ID товаров были переданы
    if (empty($data->cart_item_ids)) {
        echo json_encode(array("error" => "Ошибка: Не переданы ID товаров."));
        exit;
    }
    
    // Проверьте, что $data->cart_item_ids действительно является массивом
    if (!is_array($data->cart_item_ids)) {
        echo json_encode(array("error" => "Ошибка: Неверный формат ID товаров."));
        exit;
    }
    
    // Преобразуем массив ID товаров в строку для запроса
    $cart_item_ids = implode(',', array_map('intval', $data->cart_item_ids));
    
    // Запрос на удаление товаров из корзины
    $query = "DELETE FROM cart_items WHERE cart_item_id IN ($cart_item_ids)";
    
    $stmt = $db->prepare($query);
    
    // Выполняем запрос
    if($stmt->execute()) {
        echo json_encode(array("message" => "Товары удалены из корзины."));
    } else {
        echo json_encode(array("error" => "Ошибка при удалении товаров из корзины."));
    }
?>