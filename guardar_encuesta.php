<?php
session_start();
include "conexion.php";

$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : "";
$folio = isset($_POST['folio']) ? $_POST['folio'] : "";
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : "";
$servicio = ""; // Inicializar la variable servicio

$response = []; // Array para almacenar la respuesta

if ($conexion !== null) {
    // Verificar si hay un usuario iniciado sesión
    if (isset($_SESSION['usuario_id'])) {
        $creador_id = $_SESSION['usuario_id'];
        
        // Consulta para obtener el área del usuario
        $consultaAreaUsuario = "SELECT area FROM usuario WHERE id = $creador_id";
        $resultadoArea = $conexion->query($consultaAreaUsuario);
        $filaArea = $resultadoArea->fetch_assoc();
        $servicio = $filaArea['area']; // Utilizamos el área del usuario como servicio

        // Obtener preguntas del formulario
        $preguntas_json = isset($_POST['questions']) ? $_POST['questions'] : "";
        $preguntas = json_decode($preguntas_json);

        // Insertar preguntas en la base de datos
        if (!empty($preguntas)) {
            foreach ($preguntas as $pregunta) {
                // Ajusta la inserción según tu estructura de base de datos
                $sql_pregunta = "INSERT INTO encabezado_largo (pregunta) VALUES ('$pregunta')";
                if ($conexion->query($sql_pregunta) !== TRUE) {
                    $response['status'] = 'error';
                    $response['message'] = 'Error al guardar preguntas en la base de datos: ' . $conexion->error;
                    // En caso de error, puedes manejarlo según tus necesidades
                }
            }
        }

        // Insertar encuesta en la base de datos
        $sql = "INSERT INTO encuestas (nombre, folio, fecha, creador_id, servicio) 
                VALUES ('$nombre', '$folio', '$fecha', '$creador_id', '$servicio')";

        if ($conexion->query($sql) === TRUE) {
            $response['status'] = 'success';
            $response['message'] = 'Encuesta guardada en la base de datos';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error al guardar en la base de datos: ' . $conexion->error;
        }

        // Consulta para obtener todas las encuestas
        $consultaEncuestas = "SELECT * FROM encuestas";
        $result = $conexion->query($consultaEncuestas);

        // Guarda las encuestas en un array para enviarlas al cliente
        $encuestas = [];
        while ($row = $result->fetch_assoc()) {
            $encuestas[] = $row;
        }

        $response['encuestas'] = $encuestas;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Usuario no autenticado';
    }

    $conexion->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error: Conexión a la base de datos no establecida';
}

// Enviar la respuesta al cliente en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>