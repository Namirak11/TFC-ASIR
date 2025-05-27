<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - K-SALUD</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="logo-container">
    <img src="img/logo.png" alt="Logo K-SALUD">
</div>

<h2 class="login-title">Iniciar sesión en K-SALUD</h2>

<?php if ($error): ?>
    <div class="login-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="includes/login.php" class="login-form">
    <label for="username">Usuario:</label>
    <input type="text" name="username" id="username" required>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>

    <input type="submit" value="Iniciar sesión">
</form>

<?php require_once 'includes/footer.php'; ?>