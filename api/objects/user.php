<?php
// 'user' object
class User{
    // database connection and table name
    private $conn;
    private $table_name = "users";
    
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public $rule;
    
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
    
    // create new user record
    public function create(){
        // SQL-запрос для вставки записи
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    password = :password,
                    role = :role,
                    profile_photo = :profile_photo"; // Добавьте поле profile_photo в запрос
    
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
    
        // Очистка данных
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role)); // Очистка поля role
        $this->profile_photo = htmlspecialchars(strip_tags($this->profile_photo)); // Очистка поля profile_photo
    
        // Привязка значений
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role); // Привязка поля role
        $stmt->bindParam(':profile_photo', $this->profile_photo); // Привязка поля profile_photo
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }   
    
    // check if given email exist in the database
    function emailExists(){
        // query to check if email exists
        $query = "SELECT id, firstname, lastname, password, role, profile_photo
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num > 0){
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->password = $row['password'];
            $this->role = $row['role'];// Assign the role propertyp 
            $this->profile_photo = $row['profile_photo'];
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
    
    // update a user record
    public function update(){
        // Если пароль предоставлен, обновляем его
        $password_set = !empty($this->password) ? ", password = :password" : "";
        
        $query = "UPDATE " . $this->table_name . "
                  SET
                      firstname = :firstname,
                      lastname = :lastname,
                      profile_photo = :profile_photo
                      {$password_set}
                  WHERE id = :id";
                  
        // Подготавливаем запрос
        $stmt = $this->conn->prepare($query);
        
        // Санитизация входных данных
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->profile_photo = htmlspecialchars(strip_tags($this->profile_photo)); // Санитизация поля profile_photo
    
        // Привязываем значения
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':profile_photo', $this->profile_photo); // Привязываем фото профиля
    
        // Привязываем ID записи для редактирования
        $stmt->bindParam(':id', $this->id);
    
        // Если пароль предоставлен, хэшируем его перед сохранением в базу данных
        if(!empty($this->password)){
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
        
        // Выполняем запрос
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

}
















