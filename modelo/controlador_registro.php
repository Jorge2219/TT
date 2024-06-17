<?php
$sql = false; // Inicializar la variable $sql

if (!empty($_POST["btnregistrar"])) {
    if (empty($_POST["usuario"]) or empty($_POST["password"])) {
        echo '<div class="alert alert-danger">Por favor llene los campos</div>';
    } else {
        $usuario = $_POST["usuario"];
        $clave = $_POST["password"];
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $area = $_POST["area"];

        // Verificar si el correo electrónico tiene el dominio "@ipn.mx" o "@gmail.com"
        if (strpos($usuario, '@ipn.mx') !== false || strpos($usuario, '@gmail.com') !== false) {
            // Generar tokens únicos
            $token = md5(uniqid(rand(), true));
            $token_recuperacion = md5(uniqid(rand(), true));

            // Guardar usuario con estado no verificado y tokens en la base de datos
            $sql_insert = $conexion->query("INSERT INTO usuario (usuario, clave, nombre, apellidos, area, estado, token, token_recuperacion) VALUES ('$usuario','$clave', '$nombre', '$apellidos', '$area', 'no verificado', '$token', '$token_recuperacion')");

            if ($sql_insert) {
                // Envía el correo con el enlace de verificación
                $asunto_verificacion = "Verificación de cuenta";
                $mensaje_verificacion = "Hola $nombre, haz clic en el siguiente enlace para verificar tu cuenta: http://192.168.137.73/login/verificar.php?token=$token";
                mail($usuario, $asunto_verificacion, $mensaje_verificacion);

                // Muestra mensaje de éxito
                ?>
                <script>
                    Swal.fire(
                        'Huélum',
                        'Usuario registrado! Se ha enviado un correo de verificación.',
                        'success'
                    ).then(function () {
                        // Redirigir a la página deseada
                        window.location.href = 'login.php';
                    });
                </script>
                <?php
            } else {
                // Muestra mensaje de error al insertar en la base de datos
                ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error...',
                        text: 'Hubo un error al registrar el usuario'
                    })
                </script>
                <?php
            }
        } else {
            // Muestra mensaje de error de dominio de correo electrónico
            echo '<div class="alert alert-danger">El correo electrónico debe ser de dominio @ipn.mx o @gmail.com</div>';
        }
    }
}
?>
<script>
    setTimeout(() => {
        window.history.replaceState(null, null, window.location.pathname)
    }, 0);
</script>
