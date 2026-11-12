<?php
// PEDIDOS: muestra las compras que he hecho con mi cuenta.
// Los datos salen de las tablas "pedidos" y "pedido_productos".

include "conexion.php";
include "funciones.php";

// Para ver los pedidos hay que tener la cuenta abierta
if (!isset($_SESSION['id'])) {
    header("Location: cuenta.php");
    exit;
}

$usuario = $_SESSION['id'];

$titulo = "Pedidos";
$pagina = "pedidos";
include "encabezado.php";

// Pido a la base de datos mis pedidos, el más nuevo primero
$consulta = mysqli_prepare($conexion, "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY id DESC");
mysqli_stmt_bind_param($consulta, "i", $usuario);
mysqli_stmt_execute($consulta);
$pedidos = mysqli_stmt_get_result($consulta);
?>

<section class="encabezado-pagina">
    <div class="contenido">
        <h1>Mis <span class="rojo-texto">pedidos</span></h1>
        <p>Aquí aparecen todas tus compras.</p>
    </div>
</section>

<section class="seccion">
    <div class="contenido">

        <!-- Aviso verde: sale cuando vienen de comprar -->
        <?php if (isset($_GET['listo'])) { ?>
            <div class="aviso bien">¡Pedido registrado! Gracias por tu compra.</div>
        <?php } ?>

        <?php if (mysqli_num_rows($pedidos) == 0) { ?>
            <div class="carrito-vacio">
                <p>Todavía no has hecho ningún pedido.</p>
                <a class="boton rojo grande" href="trajes.php">Ver productos</a>
            </div>
        <?php } ?>

        <?php while ($pedido = mysqli_fetch_assoc($pedidos)) { ?>
            <div class="pedido-caja">
                <div class="pedido-cabeza">
                    <h3>Pedido #<?php echo $pedido['id']; ?></h3>
                    <span class="gris"><?php echo date("d/m/Y H:i", strtotime($pedido['fecha'])); ?></span>
                </div>
                <p class="pedido-datos">Dirección: <?php echo htmlspecialchars($pedido['direccion']); ?></p>
                <p class="pedido-datos">Método de pago: <?php echo htmlspecialchars($pedido['metodo_pago']); ?></p>

                <?php
                // Pido los productos de este pedido
                $consulta = mysqli_prepare($conexion, "SELECT * FROM pedido_productos WHERE id_pedido = ?");
                mysqli_stmt_bind_param($consulta, "i", $pedido['id']);
                mysqli_stmt_execute($consulta);
                $detalle = mysqli_stmt_get_result($consulta);

                while ($item = mysqli_fetch_assoc($detalle)) {
                ?>
                    <div class="resumen-fila">
                        <span><?php echo $item['cantidad']; ?> x <?php echo htmlspecialchars($item['nombre']); ?></span>
                        <span>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                    </div>
                <?php } ?>

                <div class="resumen-total">
                    <span>Total</span>
                    <b>$<?php echo number_format($pedido['total'], 2); ?> MXN</b>
                </div>
            </div>
        <?php } ?>

    </div>
</section>

<?php footer(); ?>
</body>
</html>
