<?php
require '../vendor/autoload.php';

try {
    $cliente = new MongoDB\Client("mongodb://localhost:27017");
    $baseDatos = $cliente->TFC;
} catch (Exception $e) {
    exit("Error de conexión a MongoDB: " . $e->getMessage());
}

return $baseDatos;
?>