<?php
// ENCABEZADO: la parte de arriba de TODAS las páginas (el head y el menú).
// Antes de incluirlo, cada página tiene que definir $titulo y $pagina.
// $titulo = lo que sale en la pestaña del navegador
// $pagina = cuál link del menú se marca en rojo

// Cuento cuántas piezas hay en mi carrito para el numerito del menú
$cantidad_carrito = 0;
if (isset($_SESSION['id'])) {
    $consulta = mysqli_prepare($conexion, "SELECT SUM(cantidad) FROM carrito WHERE id_usuario = ?");
    mysqli_stmt_bind_param($consulta, "i", $_SESSION['id']);
    mysqli_stmt_execute($consulta);
    mysqli_stmt_bind_result($consulta, $suma);
    mysqli_stmt_fetch($consulta);
    $cantidad_carrito = (int) $suma;
    mysqli_stmt_close($consulta);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo; ?> - KEVLAR</title>
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<!-- ===== MENÚ DE ARRIBA ===== -->
<header class="barra">
    <div class="barra-contenido">

        <!-- Logo y nombre -->
        <a class="logo" href="index.php">
            <img src="img/logo.png" alt="Logo de KEVLAR">
            <span>KEV<b>LAR</b></span>
        </a>

        <!-- Links del menú: Inicio, una página por categoría, Registrar, Carrito y Pedidos.
             El de la página donde estoy lleva la clase "activo" (se marca en rojo) -->
        <nav class="menu">
            <a href="index.php"      <?php if ($pagina == 'inicio')     echo 'class="activo"'; ?>>Inicio</a>
            <a href="trajes.php"     <?php if ($pagina == 'trajes')     echo 'class="activo"'; ?>>Trajes</a>
            <a href="armas.php"      <?php if ($pagina == 'armas')      echo 'class="activo"'; ?>>Armas</a>
            <a href="accesorios.php" <?php if ($pagina == 'accesorios') echo 'class="activo"'; ?>>Accesorios</a>
            <a href="registrar.php"  <?php if ($pagina == 'registrar')  echo 'class="activo"'; ?>>Registrar</a>
            <a href="carrito.php"    <?php if ($pagina == 'carrito')    echo 'class="activo"'; ?>>Carrito<?php if ($cantidad_carrito > 0) echo '<span class="contador">' . $cantidad_carrito . '</span>'; ?></a>
            <?php if (isset($_SESSION['id'])) { ?>
                <a href="pedidos.php" <?php if ($pagina == 'pedidos') echo 'class="activo"'; ?>>Pedidos</a>
            <?php } ?>
        </nav>

        <!-- Esquina derecha: si ya entró sale "Hola, nombre" y Salir; si no, el botón rojo de Cuenta -->
        <div class="sesion">
            <?php if (isset($_SESSION['nombre'])) { ?>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a class="boton blanco" href="salir.php">Salir</a>
            <?php } else { ?>
                <a class="boton rojo" href="cuenta.php">Cuenta</a>
            <?php } ?>
        </div>

    </div>
</header>
