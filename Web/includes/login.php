<?php
session_start();
require_once 'conexion.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $collection = $client->TFC->usuarios;
    $usuario = $collection->findOne(['username' => $username]);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $usuario['role'];
        session_write_close();

        // Redirección directa según el rol (nombres únicos)
        $destinos = [
            'admin' => 'admindashboard.php',
            'doctor' => 'doctordashboard.php',
            'recepcion' => 'recepciondashboard.php'
        ];

        $rol = $usuario['role'];
        if (array_key_exists($rol, $destinos)) {
            header("Location:" . $destinos[$rol]);
            exit();
        } else {
            $_SESSION['error'] = "Rol no reconocido.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
