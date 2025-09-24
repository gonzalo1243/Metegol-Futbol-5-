<?php
include_once '../includes/header.php'; // Incluye el encabezado
?>
<h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p>Desde aquí puedes gestionar jugadores, reservas e informes.</p>
<?php
include_once '../includes/footer.php'; // Incluye el pie de página
?>