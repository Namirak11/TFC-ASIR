<?php
require __DIR__ . '/../vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->TFC->usuarios;

$nuevoPasswordPlano = "admin"; // Cambia esto si quieres
$nuevoHash = password_hash($nuevoPasswordPlano, PASSWORD_DEFAULT);

$resultado = $collection->updateOne(
    ['username' => 'admin1'],
    ['$set' => ['password' => $nuevoHash]]
);

if ($resultado->getModifiedCount() > 0) {
    echo "✅ Contraseña actualizada correctamente.";
} else {
    echo "❌ No se actualizó la contraseña (¿usuario incorrecto o ya tenía ese hash?).";
}
?>