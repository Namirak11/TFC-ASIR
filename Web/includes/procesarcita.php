<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $paciente_id = $_POST['paciente_id'] ?? '';
    $medico = $_POST['medico'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $motivo = $_POST['motivo'] ?? '';

    if (!$paciente_id || !$medico || !$fecha || !$motivo) {
        $_SESSION['error'] = "Faltan campos obligatorios.";
        header("Location: recepciondashboard.php");
        exit();
    }

    try {
        $fechaMongo = new MongoDB\BSON\UTCDateTime((new DateTime($fecha))->getTimestamp() * 1000);

        $db = $client->TFC;

        // Insertar nueva cita
        $resultado = $db->citas->insertOne([
            'paciente_id' => new MongoDB\BSON\ObjectId($paciente_id),
            'medico' => $medico,
            'fecha' => $fechaMongo,
            'motivo' => $motivo
        ]);

        // Actualizar paciente para incluir el ID de la cita
        $db->pacientes->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($paciente_id)],
            ['$push' => ['citas' => $resultado->getInsertedId()]]
        );

        $_SESSION['msg'] = "Cita agendada correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al procesar la cita: " . $e->getMessage();
    }

    header("Location: recepciondashboard.php");
    exit();
} else {
    header("Location: recepciondashboard.php");
    exit();
}
