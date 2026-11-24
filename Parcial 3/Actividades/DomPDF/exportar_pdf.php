<?php
// Reporte de errores para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '256M');

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Cargar y procesar datos del JSON
$jsonPath = __DIR__ . '/data.json';
if (!file_exists($jsonPath)) {
    die("Error: No se encontró el archivo data.json");
}

$jsonData = json_decode(file_get_contents($jsonPath), true);
$titulo = $jsonData['titulo'] ?? 'Calificaciones finales de alumnos';
$unidad = $jsonData['unidad'] ?? 'puntos';
$puntos = $jsonData['puntos'] ?? [];

$totalAlumnos = count($puntos);
$sumaCalificaciones = 0;
$sumaAsistencia = 0;
$sumaTareas = 0;
$aprobados = 0;
$reprobados = 0;

$grupos = [];

foreach ($puntos as $p) {
    $calif = $p['total'];
    $sumaCalificaciones += $calif;
    $sumaAsistencia += $p['asistencia'];
    $sumaTareas += $p['tareas_entregadas'];

    if ($calif >= 70) {
        $aprobados++;
    } else {
        $reprobados++;
    }

    $grp = $p['categoria'];
    if (!isset($grupos[$grp])) {
        $grupos[$grp] = [
            'total' => 0,
            'sumaCalif' => 0,
            'sumaAsistencia' => 0,
            'sumaTareas' => 0,
            'aprobados' => 0
        ];
    }
    $grupos[$grp]['total']++;
    $grupos[$grp]['sumaCalif'] += $calif;
    $grupos[$grp]['sumaAsistencia'] += $p['asistencia'];
    $grupos[$grp]['sumaTareas'] += $p['tareas_entregadas'];
    if ($calif >= 70) {
        $grupos[$grp]['aprobados']++;
    }
}

ksort($grupos);

$promedioGeneral = $totalAlumnos > 0 ? ($sumaCalificaciones / $totalAlumnos) : 0;
$promedioAsistencia = $totalAlumnos > 0 ? ($sumaAsistencia / $totalAlumnos) : 0;
$promedioTareas = $totalAlumnos > 0 ? ($sumaTareas / $totalAlumnos) : 0;
$tasaAprobacion = $totalAlumnos > 0 ? (($aprobados / $totalAlumnos) * 100) : 0;

$metricasGrupos = [];
foreach ($grupos as $k => $v) {
    $metricasGrupos[$k] = [
        'total' => $v['total'],
        'promedio' => $v['sumaCalif'] / $v['total'],
        'asistencia' => $v['sumaAsistencia'] / $v['total'],
        'tareas' => $v['sumaTareas'] / $v['total'],
        'aprobados' => $v['aprobados']
    ];
}

// Top 10 alumnos
$topAlumnos = $puntos;
usort($topAlumnos, function($a, $b) {
    return $b['total'] <=> $a['total'];
});
$topAlumnos = array_slice($topAlumnos, 0, 10);

// Recibir imágenes base64 de las gráficas desde POST
$chartPromedioGrupos = $_POST['chart_promedio_grupos'] ?? '';
$chartAprobados = $_POST['chart_aprobados'] ?? '';
$chartAsistenciaGrupos = $_POST['chart_asistencia_grupos'] ?? '';
$chartGenero = $_POST['chart_genero'] ?? '';

// Renderizar la plantilla HTML
ob_start();
include __DIR__ . '/plantilla_pdf.php';
$html = ob_get_clean();

// Configuración de Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();

// Enviar el PDF al navegador (Attachment => false abre en visor de PDF)
$dompdf->stream("Reporte_Dashboard_Calificaciones.pdf", ["Attachment" => false]);
exit;
