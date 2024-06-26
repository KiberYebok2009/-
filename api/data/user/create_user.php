<?php
    // required headers
    header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // database connection will be here
    // files needed to connect to database
    include_once '../../config/database.php';
    include_once '../../objects/user.php';
    
    // get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // instantiate user object
    $user = new User($db);
    
    // get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // set user property values
    $user->firstname = $data->firstname;
    $user->lastname = $data->lastname;
    $user->email = $data->email;
    $user->password = $data->password;
    $user->role = $data->role; // assuming you are sending role in the posted data
    $user->profile_photo = "api/images/server/server-error.jpg"; // set default profile photo path
    
    // create the user
    if(
        !empty($user->firstname) &&
        !empty($user->lastname) &&
        !empty($user->email) &&
        !empty($user->password) &&
        !empty($user->role) && // make sure the role is also set
        $user->create()
    ){
        // set response code
        http_response_code(200);
    
        // display message: user was created
        echo json_encode(array("message" => "User was created."));
    }
    // message if unable to create user
    else{
        // set response code
        http_response_code(400);
    
        // display message: unable to create user
        echo json_encode(array("message" => "Unable to create user."));
    }
?>