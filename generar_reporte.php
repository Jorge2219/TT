<?php
require('conexion.php');
require('TCPDF-main/tcpdf.php');

// Obtener ID de la encuesta desde la URL
$encuesta_id = $_GET['encuesta_id'];

// Consulta para obtener las preguntas de la encuesta
$query = "SELECT id, pregunta FROM preguntas WHERE encuesta_id = $encuesta_id";
$result = $conexion->query($query);

// Función para calcular estadísticas descriptivas
function calcularEstadisticas($puntuaciones) {
    $n = count($puntuaciones);
    if ($n == 0) {
        return [
            'media' => 0,
            'mediana' => 0,
            'desv_estandar' => 0,
            'varianza' => 0,
            'min' => 0,
            'max' => 0
        ];
    }

    $media = array_sum($puntuaciones) / $n;
    sort($puntuaciones);
    $mediana = $puntuaciones[(int)($n / 2)];
    $desv_estandar = sqrt(array_sum(array_map(function($x) use ($media) {
        return pow($x - $media, 2);
    }, $puntuaciones)) / $n);
    $varianza = pow($desv_estandar, 2);
    $min = min($puntuaciones);
    $max = max($puntuaciones);

    return [
        'media' => $media,
        'mediana' => $mediana,
        'desv_estandar' => $desv_estandar,
        'varianza' => $varianza,
        'min' => $min,
        'max' => $max
    ];
}

// Crear nuevo PDF
$pdf = new TCPDF();
$pdf->AddPage();

// Título del PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de la Encuesta ' . $encuesta_id, 0, 1, 'C');
$pdf->Ln(10);

// Iterar sobre cada pregunta
while ($pregunta = $result->fetch_assoc()) {
    $pregunta_id = $pregunta['id'];
    $texto_pregunta = $pregunta['pregunta'];

    // Obtener puntuaciones de la pregunta actual
    $query_respuestas = "SELECT puntuacion FROM respuestas_prueba WHERE encuesta_id = $encuesta_id AND pregunta_id = $pregunta_id";
    $result_respuestas = $conexion->query($query_respuestas);

    $puntuaciones = [];
    while ($respuesta = $result_respuestas->fetch_assoc()) {
        $puntuaciones[] = $respuesta['puntuacion'];
    }

    // Calcular estadísticas descriptivas
    $estadisticas = calcularEstadisticas($puntuaciones);

    // Contar las frecuencias de cada puntuación
    $frecuencias = array_count_values($puntuaciones);
    ksort($frecuencias);

    // Generar datos para la gráfica
    $labels = [];
    $data = [];
    foreach ($frecuencias as $puntuacion => $conteo) {
        $labels[] = $puntuacion;
        $data[] = $conteo;
    }

    // Verificar si hay datos para evitar errores
    if (!empty($data)) {
        $max_value = max($data);
        $suggested_max = $max_value + 1;
    } else {
        $max_value = 0;
        $suggested_max = 1;
    }

    $chart_data = json_encode($data);
    $chart_labels = json_encode($labels);

    // Generar URL de la gráfica de barras con escala ajustada y etiqueta "puntuacion"
    $chart_url_bar = "https://quickchart.io/chart?c=" . urlencode(json_encode([
        "type" => "bar",
        "data" => [
            "labels" => $labels,
            "datasets" => [
                [
                    "label" => "Puntuaciones",
                    "data" => $data
                ]
            ]
        ],
        "options" => [
            "scales" => [
                "yAxes" => [
                    [
                        "ticks" => [
                            "beginAtZero" => true,
                            "suggestedMax" => $suggested_max
                        ]
                    ]
                ],
                "xAxes" => [
                    [
                        "scaleLabel" => [
                            "display" => true,
                            "labelString" => "puntuacion"
                        ]
                    ]
                ]
            ]
        ]
    ]));

    // Generar URL de la gráfica de pastel
    $chart_url_pie = "https://quickchart.io/chart?c=" . urlencode(json_encode([
        "type" => "pie",
        "data" => [
            "labels" => $labels,
            "datasets" => [
                [
                    "label" => "Puntuaciones",
                    "data" => $data,
                    "backgroundColor" => [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    "borderColor" => [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    "borderWidth" => 1
                ]
            ]
        ],
        "options" => [
            "responsive" => true,
            "maintainAspectRatio" => false
        ]
    ]));

    // Insertar pregunta
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Pregunta: ' . $texto_pregunta, 0, 1);
    $pdf->Ln(5);

    // Insertar gráfica de barras
    $img_file = file_get_contents($chart_url_bar);
    if ($img_file !== false) {
        $pdf->Image('@' . $img_file, '', '', 150, 75, 'PNG');
        $pdf->Ln(90); // Espacio adicional después de la gráfica
    } else {
        $pdf->Cell(0, 10, 'No se pudo generar la gráfica de barras.', 0, 1);
        $pdf->Ln(10); // Espacio adicional en caso de error
    }

    // Insertar gráfica de pastel
    $img_file = file_get_contents($chart_url_pie);
    if ($img_file !== false) {
        $pdf->Image('@' . $img_file, '', '', 150, 75, 'PNG');
        $pdf->Ln(90); // Espacio adicional después de la gráfica
    } else {
        $pdf->Cell(0, 10, 'No se pudo generar la gráfica de pastel.', 0, 1);
        $pdf->Ln(10); // Espacio adicional en caso de error
    }

    // Insertar estadísticas descriptivas en una tabla
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 10, 'Estadísticas Descriptivas:', 0, 1);
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 10, 'Media:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['media'], 2), 1, 1);
    $pdf->Cell(30, 10, 'Mediana:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['mediana'], 2), 1, 1);
    $pdf->Cell(30, 10, 'Desv. Estándar:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['desv_estandar'], 2), 1, 1);
    $pdf->Cell(30, 10, 'Varianza:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['varianza'], 2), 1, 1);
    $pdf->Cell(30, 10, 'Mínimo:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['min'], 2), 1, 1);
    $pdf->Cell(30, 10, 'Máximo:', 1);
    $pdf->Cell(30, 10, number_format($estadisticas['max'], 2), 1, 1);
    $pdf->Ln(10); // Espacio después de las estadísticas
}

// Salida del PDF
$pdf->Output('reporte_encuesta_' . $encuesta_id . '.pdf', 'I');
?>