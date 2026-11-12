<?php
// COMPRAR: pantalla final del carrito. Se elige la dirección y el método de pago
// y al confirmar se guarda el pedido en la base de datos.
// Es académico: no se cobra nada ni se piden datos de tarjeta.

include "conexion.php";
include "funciones.php";

// Para comprar hay que tener la cuenta abierta
if (!isset($_SESSION['id'])) {
    header("Location: cuenta.php?carrito=1");
    exit;
}

$usuario = $_SESSION['id'];
$errores = array();
$direccion = "";
$metodo = "Tarjeta";

// Saco de la base de datos lo que tengo en el carrito y calculo el total
$productos = array();
$total = 0;

$consulta = mysqli_prepare($conexion, "SELECT productos.*, carrito.cantidad
    FROM carrito
    JOIN productos ON productos.id = carrito.id_producto
    WHERE carrito.id_usuario = ?
    ORDER BY productos.nombre");
mysqli_stmt_bind_param($consulta, "i", $usuario);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

while ($fila = mysqli_fetch_assoc($resultado)) {
    $fila['subtotal'] = $fila['precio'] * $fila['cantidad'];
    $total += $fila['subtotal'];
    $productos[] = $fila;
}

// Si el carrito está vacío no hay nada que comprar
if (count($productos) == 0) {
    header("Location: carrito.php");
    exit;
}

// Esto solo pasa cuando le dan clic a "Confirmar pedido"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $direccion = trim($_POST['direccion']);
    $metodo = isset($_POST['metodo']) ? $_POST['metodo'] : "";

    // ----- 1. Reviso que todo esté bien -----
    if ($direccion == "") {
        $errores[] = "Escribe la dirección de envío.";
    }
    if (!in_array($metodo, array("Tarjeta", "Código promocional"))) {
        $errores[] = "Elige un método de pago.";
    }

    // ----- 2. Si todo bien, guardo el pedido -----
    if (count($errores) == 0) {
        // Uso una transacción: o se guarda todo el pedido o no se guarda nada
        mysqli_begin_transaction($conexion);

        try {
            // Guardo el pedido (quién lo hizo, dirección, método de pago y total)
            $consulta = mysqli_prepare($conexion, "INSERT INTO pedidos (id_usuario, direccion, metodo_pago, total) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($consulta, "issd", $usuario, $direccion, $metodo, $total);
            mysqli_stmt_execute($consulta);
            $id_pedido = mysqli_insert_id($conexion);

            // Guardo cada producto del pedido y le bajo el stock
            foreach ($productos as $p) {
                $consulta = mysqli_prepare($conexion, "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
                mysqli_stmt_bind_param($consulta, "iii", $p['cantidad'], $p['id'], $p['cantidad']);
                mysqli_stmt_execute($consulta);

                // Si no se pudo bajar el stock es porque ya no hay suficientes piezas
                if (mysqli_stmt_affected_rows($consulta) == 0) {
                    throw new Exception("Ya no hay suficientes piezas de " . $p['nombre'] . ". Ajusta tu carrito.");
                }

                $consulta = mysqli_prepare($conexion, "INSERT INTO pedido_productos (id_pedido, id_producto, nombre, precio, cantidad) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($consulta, "iisdi", $id_pedido, $p['id'], $p['nombre'], $p['precio'], $p['cantidad']);
                mysqli_stmt_execute($consulta);
            }

            // Vacío el carrito porque ya se compró
            $consulta = mysqli_prepare($conexion, "DELETE FROM carrito WHERE id_usuario = ?");
            mysqli_stmt_bind_param($consulta, "i", $usuario);
            mysqli_stmt_execute($consulta);

            mysqli_commit($conexion);

            // Lo mando a ver sus pedidos
            header("Location: pedidos.php?listo=1");
            exit;
        } catch (Exception $e) {
            // Si algo falló deshago todo
            mysqli_rollback($conexion);
            $errores[] = $e->getMessage();
        }
    }
}

$titulo = "Comprar";
$pagina = "carrito";
include "encabezado.php";
?>

<section class="encabezado-pagina">
    <div class="contenido">
        <h1>Finalizar <span class="rojo-texto">compra</span></h1>
        <p>Elige tu dirección y el método de pago. Es una compra de prueba, no se cobra nada.</p>
    </div>
</section>

<section class="seccion">
    <div class="contenido">

        <!-- Los errores salen aquí, en rojo -->
        <?php foreach ($errores as $error) { ?>
            <div class="aviso error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <div class="compra">

            <!-- Resumen de lo que voy a comprar -->
            <div class="compra-resumen">
                <h3>Tu pedido</h3>
                <?php foreach ($productos as $p) { ?>
                    <div class="resumen-fila">
                        <span><?php echo $p['cantidad']; ?> x <?php echo htmlspecialchars($p['nombre']); ?></span>
                        <span>$<?php echo number_format($p['subtotal'], 2); ?></span>
                    </div>
                <?php } ?>
                <div class="resumen-total">
                    <span>Total</span>
                    <b>$<?php echo number_format($total, 2); ?> MXN</b>
                </div>
            </div>

            <!-- Dirección y método de pago -->
            <form class="compra-form" method="post" action="comprar.php">
                <h3>Envío y pago</h3>

                <label class="titulo-campo" for="direccion">Dirección de envío</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>" required>

                <span class="titulo-campo">Método de pago</span>
                <label class="opcion-pago">
                    <input type="radio" name="metodo" value="Tarjeta" <?php if ($metodo == "Tarjeta") echo "checked"; ?>>
                    Tarjeta
                </label>
                <label class="opcion-pago">
                    <input type="radio" name="metodo" value="Código promocional" <?php if ($metodo == "Código promocional") echo "checked"; ?>>
                    Código promocional
                </label>

                <button class="boton rojo grande completo" type="submit">Confirmar pedido</button>
                <a class="boton blanco grande completo" href="carrito.php">Volver al carrito</a>
            </form>

        </div>

    </div>
</section>

<?php footer(); ?>
</body>
</html>
