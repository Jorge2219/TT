<link rel="stylesheet" href="css/sweetalert2.min.css">
    <script src="js/sweetalert2.all.min.js"></script>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha pasado un ID válido por el parámetro GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_encuestas = $_GET['id'];

    // Consulta para eliminar el usuario
    $sql = "DELETE FROM encuestas WHERE id = $id_encuestas";

    if ($conn->query($sql) === TRUE) {
        ?>
        <body>
            <script>
            Swal.fire(
                    'Huélum',
                    'Encuesta Eliminada!',
                    'success'
                ).then(function() {
                    // Redirigir a la página deseada
                    window.location.href = 'encuestas.php';
                });
        </script>
        </body>
        
        <?php
    } else {
        echo "Error al eliminar Encuesta: " . $conn->error;
    }
} else {
    echo "ID de Encuesta no válido";
}

// Cerrar la conexión
$conn->close();
?>
