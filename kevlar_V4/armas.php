<?php
// ARMAS: página de la categoría Armas (todas son de utilería)
include "conexion.php";
include "funciones.php";

$titulo = "Armas";
$pagina = "armas";
include "encabezado.php";

// Dibuja el título y los productos (la función está en funciones.php)
seccion_categoria($conexion, "Armas", "Ar", "mas", "Réplicas de utilería para cosplay y exhibición. Ninguna es apta para uso real.");

footer();
?>
</body>
</html>
