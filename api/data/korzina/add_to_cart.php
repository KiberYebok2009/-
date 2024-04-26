<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    

    $database = new Database();
    $db = $database->getConnection();
    
    // Получаем данные из тела запроса
    $data = json_decode(file_get_contents("php://input"));
    
    // Проверяем, что необходимые данные были переданы
    if(empty($data->user_id) || empty($data->product_id)) {
        echo json_encode(array("error" => "Ошибка: Не все данные предоставлены."));
        exit;
    }
    
    $user_id = $data->user_id;
    $product_id = $data->product_id;
    
    // Запрос на добавление товара в корзину
    $query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    
    if($stmt->execute()) {
        echo json_encode(array("message" => "Товар добавлен в корзину."));
    } else {
        echo json_encode(array("error" => "Ошибка при добавлении товара в корзину."));
    }
?>