<?php
session_start();

date_default_timezone_set("Europe/Madrid");

$hora = date("H");
if ($hora < 12) {
    $saludo = "Buenos días";
} elseif ($hora < 18) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-SALUD - Ingreso</title>
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="Logo de K-SALUD">
        </div>
        <h1>K-SALUD</h1>
        <p class="saludo"><?php echo $saludo; ?>, bienvenido a la plataforma K-SALUD</p>
        <form class="login-form" action="includes/login.php" method="POST">
            <div class="formulario_cajitas">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>
            </div>
            <div class="formulario_cajitas">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>