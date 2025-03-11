<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$usuario = htmlspecialchars($_SESSION['user']);
$rol = htmlspecialchars($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - K-SALUD</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo $usuario; ?> 👋</h1>
        <p>Tu rol en el sistema: <strong><?php echo $rol; ?></strong></p>
        <a href="includes/logout.php" class="logout-btn">Cerrar sesión</a>
    </header>

    <main>
        <section>
            <h2>Opciones disponibles</h2>
            <ul>
                <li><a href="gestion_pacientes.php">Gestión de pacientes</a></li>
                <li><a href="gestion_citas.php">Gestión de citas</a></li>
                <li><a href="reportes.php">Reportes y estadísticas</a></li>
            </ul>
        </section>
    </main>
</body>
</html>