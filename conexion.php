<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
$dbname = 'educacion_vial_quibdo'; // Reemplaza con el nombre de tu base de datos
$username = 'root'; // Reemplaza con tu usuario de base de datos
$password = ''; // Reemplaza con tu contraseña de base de datos

try {
  // Establecer la conexión a la base de datos utilizando PDO
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  // Configurar el modo de error de PDO para que lance excepciones
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // Manejo de errores de conexión
  die("Error de conexión: " . $e->getMessage());
}
