<?php
// CUENTA: aquí se entra a la cuenta.
// Revisa en la tabla "usuarios" que el correo y la contraseña existan.

include "conexion.php";
include "funciones.php";

$error = "";
$correo = "";

// Esto solo pasa cuando le dan clic a "Entrar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    // Busco a alguien con ese correo
    $consulta = mysqli_prepare($conexion, "SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?");
    mysqli_stmt_bind_param($consulta, "s", $correo);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $usuario = mysqli_fetch_assoc($resultado);   // si no existe, queda vacío

    // Si existe y la contraseña es igual, lo dejo entrar y guardo su id y nombre
    if ($usuario && $contrasena == $usuario['contrasena']) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}

$titulo = "Cuenta";
$pagina = "cuenta";
include "encabezado.php";
?>

<!-- ===== FORMULARIO ===== -->
<section class="formulario-fondo">
    <div class="formulario">
        <h1>Mi <span class="rojo-texto">cuenta</span></h1>
        <p class="gris">Escribe tu correo y tu contraseña.</p>

        <!-- Aviso verde: sale cuando vienen de registrarse -->
        <?php if (isset($_GET['registro'])) { ?>
            <div class="aviso bien">¡Cuenta creada! Ya puedes entrar a tu cuenta.</div>
        <?php } ?>

        <!-- Aviso: sale si quisieron usar el carrito sin tener la cuenta abierta -->
        <?php if (isset($_GET['carrito'])) { ?>
            <div class="aviso error">Entra a tu cuenta para usar el carrito.</div>
        <?php } ?>

        <!-- Aviso rojo: sale si se equivocaron -->
        <?php if ($error != "") { ?>
            <div class="aviso error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="post" action="cuenta.php">
            <label>Correo electrónico
                <input type="email" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
            </label>
            <label>Contraseña
                <input type="password" name="contrasena" required>
            </label>
            <button class="boton rojo grande completo" type="submit">Entrar</button>
        </form>

        <p class="alternativa">¿Aún no tienes cuenta? <a href="registrar.php">Regístrate</a></p>
    </div>
</section>

<?php footer(); ?>
</body>
</html>
