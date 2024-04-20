<?php
class Product {
    // Подключение к базе данных и имя таблицы
    private $conn;
    private $table_name = "products";

    // Свойства объекта
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $created;
    public $path_photo; // Добавлено новое свойство для пути к фото продукта

    // Конструктор для соединения с базой данных
    public function __construct($db) {
        $this->conn = $db;
    }

    // Метод для чтения всех продуктов
    public function getProducts() {
        $query = "SELECT id, name, description, price, path_photo FROM " . $this->table_name . " ORDER BY created DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($products, $row);
        }
        
        return $products;
    }

    // Метод для создания продукта
    function create() {
        // Запрос SQL для вставки записи
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name=:name, description=:description, price=:price, category_id=:category_id, created=:created, path_photo=:path_photo";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Очистка и привязка данных
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->created = htmlspecialchars(strip_tags($this->created));
        $this->path_photo = htmlspecialchars(strip_tags($this->path_photo)); // Очистка нового поля path_photo

        // Привязка значений
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":created", $this->created);
        $stmt->bindParam(":path_photo", $this->path_photo); // Привязка нового поля path_photo

        // Выполнение запроса
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Методы update и delete могут быть реализованы аналогично, включая обработку поля path_photo
    public function update_product() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                      name = :name,
                      description = :description,
                      price = :price,
                      path_photo = :path_photo,
                      category_id = :category_id
                  WHERE
                      id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        // Привязка значений
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':path_photo', $this->path_photo);
        $stmt->bindParam(':category_id', $this->category_id);
    
        // Выполнение запроса
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    // Другие методы...
}
?>