<?php
    // Подключение к базе данных и другие необходимые файлы
    include_once '../../config/database.php';
    include_once '../../objects/category.php';
    
    // Получение подключения к базе данных
    $database = new Database();
    $db = $database->getConnection();
    
    // Создание объекта Category
    $category = new Category($db);
    
    // Получение данных из запроса
    $data = json_decode(file_get_contents("php://input"));
    
    // Установка свойств категории
    $category->name = $_POST['name'];
    $category->description = $_POST['name'];
    
    // Создание категории
    if ($category->create()) {
        echo json_encode(array("message" => "Категория была создана."));
    } else {
        echo json_encode(array("message" => "Не удалось создать категорию."));
    }
?>