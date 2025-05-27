<?php
require_once 'check_session.php';
require_once 'conexion.php';
require_once 'header.php';

$db = $client->TFC;
$usuariosCol = $db->usuarios;

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['role'] ?? '';

    if ($username && $password && $rol) {
        $existe = $usuariosCol->findOne(['username' => $username]);
        if (!$existe) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $usuariosCol->insertOne([
                'username' => $username,
                'password' => $hash,
                'role' => $rol
            ]);
            echo "<p style='color:green;'>✔ Usuario creado correctamente.</p>";
        } else {
            echo "<p style='color:red;'>El nombre de usuario ya existe.</p>";
        }
    } else {
        echo "<p style='color:red;'>Faltan campos obligatorios.</p>";
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $usuarioEliminar = $_GET['eliminar'];
    if ($usuarioEliminar !== $_SESSION['user']) {
        $usuariosCol->deleteOne(['username' => $usuarioEliminar]);
        echo "<p style='color:green;'>✔ Usuario '$usuarioEliminar' eliminado.</p>";
    } else {
        echo "<p style='color:red;'>No puedes eliminar tu propio usuario.</p>";
    }
}
?>

<h2>Gestión de usuarios</h2>

<table border="1" cellpadding="8">
    <tr><th>Usuario</th><th>Rol</th><th>Acciones</th></tr>
    <?php
    $usuarios = $usuariosCol->find();
    foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td>
                <?php if ($u['username'] !== $_SESSION['user']): ?>
                    <a href="admindashboard.php?eliminar=<?= $u['username'] ?>" onclick="return confirm('¿Eliminar al usuario <?= $u['username'] ?>?')">Eliminar</a>
                <?php else: ?>
                    <em>(tú mismo)</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Crear nuevo usuario</h3>

<form method="POST" action="admindashboard.php">
    <input type="hidden" name="nuevo_usuario" value="1">

    <label>Nombre de usuario:</label>
    <input type="text" name="username" required>

    <label>Contraseña:</label>
    <input type="password" name="password" required>

    <label>Rol:</label>
    <select name="role" required>
        <option value="">-- Selecciona un rol --</option>
        <option value="admin">admin</option>
        <option value="doctor">doctor</option>
        <option value="recepcion">recepcion</option>
    </select>

    <input type="submit" value="Crear usuario">
</form>

<?php require_once 'footer.php'; ?>