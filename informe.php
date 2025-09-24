<?php
include_once '../includes/header.php';
require_once '../includes/db_connection.php';
?>

<h1>Informes del Sistema</h1>

<h2>Reservas Futuras</h2>
<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Jugador</th>
            <th>DNI</th>
            <th>Fecha</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Cancha</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql_futuras = "SELECT r.id, j.nombre, j.apellido, j.dni, r.fecha_reserva, r.hora_inicio, r.hora_fin, r.cancha_numero
                        FROM reservas r
                        JOIN jugadores j ON r.id_jugador = j.id
                        WHERE r.fecha_reserva >= CURDATE()
                        ORDER BY r.fecha_reserva ASC, r.hora_inicio ASC";
        $result_futuras = $conn->query($sql_futuras);

        if ($result_futuras->num_rows > 0) {
            while($row = $result_futuras->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dni']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_reserva']) . "</td>";
                echo "<td>" . htmlspecialchars($row['hora_inicio']) . "</td>";
                echo "<td>" . htmlspecialchars($row['hora_fin']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cancha_numero']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay reservas futuras.</td></tr>";
        }
        ?>
    </tbody>
</table>

<hr>

<h2>Jugadores Registrados</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql_jugadores = "SELECT id, dni, nombre, apellido, telefono FROM jugadores ORDER BY apellido, nombre";
        $result_jugadores = $conn->query($sql_jugadores);

        if ($result_jugadores->num_rows > 0) {
            while($row = $result_jugadores->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dni']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
                echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hay jugadores registrados.</td></tr>";
        }
        $conn->close();
        ?>
    </tbody>
</table>

<?php
include_once '../includes/footer.php';
?>