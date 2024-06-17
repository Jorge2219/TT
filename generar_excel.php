<?php
require 'C:/xampp/htdocs/Login/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

if (!isset($_GET['encuesta_id'])) {
    die('Error: encuesta_id no está definido.');
}

$encuesta_id = $_GET['encuesta_id'];

// Conexión a la base de datos
$mysqli = new mysqli("localhost", "root", "", "login");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Obtener el título del servicio desde la base de datos
$queryServicio = "SELECT nombre FROM servicios WHERE id = (SELECT id_servicio FROM encuestas WHERE id = ?)";
$stmtServicio = $mysqli->prepare($queryServicio);
$stmtServicio->bind_param("i", $encuesta_id);
$stmtServicio->execute();
$resultServicio = $stmtServicio->get_result();
$servicio = $resultServicio->fetch_assoc();
$nombreServicio = $servicio ? $servicio['nombre'] : 'Desconocido';

// Obtener el título de la encuesta desde la base de datos
$queryEncuesta = "SELECT nombre FROM encuestas WHERE id = ?";
$stmtEncuesta = $mysqli->prepare($queryEncuesta);
$stmtEncuesta->bind_param("i", $encuesta_id);
$stmtEncuesta->execute();
$resultEncuesta = $stmtEncuesta->get_result();
$encuesta = $resultEncuesta->fetch_assoc();
$tituloEncuesta = $encuesta ? $encuesta['nombre'] : 'Desconocida';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configuración de estilos
$boldStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];

$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

$fillPantone7420Style = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => 'FFAB2328'], // Pantone 7420
    ],
];

$fillPantone424Style = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => 'FF7C7F7E'], // Pantone 424 C
    ],
];

$fillPantone468Style = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => 'FFE0D7AF'], // Pantone 468
    ],
];

$blackFontStyle = [
    'font' => [
        'color' => ['argb' => Color::COLOR_BLACK],
    ],
];

$whiteFontStyle = [
    'font' => [
        'color' => ['argb' => Color::COLOR_WHITE],
    ],
];

// Encabezados y configuraciones de la tabla
$sheet->mergeCells('B1:R1');
$sheet->mergeCells('B2:R2');
$sheet->mergeCells('B3:R3');
$sheet->mergeCells('B4:R4');
$sheet->mergeCells('B5:R5');
$sheet->setCellValue('B1', 'Instituto Politécnico Nacional');
$sheet->setCellValue('B2', 'Unidad Profesional Interdisciplinaria de Ingeniería campus Zacatecas');
$sheet->setCellValue('B3', 'Título del servicio: ' . $nombreServicio);
$sheet->setCellValue('B4', 'Título de la encuesta: ' . $tituloEncuesta);

// Agregar logotipos
$logoIPN = new Drawing();
$logoIPN->setName('Logo IPN');
$logoIPN->setDescription('Logo IPN');
$logoIPN->setPath('C:/xampp/htdocs/Login/img/LogoIPN.png');
$logoIPN->setHeight(90);
$logoIPN->setCoordinates('B1');
$logoIPN->setWorksheet($spreadsheet->getActiveSheet());

$logoUPIIZ = new Drawing();
$logoUPIIZ->setName('Logo UPIIZ');
$logoUPIIZ->setDescription('Logo UPIIZ');
$logoUPIIZ->setPath('C:/xampp/htdocs/Login/img/LogoUPIIZ.png');
$logoUPIIZ->setHeight(90);
$logoUPIIZ->setCoordinates('R1');
$logoUPIIZ->setWorksheet($spreadsheet->getActiveSheet());

$sheet->setCellValue('B8', 'PREGUNTAS');
$sheet->getStyle('B8')->applyFromArray($boldStyle);
$sheet->getStyle('B8')->applyFromArray($fillPantone468Style);
$sheet->getStyle('B8')->applyFromArray($borderStyle);

// Consulta SQL para obtener las preguntas y sus respuestas
$sqlPreguntas = "
    SELECT id, pregunta 
    FROM preguntas 
    WHERE encuesta_id = ?
    ORDER BY id
";

$sqlRespuestas = "
    SELECT pregunta_id, respuesta, puntuacion, descripcion
    FROM respuestas_prueba 
    WHERE pregunta_id IN (
        SELECT id 
        FROM preguntas 
        WHERE encuesta_id = ?
    )
    ORDER BY pregunta_id, id
";

$stmtPreguntas = $mysqli->prepare($sqlPreguntas);
$stmtPreguntas->bind_param("i", $encuesta_id);
$stmtPreguntas->execute();
$resultPreguntas = $stmtPreguntas->get_result();

$preguntas = [];
while ($row = $resultPreguntas->fetch_assoc()) {
    $preguntas[] = $row;
}

$stmtRespuestas = $mysqli->prepare($sqlRespuestas);
$stmtRespuestas->bind_param("i", $encuesta_id);
$stmtRespuestas->execute();
$resultRespuestas = $stmtRespuestas->get_result();

$respuestas = [];
while ($row = $resultRespuestas->fetch_assoc()) {
    $respuestas[$row['pregunta_id']][] = $row;
}

$mysqli->close();

// Escribir encabezados de ID de respuesta (representan a las personas que respondieron)
$col = 'C';
$respuestaNum = 1;
foreach ($preguntas as $pregunta) {
    if (isset($respuestas[$pregunta['id']])) {
        foreach ($respuestas[$pregunta['id']] as $respuesta) {
            $sheet->setCellValue($col . '8', $respuestaNum);
            $sheet->getStyle($col . '8')->applyFromArray($fillPantone7420Style);
            $sheet->getStyle($col . '8')->applyFromArray($whiteFontStyle);
            $col++;
            $respuestaNum++;
        }
    }
}

// Escribir preguntas y puntuaciones en el archivo de Excel
$row = 9;
foreach ($preguntas as $pregunta) {
    $sheet->setCellValue('B' . $row, $pregunta['pregunta']);
    $sheet->getStyle('B' . $row)->applyFromArray($fillPantone7420Style);
    $sheet->getStyle('B' . $row)->applyFromArray($whiteFontStyle);
    $col = 'C';
    if (isset($respuestas[$pregunta['id']])) {
        foreach ($respuestas[$pregunta['id']] as $respuesta) {
            if ($respuesta['puntuacion'] !== null) {
                $sheet->setCellValue($col . $row, $respuesta['puntuacion']);
                $sheet->getStyle($col . $row)->applyFromArray($blackFontStyle);
                // Eliminamos la aplicación del estilo del borde
            }
            $col++;
        }
    }
    $row++;
}

// Agregar descripciones
$sheet->setCellValue('B' . ($row + 2), 'Descripciones');
$sheet->getStyle('B' . ($row + 2))->applyFromArray($boldStyle);
$currentDescriptionRow = $row + 3;
$col = 'C';
foreach ($preguntas as $pregunta) {
    if (isset($respuestas[$pregunta['id']])) {
        foreach ($respuestas[$pregunta['id']] as $respuesta) {
            $sheet->setCellValue($col . $currentDescriptionRow, $respuesta['descripcion']);
            $sheet->getStyle($col . $currentDescriptionRow)->applyFromArray($fillPantone468Style);
            $sheet->getStyle($col . $currentDescriptionRow)->applyFromArray($borderStyle);
            $col++;
        }
        $currentDescriptionRow++;
        $col = 'C';
    }
}

// Agregar comentarios
$sheet->setCellValue('B' . ($currentDescriptionRow + 2), 'Comentarios');
$sheet->getStyle('B' . ($currentDescriptionRow + 2))->applyFromArray($boldStyle);
$currentCommentRow = $currentDescriptionRow + 3;
$col = 'C';
foreach ($preguntas as $pregunta) {
    if (isset($respuestas[$pregunta['id']])) {
        foreach ($respuestas[$pregunta['id']] as $respuesta) {
            $sheet->setCellValue($col . $currentCommentRow, $respuesta['respuesta']);
            $sheet->getStyle($col . $currentCommentRow)->applyFromArray($fillPantone424Style);
            $sheet->getStyle($col . $currentCommentRow)->applyFromArray($whiteFontStyle);
            $col++;
        }
        $currentCommentRow++;
        $col = 'C';
    }
}

// Aplicar estilos a las celdas de encabezados
$sheet->getStyle('B1:R1')->applyFromArray($boldStyle);
$sheet->getStyle('B2:R2')->applyFromArray($boldStyle);
$sheet->getStyle('B3:R3')->applyFromArray($boldStyle);
$sheet->getStyle('B4:R4')->applyFromArray($boldStyle);
$sheet->getStyle('B5:R5')->applyFromArray($boldStyle);
$sheet->getStyle('B8:R8')->applyFromArray($borderStyle);

// Guardar el archivo de Excel
$writer = new Xlsx($spreadsheet);
$filename = 'reporte_encuesta.xlsx';
$writer->save($filename);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');

exit();
?>