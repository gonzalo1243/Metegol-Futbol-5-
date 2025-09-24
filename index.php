<?php
session_start(); // Inicia la sesión para guardar el estado del usuario
require_once 'includes/db_connection.php'; // Incluye el archivo de conexión a la BD

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara la consulta SQL para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT id, username, password, role FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username); // 's' indica que username es un string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifica la contraseña hasheada
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Guarda el rol del usuario

            header("Location: pages/welcome.php"); // Redirige a la página de bienvenida
            exit();
        } else {
            $error_message = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error_message = "Usuario o contraseña incorrectos.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cancha de Fútbol 5</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="index.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>