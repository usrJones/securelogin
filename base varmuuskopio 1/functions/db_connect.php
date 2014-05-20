<?php

try {
    $dbrobots = new PDO("mysql:host=localhost;dbname=secure_login", "root", "");
} catch (PDOException $e) {
    die("Virhe: " . $e->getMessage());
}

$dbrobots->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);