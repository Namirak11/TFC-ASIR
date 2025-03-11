<?php
session_start();
require_once "conexion.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $collection = $baseDatos->selectCollection("usuarios");
    $usuario = $collection->findOne(['username' => $username]);

    if ($usuario) {
        $passwordHash = (string)$usuario['password'];

        if (password_verify($password, $passwordHash)) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $usuario['role'];
            session_write_close();
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['error'] = "Usuario no encontrado.";
    }
} else {
    $_SESSION['error'] = "Acceso no permitido.";
}
