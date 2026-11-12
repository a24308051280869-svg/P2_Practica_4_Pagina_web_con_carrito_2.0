<?php
// ACCESORIOS: página de la categoría Accesorios
include "conexion.php";
include "funciones.php";

$titulo = "Accesorios";
$pagina = "accesorios";
include "encabezado.php";

// Dibuja el título y los productos (la función está en funciones.php)
seccion_categoria($conexion, "Accesorios", "Acce", "sorios", "Máscaras, guantes, botas y todo para completar tu look.");

footer();
?>
</body>
</html>
