<?php
// SALIR: cierra la sesión y regresa al inicio
session_start();
session_destroy();
header("Location: index.php");
exit;
