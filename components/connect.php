<?php
$db_name = 'mysql:host=localhost;dbname=blog_db';
$user_name = 'root';
$user_password = 'admin';

try {
    $conn = new PDO($db_name, $user_name, $user_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    echo "Erreur de connexion a la BDD : " . $e->getMessage();
    die();
}
