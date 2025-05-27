<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
} catch (Exception $e) {
    die("Error al conectar con MongoDB: " . $e->getMessage());
}
