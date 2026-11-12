<?php
// CARRITO: muestra lo que agregué, deja cambiar cantidades, quitar y vaciar.
// Todo se guarda en la tabla "carrito" de la base de datos, ligado a mi cuenta.
// Desde aquí se pasa a comprar.php para hacer el pedido.

include "conexion.php";
include "funciones.php";

// Para ver el carrito hay que tener la cuenta abierta
if (!isset($_SESSION['id'])) {
    header("Location: cuenta.php?carrito=1");
    exit;
}

$usuario = $_SESSION['id'];

// Esto pasa cuando le dan clic a algún botón del carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($accion == 'sumar') {
        // Le sumo 1 solo si todavía hay stock disponible
        $consulta = mysqli_prepare($conexion, "UPDATE carrito SET cantidad = cantidad + 1
            WHERE id_usuario = ? AND id_producto = ?
            AND cantidad < (SELECT stock FROM productos WHERE id = ?)");
        mysqli_stmt_bind_param($consulta, "iii", $usuario, $id, $id);
        mysqli_stmt_execute($consulta);
    }

    if ($accion == 'restar') {
        $consulta = mysqli_prepare($conexion, "UPDATE carrito SET cantidad = cantidad - 1 WHERE id_usuario = ? AND id_producto = ?");
        mysqli_stmt_bind_param($consulta, "ii", $usuario, $id);
        mysqli_stmt_execute($consulta);

        // Si llegó a 0 lo quito del carrito
        $consulta = mysqli_prepare($conexion, "DELETE FROM carrito WHERE id_usuario = ? AND cantidad <= 0");
        mysqli_stmt_bind_param($consulta, "i", $usuario);
        mysqli_stmt_execute($consulta);
    }

    if ($accion == 'quitar') {
        $consulta = mysqli_prepare($conexion, "DELETE FROM carrito WHERE id_usuario = ? AND id_producto = ?");
        mysqli_stmt_bind_param($consulta, "ii", $usuario, $id);
        mysqli_stmt_execute($consulta);
    }

    if ($accion == 'vaciar') {
        $consulta = mysqli_prepare($conexion, "DELETE FROM carrito WHERE id_usuario = ?");
        mysqli_stmt_bind_param($consulta, "i", $usuario);
        mysqli_stmt_execute($consulta);
    }

    // Recargo la página para que no se repita la acción al actualizar
    header("Location: carrito.php");
    exit;
}

$titulo = "Carrito";
$pagina = "carrito";
include "encabezado.php";

// Saco de la base de datos los productos que tengo en mi carrito
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
?>

<section class="encabezado-pagina">
    <div class="contenido">
        <h1>Mi <span class="rojo-texto">carrito</span></h1>
        <p>Revisa lo que agregaste antes de seguir viendo la tienda.</p>
    </div>
</section>

<section class="seccion">
    <div class="contenido">

        <?php if (count($productos) == 0) { ?>
            <!-- Carrito vacío -->
            <div class="carrito-vacio">
                <p>Tu carrito está vacío.</p>
                <a class="boton rojo grande" href="trajes.php">Ver productos</a>
            </div>

        <?php } else { ?>
            <!-- Una fila por cada producto del carrito -->
            <div class="carrito-lista">
                <?php foreach ($productos as $p) { ?>
                    <div class="carrito-fila">
                        <img src="<?php echo htmlspecialchars($p['imagen']); ?>" alt="" onerror="this.style.display='none'">

                        <div class="carrito-datos">
                            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            <span class="gris"><?php echo htmlspecialchars($p['categoria']); ?> · <?php echo htmlspecialchars($p['heroe']); ?></span>
                            <span class="precio">$<?php echo number_format($p['precio'], 2); ?> MXN</span>
                        </div>

                        <!-- Botones - y + para cambiar la cantidad -->
                        <div class="carrito-cantidad">
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="accion" value="restar">
                                <button type="submit">-</button>
                            </form>
                            <span><?php echo $p['cantidad']; ?></span>
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="accion" value="sumar">
                                <button type="submit">+</button>
                            </form>
                        </div>

                        <span class="carrito-subtotal">$<?php echo number_format($p['subtotal'], 2); ?></span>

                        <form method="post" action="carrito.php">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                            <input type="hidden" name="accion" value="quitar">
                            <button class="boton blanco" type="submit">Quitar</button>
                        </form>
                    </div>
                <?php } ?>
            </div>

            <!-- Total y botón de vaciar -->
            <div class="carrito-total">
                <form method="post" action="carrito.php">
                    <input type="hidden" name="accion" value="vaciar">
                    <button class="boton blanco" type="submit">Vaciar carrito</button>
                </form>
                <div class="total-comprar">
                    <div class="total">Total: <b>$<?php echo number_format($total, 2); ?> MXN</b></div>
                    <a class="boton rojo grande" href="comprar.php">Comprar</a>
                </div>
            </div>
        <?php } ?>

    </div>
</section>

<?php footer(); ?>
</body>
</html>
