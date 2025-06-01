<?php
require_once 'check_session.php';
require_once 'conexion.php';
require_once 'header.php';

$db = $client->TFC;
$semanaActual = isset($_GET['semana']) ? (int)$_GET['semana'] : 0;

// Mostrar todas las citas o solo las de la semana
if (isset($_GET['ver']) && $_GET['ver'] === 'todo') {
    echo "<h2>Todas las citas registradas</h2>";
    $citas = $db->citas->find([], ['sort' => ['fecha' => 1]]);
} else {
    echo "<h2>Citas de la semana</h2>";
    $lunes = strtotime("monday this week +{$semanaActual} week");
    $domingo = strtotime("sunday this week +{$semanaActual} week");

    $fechaInicio = new MongoDB\BSON\UTCDateTime($lunes * 1000);
    $fechaFin = new MongoDB\BSON\UTCDateTime(($domingo + 86399) * 1000);

    $citas = $db->citas->find([
        'fecha' => ['$gte' => $fechaInicio, '$lte' => $fechaFin]
    ], ['sort' => ['fecha' => 1]]);
}

// Navegación entre semanas
echo "<div style='margin-bottom: 10px;'>
        <a href='recepciondashboard.php?semana=" . ($semanaActual - 1) . "'>⬅ Semana anterior</a> |
        <a href='recepciondashboard.php?semana=0'>Semana actual</a> |
        <a href='recepciondashboard.php?semana=" . ($semanaActual + 1) . "'>Semana siguiente ➡</a> |
        <a href='recepciondashboard.php?ver=todo'>Ver todas las citas</a>
      </div>";

// Tabla de citas
echo "<table border='1' cellpadding='8'>
        <tr><th>Fecha</th><th>Paciente</th><th>Médico</th><th>Motivo</th><th>Eliminar</th></tr>";

foreach ($citas as $cita) {
    $paciente = $db->pacientes->findOne(['_id' => $cita['paciente_id'] ?? null]);

    $nombreCompleto = $paciente ? $paciente['nombre'] . ' ' . $paciente['apellidos'] : 'Paciente eliminado';
    $dni = $paciente['dni'] ?? '---';
    $fecha = isset($cita['fecha']) && $cita['fecha'] instanceof MongoDB\BSON\UTCDateTime
        ? $cita['fecha']->toDateTime()->format('Y-m-d H:i')
        : 'Fecha no disponible';

    echo "<tr>
        <td>$fecha</td>
        <td>$nombreCompleto (DNI: $dni)</td>
        <td>" . ($cita['medico'] ?? '---') . "</td>
        <td>" . ($cita['motivo'] ?? '---') . "</td>
        <td>
            <form method='POST' action='eliminar_cita.php' class='formulario-enlinea' onsubmit='return confirm(\"¿Estás seguro de que quieres eliminar esta cita?\");'>
                <input type='hidden' name='cita_id' value='" . $cita['_id'] . "'>
                <input type='submit' value='Eliminar' class='boton-eliminar'>
            </form>
        </td>
      </tr>";

}
echo "</table>";
?>

<h2>Agendar nueva cita</h2>
<form method="POST" action="procesarcita.php">
    <label>Paciente:</label>
    <select name="paciente_id" required>
        <?php
        $pacientes = $db->pacientes->find();
        foreach ($pacientes as $p): ?>
            <option value="<?= $p['_id'] ?>">
                <?= $p['nombre'] . ' ' . $p['apellidos'] ?> (DNI: <?= $p['dni'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Médico:</label>
    <select name="medico" required>
        <?php
        $doctores = $db->usuarios->find(['role' => 'doctor']);
        foreach ($doctores as $d): ?>
            <option value="<?= $d['username'] ?>"><?= $d['username'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Fecha y hora:</label>
    <input type="datetime-local" name="fecha" required><br><br>

    <label>Motivo:</label>
    <input type="text" name="motivo" required><br><br>

    <input type="submit" value="Agendar cita">
</form>

<h2>Buscar citas por paciente</h2>
<form method="GET" action="recepciondashboard.php">
    <label>Selecciona un paciente:</label>
    <select name="buscar_paciente" required>
        <?php
        $pacientes_cursor = $db->pacientes->find();
        foreach ($pacientes_cursor as $p): ?>
            <option value="<?= $p['_id'] ?>">
                <?= $p['nombre'] . ' ' . $p['apellidos'] ?> (DNI: <?= $p['dni'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Buscar">
</form>

<?php
if (isset($_GET['buscar_paciente'])) {
    $paciente_id = $_GET['buscar_paciente'];
    $paciente = $db->pacientes->findOne(['_id' => new MongoDB\BSON\ObjectId($paciente_id)]);

    if ($paciente) {
        echo "<h3>Citas de {$paciente['nombre']} {$paciente['apellidos']} (DNI: {$paciente['dni']})</h3>";

        $citasPaciente = $db->citas->find(
            ['paciente_id' => $paciente['_id']],
            ['sort' => ['fecha' => 1]]
        );

        echo "<table border='1' cellpadding='8'>
                <tr><th>Fecha</th><th>Médico</th><th>Motivo</th></tr>";

        foreach ($citasPaciente as $cita) {
            $fecha = isset($cita['fecha']) && $cita['fecha'] instanceof MongoDB\BSON\UTCDateTime
                ? $cita['fecha']->toDateTime()->format('Y-m-d H:i')
                : 'Fecha no disponible';

            echo "<tr>
                    <td>$fecha</td>
                    <td>" . ($cita['medico'] ?? '---') . "</td>
                    <td>" . ($cita['motivo'] ?? '---') . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>Paciente no encontrado.</p>";
    }
}
?>

<h2>Buscar citas por texto (nombre, apellidos o DNI)</h2>
<form method="GET" action="recepciondashboard.php">
    <input type="text" name="texto_busqueda" placeholder="Ej. Juan, Pérez, 12345678A" required>
    <input type="submit" value="Buscar">
</form>

<?php
if (isset($_GET['texto_busqueda'])) {
    $texto = $_GET['texto_busqueda'];

    $pacientesEncontrados = $db->pacientes->find([
        '$or' => [
            ['nombre' => new MongoDB\BSON\Regex($texto, 'i')],
            ['apellidos' => new MongoDB\BSON\Regex($texto, 'i')],
            ['dni' => new MongoDB\BSON\Regex($texto, 'i')]
        ]
    ]);

    foreach ($pacientesEncontrados as $paciente) {
        echo "<h3>Citas de {$paciente['nombre']} {$paciente['apellidos']} (DNI: {$paciente['dni']})</h3>";

        $citasPaciente = $db->citas->find(
            ['paciente_id' => $paciente['_id']],
            ['sort' => ['fecha' => 1]]
        );

        echo "<table border='1' cellpadding='8'>
                <tr><th>Fecha</th><th>Médico</th><th>Motivo</th></tr>";

        foreach ($citasPaciente as $cita) {
            $fecha = isset($cita['fecha']) && $cita['fecha'] instanceof MongoDB\BSON\UTCDateTime
                ? $cita['fecha']->toDateTime()->format('Y-m-d H:i')
                : 'Fecha no disponible';

            echo "<tr>
                    <td>$fecha</td>
                    <td>" . ($cita['medico'] ?? '---') . "</td>
                    <td>" . ($cita['motivo'] ?? '---') . "</td>
                  </tr>";
        }
        echo "</table><br>";
    }
}
?>

</body>
</html>

<?php
require_once 'footer.php';
?>