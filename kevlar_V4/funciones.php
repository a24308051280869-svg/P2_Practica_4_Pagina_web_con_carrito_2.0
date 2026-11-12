<?php
// FUNCIONES: cosas que se repiten en varias páginas.
// Aquí están: footer() = el pie de página, tarjeta() = la cajita de cada producto,
// seccion_categoria() = la página completa de una categoría (Trajes, Armas o Accesorios).

// ---------- FOOTER: el pie de página (logo, nombre y MME-0869) ----------
function footer() {
    echo '
    <footer class="footer">
        <div class="footer-contenido">
            <div class="logo">
                <img src="img/logo.png" alt="Logo de KEVLAR">
                <span>KEV<b>LAR</b></span>
            </div>
            <span class="folio">MME-0869</span>
        </div>
    </footer>';
}

// ---------- TARJETA: la cajita de un producto (foto, nombre, descripción, precio, stock y botón del carrito) ----------
// $p es una fila de la tabla productos (una fila = un producto)
function tarjeta($p) {
    $precio = number_format($p['precio'], 2);   // 3499 se ve como 3,499.00

    // Si hay piezas muestro cuántas, si no, "Agotado"
    if ($p['stock'] > 0) {
        $stock = '<span class="stock">' . $p['stock'] . ' disponibles</span>';
        $boton = '<button type="submit">Agregar al carrito</button>';
    } else {
        $stock = '<span class="stock agotado">Agotado</span>';
        $boton = '<button type="submit" disabled>Agotado</button>';
    }

    // htmlspecialchars: para que los textos no rompan la página
    // Si la foto no existe, se oculta y se ve el fondo rojo
    // El formulario manda el id a agregar.php para meterlo al carrito
    echo '
    <div class="producto">
        <div class="foto">
            <img src="' . htmlspecialchars($p['imagen']) . '" alt="" onerror="this.style.display=\'none\'">
            <span class="etiqueta">' . htmlspecialchars($p['categoria']) . '</span>
            <span class="heroe">' . htmlspecialchars($p['heroe']) . '</span>
        </div>
        <div class="info">
            <h3>' . htmlspecialchars($p['nombre']) . '</h3>
            <p>' . htmlspecialchars($p['descripcion']) . '</p>
            <div class="pie-producto">
                <span class="precio">$' . $precio . ' MXN</span>
                ' . $stock . '
            </div>
            <form class="agregar" method="post" action="agregar.php">
                <input type="hidden" name="id" value="' . $p['id'] . '">
                ' . $boton . '
            </form>
        </div>
    </div>';
}

// ---------- SECCION_CATEGORIA: título + todos los productos de una categoría ----------
// Lo uso en trajes.php, armas.php y accesorios.php.
// $categoria es el nombre tal cual está en la base de datos (Trajes, Armas o Accesorios)
// $titulo_a y $titulo_b son las dos partes del título (la segunda sale en rojo)
function seccion_categoria($conexion, $categoria, $titulo_a, $titulo_b, $descripcion) {
    // Título de la página
    echo '
    <section class="encabezado-pagina">
        <div class="contenido">
            <h1>' . $titulo_a . '<span class="rojo-texto">' . $titulo_b . '</span></h1>
            <p>' . htmlspecialchars($descripcion) . '</p>
        </div>
    </section>
    <section class="seccion">
        <div class="contenido">
            <div class="productos">';

    // Pido a la base de datos solo los productos de esa categoría
    $consulta = mysqli_prepare($conexion, "SELECT * FROM productos WHERE categoria = ? ORDER BY heroe, nombre");
    mysqli_stmt_bind_param($consulta, "s", $categoria);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);

    // Dibujo una tarjeta por cada producto
    while ($fila = mysqli_fetch_assoc($resultado)) {
        tarjeta($fila);
    }

    echo '
            </div>
        </div>
    </section>';
}
