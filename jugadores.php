<?php
include_once '../includes/header.php';
require_once '../includes/db_connection.php';

$message = ''; // Para mostrar mensajes al usuario

// --- Lógica para el Alta de Jugadores ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_player') {
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("INSERT INTO jugadores (dni, nombre, apellido, direccion, telefono) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $dni, $nombre, $apellido, $direccion, $telefono);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Jugador agregado exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al agregar jugador: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Lógica para la Baja de Jugadores ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_player' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM jugadores WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' indica que id es un entero

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Jugador eliminado exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al eliminar jugador: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Lógica para la Modificación de Jugadores (actualizar) ---
// Normalmente esto se haría con un formulario pre-rellenado o un modal.
// Para este ejemplo, haremos un formulario de edición que envía a la misma página.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_player') {
    $id = $_POST['id'];
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("UPDATE jugadores SET dni=?, nombre=?, apellido=?, direccion=?, telefono=? WHERE id=?");
    $stmt->bind_param("sssssi", $dni, $nombre, $apellido, $direccion, $telefono, $id);

    if ($stmt->execute()) {
        $message = "<p class='success-message'>Jugador actualizado exitosamente.</p>";
    } else {
        $message = "<p class='error-message'>Error al actualizar jugador: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// --- Obtener datos de un jugador para edición (si se pasa un ID) ---
$jugador_para_editar = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit_form' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM jugadores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $jugador_para_editar = $result->fetch_assoc();
    }
    $stmt->close();
}

?>

<h1>Gestión de Jugadores</h1>

<?php echo $message; // Muestra mensajes de éxito/error ?>

<h2><?php echo ($jugador_para_editar ? 'Modificar' : 'Registrar Nuevo'); ?> Jugador</h2>
<form action="jugadores.php" method="POST">
    <input type="hidden" name="action" value="<?php echo ($jugador_para_editar ? 'edit_player' : 'add_player'); ?>">
    <?php if ($jugador_para_editar): ?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($jugador_para_editar['id']); ?>">
    <?php endif; ?>

    <label for="dni">DNI:</label>
    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($jugador_para_editar['dni'] ?? ''); ?>" required><br>

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($jugador_para_editar['nombre'] ?? ''); ?>" required><br>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($jugador_para_editar['apellido'] ?? ''); ?>" required><br>

    <label for="direccion">Dirección:</label>
    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($jugador_para_editar['direccion'] ?? ''); ?>"><br>

    <label for="telefono">Teléfono:</label>
    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($jugador_para_editar['telefono'] ?? ''); ?>" required><br>

    <button type="submit"><?php echo ($jugador_para_editar ? 'Actualizar' : 'Agregar'); ?> Jugador</button>
    <?php if ($jugador_para_editar): ?>
        <button type="button" onclick="window.location.href='jugadores.php'">Cancelar Edición</button>
    <?php endif; ?>
</form>

<hr>

<h2>Listado de Jugadores</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT * FROM jugadores ORDER BY apellido, nombre");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dni']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
                echo "<td>" . htmlspecialchars($row['direccion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
                echo "<td>";
                echo "<a href='jugadores.php?action=edit_form&id=" . htmlspecialchars($row['id']) . "'>Modificar</a> | ";
                // Con confirm() para pedir confirmación antes de eliminar
                echo "<a href='jugadores.php?action=delete_player&id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('¿Estás seguro de eliminar a este jugador?');\">Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay jugadores registrados.</td></tr>";
        }
        $conn->close();
        ?>
    </tbody>
</table>

<?php
include_once '../includes/footer.php';
?>