<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <title>Recuperar Contraseña</title>
</head>
<body>
   <img class="wave" src="img/wave1.png">
   <div class="container">
      <div class="img">
      </div>
      <div class="login-content">
         <form method="post" action="recuperacion_exitosa.php"> <!-- Cambio en esta línea -->
            <img src="img/candado.png">
            <h2 class="title">Recuperar Contraseña</h2>
            <?php
            include("conexion.php");
            ?>
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-envelope"></i>
               </div>
               <div class="div">
                  <h5>Correo Electrónico</h5>
                  <input id="email" type="email" class="input" name="correo">
               </div>
            </div>
            <a class="font-italic isai5" href="login.php">Regresar</a>
            <input name="btnRecuperar" class="btn" type="submit" value="RECUPERAR CONTRASEÑA">
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
