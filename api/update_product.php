<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    //header("Content-Type: multipart/form-data; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // files for decoding jwt
    require "libs/vendor/autoload.php";
    include_once 'config/core.php';
    include_once 'libs/vendor/firebase/php-jwt/src/BeforeValidException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/ExpiredException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/JWT.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    // database connection
    include_once 'config/database.php';
    include_once 'objects/product.php';
    
    
?>