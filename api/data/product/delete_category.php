<?php
    // Подключение к базе данных
    include_once '../../config/database.php';
    include_once '../../objects/category.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $category = new Category($db);
    
    // Получение id категории из запроса
    $data = json_decode(file_get_contents("php://input"));
    
    if(!empty($data->id)) {
        $category->id = $data->id;
    
        // Удаление категории
        if($category->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Категория была успешно удалена."));
        } else {
            http_response_code(503); // Сервис недоступен
            echo json_encode(array("message" => "Не удалось удалить категорию."));
        }
    } else {
        http_response_code(400); // Неверный запрос
        echo json_encode(array("message" => "Не удалось удалить категорию. Не указан ID."));
    }
?>