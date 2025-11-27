<?php
// ================================================
// CONFIGURACIÓN DE CONEXIÓN A BASE DE DATOS
// ================================================

// Ajusta aquí tu conexión según tu servidor
$host     = "localhost";
$usuario  = "root";          // o el usuario que uses
$password = "";              // tu clave si tienes una
$basededatos = "SistemaCapacitaciones";  // nombre exacto de tu BD

$mysqli = new mysqli($host, $usuario, $password, $basededatos);

// Verificar conexión
if ($mysqli->connect_errno) {
    die("Error de conexión a la base de datos: " . $mysqli->connect_error);
}

// Forzar UTF-8 (evita problemas con tildes)
$mysqli->set_charset("utf8mb4");
?>
