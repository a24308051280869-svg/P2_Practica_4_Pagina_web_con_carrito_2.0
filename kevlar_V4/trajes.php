<?php
// TRAJES: página de la categoría Trajes
include "conexion.php";
include "funciones.php";

$titulo = "Trajes";
$pagina = "trajes";
include "encabezado.php";

// Dibuja el título y los productos (la función está en funciones.php)
seccion_categoria($conexion, "Trajes", "Tra", "jes", "Trajes de cuerpo completo para cosplay, coleccionistas y fans.");

footer();
?>
</body>
</html>
