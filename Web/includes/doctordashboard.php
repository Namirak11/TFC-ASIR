<?php
require_once 'check_session.php';
require_once 'conexion.php';
require_once 'header.php';


$db = $client->TFC;
$medico = $_SESSION['user'];

// ACTUALIZAR HISTORIAL
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['paciente_id'])) {
    $id = new MongoDB\BSON\ObjectId($_POST['paciente_id']);
    $db->pacientes->updateOne(
        ['_id' => $id],
        ['$set' => [
            'historial_clinico' => [
                'alergias' => array_map('trim', explode(',', $_POST['alergias'] ?? '')),
                'enfermedades_anteriores' => array_map('trim', explode(',', $_POST['enfermedades'] ?? '')),
                'medicacion_actual' => array_map('trim', explode(',', $_POST['medicacion'] ?? '')),
                'notas' => $_POST['notas'] ?? ''
            ]
        ]]
    );
    echo "<p style='color:green;'>✔ Historial clínico actualizado.</p>";
}

echo "<h2>Panel del Médico: $medico</h2>";

// MOSTRAR CITAS PRÓXIMAS
echo "<h3>Próximas citas</h3>";
$citas = $db->citas->find(['medico' => $medico], ['sort' => ['fecha' => 1]]);

echo "<table border='1' cellpadding='8'>
        <tr><th>Fecha</th><th>Paciente</th><th>Motivo</th></tr>";

foreach ($citas as $cita) {
    $paciente = $db->pacientes->findOne(['_id' => $cita['paciente_id'] ?? null]);
    $nombre = $paciente ? $paciente['nombre'] . ' ' . $paciente['apellidos'] : 'Paciente eliminado';
    $fecha = isset($cita['fecha']) && $cita['fecha'] instanceof MongoDB\BSON\UTCDateTime
        ? $cita['fecha']->toDateTime()->format('Y-m-d H:i')
        : 'Fecha no disponible';
    $motivo = $cita['motivo'] ?? '---';

    echo "<tr>
            <td>$fecha</td>
            <td>$nombre</td>
            <td>$motivo</td>
          </tr>";
}
echo "</table>";

// FORMULARIO DE BÚSQUEDA DE PACIENTE
?>

<h3>Buscar paciente</h3>
<form method="GET" action="doctordashboard.php">
    <label>Buscar por nombre, apellidos o DNI:</label><br>
    <input type="text" name="filtro" value="<?= htmlspecialchars($_GET['filtro'] ?? '') ?>" placeholder="Ej. María, López, 12345678X">
    <input type="submit" value="Buscar"><br><br>

    <label>Selecciona un paciente:</label><br>
    <select name="paciente_id">
        <option value="">-- Selecciona --</option>
        <?php
        $filtroTexto = $_GET['filtro'] ?? '';
        $pacientesFiltrados = $db->pacientes->find([
            '$and' => [
                ['citas' => ['$exists' => true, '$not' => ['$size' => 0]]],
                [
                    '$or' => [
                        ['nombre' => new MongoDB\BSON\Regex($filtroTexto, 'i')],
                        ['apellidos' => new MongoDB\BSON\Regex($filtroTexto, 'i')],
                        ['dni' => new MongoDB\BSON\Regex($filtroTexto, 'i')]
                    ]
                ]
            ]
        ]);

        foreach ($pacientesFiltrados as $paciente) {
            foreach ($paciente['citas'] as $citaId) {
                $cita = $db->citas->findOne([
                    '_id' => new MongoDB\BSON\ObjectId($citaId),
                    'medico' => $medico
                ]);
                if ($cita) {
                    $selected = (isset($_GET['paciente_id']) && $_GET['paciente_id'] == (string)$paciente['_id']) ? "selected" : "";
                    echo "<option value='{$paciente['_id']}' $selected>{$paciente['nombre']} {$paciente['apellidos']} (DNI: {$paciente['dni']})</option>";
                    break;
                }
            }
        }
        ?>
    </select>
    <input type="submit" value="Ver historial">
</form>

<?php
// MOSTRAR HISTORIAL CLÍNICO
if (!empty($_GET['paciente_id'])) {
    $paciente_id = new MongoDB\BSON\ObjectId($_GET['paciente_id']);
    $p = $db->pacientes->findOne(['_id' => $paciente_id]);

    if ($p) {
        echo "<h4>{$p['nombre']} {$p['apellidos']} (DNI: {$p['dni']})</h4>";
        echo "<ul>";
        echo "<li>Fecha de nacimiento: {$p['fecha_nacimiento']}</li>";
        echo "<li>Dirección: {$p['direccion']}</li>";
        echo "<li>Alergias: " . implode(", ", (array)($p['historial_clinico']['alergias'] ?? [])) . "</li>";
        echo "<li>Enfermedades anteriores: " . implode(", ", (array)($p['historial_clinico']['enfermedades_anteriores'] ?? [])) . "</li>";
        echo "<li>Medicación actual: " . implode(", ", (array)($p['historial_clinico']['medicacion_actual'] ?? [])) . "</li>";
        echo "<li>Notas: " . ($p['historial_clinico']['notas'] ?? 'Sin notas') . "</li>";
        echo "</ul>";
        ?>

        <form method="POST" action="doctordashboard.php">
            <input type="hidden" name="paciente_id" value="<?= $p['_id'] ?>">

            <label>Alergias (coma separadas):</label><br>
            <input type="text" name="alergias" value="<?= htmlspecialchars(implode(', ', (array)($p['historial_clinico']['alergias'] ?? []))) ?>"><br>

            <label>Enfermedades anteriores:</label><br>
            <input type="text" name="enfermedades" value="<?= htmlspecialchars(implode(', ', (array)($p['historial_clinico']['enfermedades_anteriores'] ?? []))) ?>"><br>

            <label>Medicación actual:</label><br>
            <input type="text" name="medicacion" value="<?= htmlspecialchars(implode(', ', (array)($p['historial_clinico']['medicacion_actual'] ?? []))) ?>"><br>

            <label>Notas:</label><br>
            <textarea name="notas" rows="3" cols="50"><?= htmlspecialchars($p['historial_clinico']['notas'] ?? '') ?></textarea><br><br>

            <input type="submit" value="Actualizar historial">
        </form>
        <?php
    } else {
        echo "<p style='color:red;'>Paciente no encontrado.</p>";
    }

} elseif (!empty($_GET['filtro'])) {
    echo "<h4>Resultados para \"{$_GET['filtro']}\"</h4>";

    $pacientesCoinciden = $db->pacientes->find([
        '$and' => [
            ['citas' => ['$exists' => true, '$not' => ['$size' => 0]]],
            [
                '$or' => [
                    ['nombre' => new MongoDB\BSON\Regex($_GET['filtro'], 'i')],
                    ['apellidos' => new MongoDB\BSON\Regex($_GET['filtro'], 'i')],
                    ['dni' => new MongoDB\BSON\Regex($_GET['filtro'], 'i')]
                ]
            ]
        ]
    ]);

    $encontrados = false;
    foreach ($pacientesCoinciden as $p) {
        foreach ($p['citas'] as $citaId) {
            $cita = $db->citas->findOne([
                '_id' => new MongoDB\BSON\ObjectId($citaId),
                'medico' => $medico
            ]);
            if ($cita) {
                echo "<p>{$p['nombre']} {$p['apellidos']} (DNI: {$p['dni']}) — 
                <a href='doctordashboard.php?filtro=" . urlencode($_GET['filtro']) . "&paciente_id={$p['_id']}'>Ver historial</a></p>";
                $encontrados = true;
                break;
            }
        }
    }

    if (!$encontrados) {
        echo "<p style='color:red;'>No se encontró ningún paciente con ese nombre, apellido o DNI.</p>";
    }
}
?>

</body>
</html>

<?php
require_once 'footer.php';
?>