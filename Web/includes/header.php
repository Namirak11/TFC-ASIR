<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>K-SALUD - Plataforma interna</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="top-bar">
    <div class="logo-left">
        <img src="../img/logo.png" alt="Logo de K-SALUD">
    </div>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['user']) ?></strong> |
            <a href="logout.php">Cerrar sesión</a>
        </div>
    <?php endif; ?>
</div>