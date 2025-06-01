<?php
require_once 'check_session.php';
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
    $db = $client->TFC;
    $citaId = new MongoDB\BSON\ObjectId($_POST['cita_id']);

    // Eliminar la cita
    $resultado = $db->citas->deleteOne(['_id' => $citaId]);

    // Opcional: también puedes eliminar la cita del array de citas del paciente
    $db->pacientes->updateMany(
        ['citas' => $citaId],
        ['$pull' => ['citas' => $citaId]]
    );
}

// Redirigir de nuevo al dashboard
header("Location: recepciondashboard.php");
exit();
?>