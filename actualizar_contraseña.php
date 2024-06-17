<link rel="stylesheet" href="css/sweetalert2.min.css">
<script src="js/sweetalert2.all.min.js"></script>
<?php
include 'conexion.php';

if (!empty($_POST["btnActualizar"])) {
    $token_recuperacion = $_POST["token"];
    $nueva_contraseña = $_POST["password"];

    // Actualizar la contraseña en la base de datos
    $sql_update_password = $conexion->query("UPDATE usuario SET clave = '$nueva_contraseña', token_recuperacion = NULL WHERE token_recuperacion = '$token_recuperacion'");

    if ($sql_update_password) {
        ?>
        <body>
        <script>
            Swal.fire({
                title: "¡Contraseña actualizada con éxito!",
                html: "Serás redirigido al inicio de sesión",
                icon: "success",
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    window.location.href = 'login.php'; // Redirige a login.php al cerrar la alerta
                }
            });
        </script>
        </body>
       
        <?php    } else {
        echo '<div class="alert alert-danger">Hubo un error al actualizar la contraseña.</div>';
    }
}
?>
