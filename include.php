<?php
session_start(); // Inicia la sesión en cada página protegida

// Verifica si el usuario está logueado, si no, lo redirige al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php"); // Asegúrate de la ruta correcta al login
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Cancha Fútbol 5</title>
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="welcome.php">Inicio</a></li>
            <li><a href="jugadores.php">Persona</a></li>
            <li><a href="reservas.php">Reserva</a></li>
            <li><a href="informes.php">Informes</a></li>
            <li class="logout"><a href="../logout.php">Salir</a></li>
        </ul>
    </nav>
    <div class="main-content"> ```

---

### d. Pie de Página Común (`includes/footer.php`)

```html
</div> <footer>
        <p>&copy; <?php echo date("Y"); ?> Cancha Fútbol 5. Todos los derechos reservados.</p>
    </footer>
    <script src="../js/script.js"></script> </body>
</html>