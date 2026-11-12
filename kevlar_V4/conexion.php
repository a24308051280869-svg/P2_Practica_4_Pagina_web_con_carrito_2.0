<?php
// CONEXIÓN: aquí me conecto a la base de datos.
// Todas las páginas incluyen este archivo al inicio.

// Esto hace que el sitio se acuerde de quién inició sesión
session_start();

// Me conecto a MySQL de XAMPP: servidor, usuario, contraseña (vacía) y nombre de la base.
// Si cambio el nombre de la base de datos, lo cambio aquí.
try {
    $conexion = mysqli_connect("localhost", "root", "", "kevlar_V4");
} catch (Exception $e) {
    // Si sale este mensaje: revisar que MySQL esté prendido y que ya importé base_de_datos.sql
    die("No me pude conectar a la base de datos. ¿Está prendido MySQL en XAMPP? ¿Ya importé base_de_datos.sql?");
}

// Esto sirve para que se vean bien los acentos y la ñ
mysqli_set_charset($conexion, "utf8mb4");
