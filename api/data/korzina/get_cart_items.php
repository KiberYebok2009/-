<?php 
    header('Content-Type: application/json');
    
    // Подключение к файлу с классом Database
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Получаем данные из тела запроса
    $data = json_decode(file_get_contents("php://input"));
    
    // Проверяем, что ID пользователя предоставлен
    if (!$data || empty($data->user_id)) {
        echo json_encode(array("error" => "Ошибка: ID пользователя не предоставлен."));
        exit;
    }
    
    $user_id = $data->user_id;
    
    // Запрос к базе данных для получения товаров в корзине пользователя
    $query = "SELECT ci.cart_item_id, ci.quantity, ci.added_at, p.id as product_id, p.name, p.price, p.path_photo, p.description
              FROM cart_items ci 
              JOIN products p ON ci.product_id = p.id 
              WHERE ci.user_id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $cart_items = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cart_items[] = $row;
    }
    
    // Вывод товаров в корзине в формате JSON
    echo json_encode($cart_items);
?>