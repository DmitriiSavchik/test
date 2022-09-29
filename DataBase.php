<?php
/**
 * Автор: Дмитрий Савчик
 *
 * Дата реализации: 28.09.2022 12:00
 *
 * Дата изменения: 29.09.2022 15:00
 *
 * Подключение и создание базы данных
 */
try {
    $connect = new PDO("mysql:host=localhost", "root", "");

    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE DATABASE IF NOT EXISTS UserTest;
            USE UserTest;
            CREATE TABLE IF NOT EXISTS users (
            id INTEGER auto_increment primary key, 
            firstname VARCHAR(30), 
            lastname VARCHAR(30),
            birthdate DATE,
            gender BIT, 
            city VARCHAR(120));";

    $connect->exec($sql);
    echo "Database has been created";
} catch (PDOException $errors) {
    echo "Database error: " . $errors->getMessage();
}

?>

