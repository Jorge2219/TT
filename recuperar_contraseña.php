<?php
include 'conexion.php';

// Verificar si se proporcionó un token en la URL
if (!empty($_GET['token'])) {
    $token_recuperacion = $_GET['token'];

    // Buscar el usuario por el token de recuperación en la base de datos
    $sql_select = $conexion->query("SELECT * FROM usuario WHERE token_recuperacion = '$token_recuperacion'");

    if ($sql_select->num_rows > 0) {
        // Token válido, mostrar formulario para actualizar contraseña
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <title>Nueva Contraseña</title>
</head>
<body>
   <img class="wave" src="img/wave1.png">
   <div class="container">
      <div class="img">
      </div>
      <div class="login-content">
         <form method="post" action="actualizar_contraseña.php"> <!-- Cambio en esta línea -->
            <img src="img/candado.png">
            <h2 class="title">Actualizar Contraseña</h2>
            <?php
            include("conexion.php");
            ?>
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-lock"></i>
               </div>
               <div class="div">
                  <h5>Contraseña nueva</h5>
                  <input type="hidden" name="token" value="<?php echo $token_recuperacion; ?>">
                    <input type="password" class="input" name="password" required>
               </div>
            </div>
            <a class="font-italic isai5" href="login.php">Regresar</a>
            <input name="btnActualizar" class="btn" type="submit" value="Actualizar Contraseña">
         </form>
      </div>
   </div>
   <script src="js/fontawesome.js"></script>
   <script src="js/main.js"></script>
   <script src="js/main2.js"></script>
   <script src="js/jquery.min.js"></script>
   <script src="js/bootstrap.js"></script>
   <script src="js/bootstrap.bundle.js"></script>
</body>
</html>


        <?php
    } else {
        echo 'Token no válido o expirado.';
    }
} else {
    echo 'Token no proporcionado en la URL.';
}
?>
