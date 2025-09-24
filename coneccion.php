<?php
$servername = "localhost";
$username = "root"; // Usuario por defecto de XAMPP/WAMP
$password = "";     // Contraseña por defecto de XAMPP/WAMP (vacía)
$dbname = "futbol5_db"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// echo "Conexión exitosa"; // Descomenta para probar la conexión
?>