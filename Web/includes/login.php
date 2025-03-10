<?php
session_start();
require_once "conexion.php";

echo "Login script ejecutado";

if ($usuario) {
    var_dump($password); // 🔍 Contraseña ingresada
    var_dump($usuario['password']); // 🔍 Contraseña en MongoDB (hasheada)
    var_dump(password_verify($password, $usuario['password'])); // 🔍 Verificar si devuelve true
    exit();
}
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $collection = $baseDatos->usuarios;
    $usuario = $collection->findOne(['username' => $username]);

    if ($usuario) {
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $usuario['role'];
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

if (!headers_sent()) {
    header("Location: ../index.php");
    exit();
}*/
?>
