<?php
    class Category {
        // Свойства класса для работы с базой данных и таблицей 'categories'
        private $conn;
        private $table_name = "categories";
    
        // Свойства объекта
        public $id;
        public $name;
        public $description;
        public $created_at;
        public $updated_at;
    
        // Конструктор класса
        public function __construct($db) {
            $this->conn = $db;
        }
    
        // Метод для создания категории
        public function create() {
            // Запрос на добавление записи
            $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:description";
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Очистка
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->description=htmlspecialchars(strip_tags($this->description));
    
            // Привязка значений
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
    
            // Выполнение запроса
            if ($stmt->execute()) {
                return true;
            }
    
            return false;
        }
        
        public function read() {
            $query = "SELECT id, name, img FROM categories ORDER BY name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt;
        }
        // Метод для удаления категории
        public function delete() {
            // Запрос на удаление записи
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Очистка
            $this->id=htmlspecialchars(strip_tags($this->id));
    
            // Привязка ID записи для удаления
            $stmt->bindParam(":id", $this->id);
    
            // Выполнение запроса
            if ($stmt->execute()) {
                return true;
            }
    
            return false;
        }
    }
?>