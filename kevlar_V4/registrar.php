<?php
// REGISTRAR: formulario para crear una cuenta nueva.
// Guarda a la persona en la tabla "usuarios".

include "conexion.php";
include "funciones.php";

// Variables vacías para que el formulario salga limpio la primera vez
$nombre = "";
$apellido = "";
$correo = "";
$telefono = "";
$errores = array();   // aquí voy juntando los errores

// Esto solo pasa cuando le dan clic a "Registrarme"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Saco lo que escribieron en el formulario
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $contrasena2 = $_POST['contrasena2'];

    // ----- 1. Reviso que todo esté bien (si algo falla, guardo el error) -----
    if ($nombre == "" || $apellido == "") {
        $errores[] = "Nombre y apellido son obligatorios.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Escribe un correo válido.";
    }
    if ($telefono != "" && !preg_match('/^[0-9 +()-]{7,20}$/', $telefono)) {
        $errores[] = "El teléfono no es válido.";
    }
    if (strlen($contrasena) < 8) {
        $errores[] = "La contraseña debe tener mínimo 8 caracteres.";
    }
    if ($contrasena != $contrasena2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    // ----- 2. Si todo bien, reviso que el correo no esté ya registrado -----
    if (count($errores) == 0) {
        $consulta = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE correo = ?");
        mysqli_stmt_bind_param($consulta, "s", $correo);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);

        if (mysqli_num_rows($resultado) > 0) {
            $errores[] = "Ese correo ya está registrado.";
        }
    }

    // ----- 3. Si sigue sin errores, lo guardo en la base de datos -----
    if (count($errores) == 0) {
        // La contraseña se guarda tal cual, sin cifrar
        $consulta = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre, apellido, correo, telefono, contrasena) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($consulta, "sssss", $nombre, $apellido, $correo, $telefono, $contrasena);
        mysqli_stmt_execute($consulta);

        // Lo mando a su cuenta
        header("Location: cuenta.php?registro=ok");
        exit;
    }
}

$titulo = "Registrar";
$pagina = "registrar";
include "encabezado.php";
?>

<!-- ===== FORMULARIO ===== -->
<section class="formulario-fondo">
    <div class="formulario">
        <h1>Crear <span class="rojo-texto">cuenta</span></h1>
        <p class="gris">Tus datos se guardan en nuestra base de datos.</p>

        <!-- Los errores salen aquí, en rojo -->
        <?php foreach ($errores as $error) { ?>
            <div class="aviso error"><?php echo $error; ?></div>
        <?php } ?>

        <!-- Al enviar, los datos regresan a esta misma página -->
        <form method="post" action="registrar.php">
            <label>Nombre
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </label>
            <label>Apellido
                <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
            </label>
            <label>Correo electrónico
                <input type="email" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
            </label>
            <label>Teléfono (opcional)
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
            </label>
            <label>Contraseña
                <input type="password" name="contrasena" required>
            </label>
            <label>Confirmar contraseña
                <input type="password" name="contrasena2" required>
            </label>
            <button class="boton rojo grande completo" type="submit">Registrarme</button>
        </form>

        <p class="alternativa">¿Ya tienes cuenta? <a href="cuenta.php">Entra a tu cuenta</a></p>
    </div>
</section>

<?php footer(); ?>
</body>
</html>
