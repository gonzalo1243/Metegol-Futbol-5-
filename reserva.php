<?php
include_once '../includes/header.php';
require_once '../includes/db_connection.php';

$message = '';

// --- Lógica para el Alta de Reservas ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_reservation') {
    $id_jugador = $_POST['id_jugador'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $cancha_numero = $_POST['cancha_numero']; // Opcional, si tienes varias canchas

    $stmt = $conn->prepare("INSERT INTO reservas (id_jugador, fecha_reserva, hora_inicio, hora_fin, cancha_numero) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $id_jugador, $fecha_reserva, $hora_inicio, $hora_fin, $cancha_numero);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Reserva agregada exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al agregar reserva: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Lógica para la Baja de Reservas ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_reservation' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Reserva eliminada exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al eliminar reserva: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Lógica para la Modificación de Reservas ---
// Similar al de jugadores, con un formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_reservation') {
    $id = $_POST['id'];
    $id_jugador = $_POST['id_jugador'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $cancha_numero = $_POST['cancha_numero'];

    $stmt = $conn->prepare("UPDATE reservas SET id_jugador=?, fecha_reserva=?, hora_inicio=?, hora_fin=?, cancha_numero=? WHERE id=?");
    $stmt->bind_param("isssii", $id_jugador, $fecha_reserva, $hora_inicio, $hora_fin, $cancha_numero, $id);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Reserva actualizada exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al actualizar reserva: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Obtener datos de una reserva para edición ---
$reserva_para_editar = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit_form' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $reserva_para_editar = $result->fetch_assoc();
    }
    $stmt->close();
}

// Obtener todos los jugadores para el SELECT del formulario
$jugadores_query = $conn->query("SELECT id, nombre, apellido, dni FROM jugadores ORDER BY apellido, nombre");
$jugadores = [];
while ($row = $jugadores_query->fetch_assoc()) {
    $jugadores[] = $row;
}

?>

<h1>Gestión de Reservas</h1>

<?php echo $message; ?>

<h2><?php echo ($reserva_para_editar ? 'Modificar' : 'Crear Nueva'); ?> Reserva</h2>
<form action="reservas.php" method="POST">
    <input type="hidden" name="action" value="<?php echo ($reserva_para_editar ? 'edit_reservation' : 'add_reservation'); ?>">
    <?php if ($reserva_para_editar): ?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($reserva_para_editar['id']); ?>">
    <?php endif; ?>

    <label for="id_jugador">Jugador:</label>
    <select id="id_jugador" name="id_jugador" required>
        <option value="">Seleccione un jugador</option>
        <?php foreach ($jugadores as $jugador): ?>
            <option value="<?php echo htmlspecialchars($jugador['id']); ?>"
                <?php echo ($reserva_para_editar && $reserva_para_editar['id_jugador'] == $jugador['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido'] . ' (DNI: ' . $jugador['dni'] . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="fecha_reserva">Fecha de Reserva:</label>
    <input type="date" id="fecha_reserva" name="fecha_reserva" value="<?php echo htmlspecialchars($reserva_para_editar['fecha_reserva'] ?? date('Y-m-d')); ?>" required><br>

    <label for="hora_inicio">Hora de Inicio:</label>
    <input type="time" id="hora_inicio" name="hora_inicio" value="<?php echo htmlspecialchars($reserva_para_editar['hora_inicio'] ?? '18:00'); ?>" required><br>

    <label for="hora_fin">Hora de Fin:</label>
    <input type="time" id="hora_fin" name="hora_fin" value="<?php echo htmlspecialchars($reserva_para_editar['hora_fin'] ?? '19:00'); ?>" required><br>

    <label for="cancha_numero">Número de Cancha:</label>
    <input type="number" id="cancha_numero" name="cancha_numero" value="<?php echo htmlspecialchars($reserva_para_editar['cancha_numero'] ?? '1'); ?>" min="1" required><br>

    <button type="submit"><?php echo ($reserva_para_editar ? 'Actualizar' : 'Crear'); ?> Reserva</button>
    <?php if ($reserva_para_editar): ?>
        <button type="button" onclick="window.location.href='reservas.php'">Cancelar Edición</button>
    <?php endif; ?>
</form>

<hr>

<h2>Listado de Reservas</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Jugador</th>
            <th>Fecha</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Cancha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Consulta para obtener reservas, incluyendo el nombre del jugador
        $sql_reservas = "SELECT r.id, j.nombre, j.apellido, r.fecha_reserva, r.hora_inicio, r.hora_fin, r.cancha_numero, r.estado
                         FROM reservas r
                         JOIN jugadores j ON r.id_jugador = j.id
                         ORDER BY r.fecha_reserva DESC, r.hora_inicio ASC";
        $result_reservas = $conn->query($sql_reservas);

        if ($result_reservas->num_rows > 0) {
            while($row = $result_reservas->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_reserva']) . "</td>";
                echo "<td>" . htmlspecialchars($row['hora_inicio']) . "</td>";
                echo "<td>" . htmlspecialchars($row['hora_fin']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cancha_numero']) . "</td>";
                echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                echo "<td>";
                echo "<a href='reservas.php?action=edit_form&id=" . htmlspecialchars($row['id']) . "'>Modificar</a> | ";
                echo "<a href='reservas.php?action=delete_reservation&id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('¿Estás seguro de eliminar esta reserva?');\">Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No hay reservas registradas.</td></tr>";
        }
        $conn->close();
        ?>
    </tbody>
</table>

<?php
include_once '../includes/footer.php';
?>