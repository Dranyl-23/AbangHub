<?php
try { 
    $pdo = new PDO("mysql:host=127.0.0.1", "root", ""); 
    $pdo->exec("CREATE DATABASE IF NOT EXISTS rentease_db"); 
    echo "DB created"; 
} catch (PDOException $e) { 
    echo $e->getMessage(); 
}
