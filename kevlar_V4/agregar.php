<?php
// AGREGAR: mete un producto al carrito de la cuenta y regresa a la página donde estaba.
// No dibuja nada, solo guarda en la base de datos y redirige.

include "conexion.php";

// Para usar el carrito hay que tener la cuenta abierta
if (!isset($_SESSION['id'])) {
    header("Location: cuenta.php?carrito=1");
    exit;
}

// Esto solo pasa cuando le dan clic a "Agregar al carrito"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_SESSION['id'];
    $id = (int) $_POST['id'];

    // Busco el producto para saber cuánto stock tiene
    $consulta = mysqli_prepare($conexion, "SELECT stock FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($consulta, "i", $id);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $producto = mysqli_fetch_assoc($resultado);

    if ($producto && $producto['stock'] > 0) {
        $stock = $producto['stock'];

        // Si no estaba en el carrito lo crea con 1.
        // Si ya estaba, le suma 1 pero solo si no se pasa del stock.
        $consulta = mysqli_prepare($conexion, "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE cantidad = IF(cantidad < ?, cantidad + 1, cantidad)");
        mysqli_stmt_bind_param($consulta, "iii", $usuario, $id, $stock);
        mysqli_stmt_execute($consulta);
    }
}

// Regreso a la página de donde vino (si no sé cuál, al inicio)
$regreso = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "index.php";
header("Location: " . $regreso);
exit;
