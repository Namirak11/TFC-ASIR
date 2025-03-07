<?php
require '../vendor/autoload.php';

try {
    $cliente = new MongoDB\Client("mongodb://localhost:27017");
    $baseDatos = $cliente->hospital;
    echo "Conexión exitosa a MongoDB en XAMPP";
} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>