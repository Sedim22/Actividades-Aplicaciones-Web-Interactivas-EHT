<?php
$jsonPath = __DIR__ . '/data.json';
if (!file_exists($jsonPath)) {
    die("Error: No se encontró data.json");
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
$generoConteo = ['F' => 0, 'M' => 0];
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

    if (isset($p['genero'])) {
        $g = strtoupper($p['genero']);
        $generoConteo[$g] = ($generoConteo[$g] ?? 0) + 1;
    }

    $grp = $p['categoria'];
    if (!isset($grupos[$grp])) {
        $grupos[$grp] = [
            'alumnos' => 0,
            'sumaCalif' => 0,
            'sumaAsistencia' => 0,
            'sumaTareas' => 0,
            'aprobados' => 0
        ];
    }
    $grupos[$grp]['alumnos']++;
    $grupos[$grp]['sumaCalif'] += $calif;
    $grupos[$grp]['sumaAsistencia'] += $p['asistencia'];
    $grupos[$grp]['sumaTareas'] += $p['tareas_entregadas'];
    if ($calif >= 70) {
        $grupos[$grp]['aprobados']++;
    }
}

ksort($grupos);

$promedioGeneral = $totalAlumnos > 0 ? round($sumaCalificaciones / $totalAlumnos, 1) : 0;
$promedioAsistencia = $totalAlumnos > 0 ? round($sumaAsistencia / $totalAlumnos, 1) : 0;
$promedioTareas = $totalAlumnos > 0 ? round($sumaTareas / $totalAlumnos, 1) : 0;
$tasaAprobacion = $totalAlumnos > 0 ? round(($aprobados / $totalAlumnos) * 100, 1) : 0;

$gruposLabels = array_keys($grupos);
$gruposPromedios = [];
$gruposAsistencias = [];
$gruposTareas = [];

foreach ($grupos as $g) {
    $gruposPromedios[] = round($g['sumaCalif'] / $g['alumnos'], 1);
    $gruposAsistencias[] = round($g['sumaAsistencia'] / $g['alumnos'], 1);
    $gruposTareas[] = round($g['sumaTareas'] / $g['alumnos'], 1);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Académico - DomPDF & Chart.js</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .chart-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .chart-container {
            position: relative;
            height: 270px;
            width: 100%;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
            <span>Sistema Escolar | Generador DomPDF</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary text-uppercase d-none d-md-inline-block">Unidad: <?= htmlspecialchars($unidad) ?></span>
            <button id="btnExportarPdf" class="btn btn-danger btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-semibold shadow-sm">
                <i class="bi bi-download"></i>
                <span>Exportar Dashboard a PDF</span>
            </button>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($titulo) ?></h2>
            <p class="text-muted mb-0">Analítica de rendimiento académico de <?= $totalAlumnos ?> alumnos evaluados en 6 grupos.</p>
        </div>
        <div class="text-end">
            <span class="text-muted small">Estado de Exportación:</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Listo para DomPDF</span>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Promedio General</span>
                        <h3 class="fw-bold my-1 text-primary"><?= $promedioGeneral ?></h3>
                        <span class="text-muted small">Escala de 0 a 100 pts</span>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Tasa de Aprobación</span>
                        <h3 class="fw-bold my-1 text-success"><?= $tasaAprobacion ?>%</h3>
                        <span class="text-muted small"><?= $aprobados ?> de <?= $totalAlumnos ?> alumnos (&ge; 70)</span>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Asistencia Media</span>
                        <h3 class="fw-bold my-1 text-info"><?= $promedioAsistencia ?>%</h3>
                        <span class="text-muted small">Promedio en todas las clases</span>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Entrega de Tareas</span>
                        <h3 class="fw-bold my-1 text-warning"><?= $promedioTareas ?> / 20</h3>
                        <span class="text-muted small"><?= round(($promedioTareas/20)*100, 1) ?>% de cumplimiento</span>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas Chart.js -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card chart-card p-3 h-100">
                <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line-fill text-primary"></i> Promedio de Calificación por Grupo
                </h5>
                <div class="chart-container">
                    <canvas id="chartPromedioGrupos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card chart-card p-3 h-100">
                <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-success"></i> Estado de Alumnos: Aprobados vs Reprobados
                </h5>
                <div class="chart-container">
                    <canvas id="chartAprobados"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card chart-card p-3 h-100">
                <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up text-info"></i> Asistencia Promedio (%) por Grupo
                </h5>
                <div class="chart-container">
                    <canvas id="chartAsistenciaGrupos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card chart-card p-3 h-100">
                <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-gender-ambiguous text-danger"></i> Distribución de Matrícula por Género
                </h5>
                <div class="chart-container">
                    <canvas id="chartGenero"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Grupos y Alumnos -->
    <div class="card chart-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-secondary mb-0"><i class="bi bi-table text-primary"></i> Detalle por Grupo Académico</h5>
            <span class="text-muted small">6 Grupos Registrados</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Grupo</th>
                        <th>Total Alumnos</th>
                        <th>Promedio Calificación</th>
                        <th>Asistencia Promedio</th>
                        <th>Tareas Promedio</th>
                        <th>Aprobados (&ge; 70)</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grupos as $k => $g): 
                        $prom = round($g['sumaCalif'] / $g['alumnos'], 1);
                        $asist = round($g['sumaAsistencia'] / $g['alumnos'], 1);
                        $tar = round($g['sumaTareas'] / $g['alumnos'], 1);
                        $porcApr = round(($g['aprobados'] / $g['alumnos']) * 100);
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($k) ?></td>
                        <td><?= $g['alumnos'] ?></td>
                        <td class="fw-bold text-primary"><?= $prom ?> pts</td>
                        <td><?= $asist ?>%</td>
                        <td><?= $tar ?> / 20</td>
                        <td><?= $g['aprobados'] ?> (<?= $porcApr ?>%)</td>
                        <td>
                            <?php if ($prom >= 75): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Excelente</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Regular</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Formulario Oculto para Enviar Imágenes de Gráficas a DomPDF -->
<form id="pdfExportForm" method="POST" action="exportar_pdf.php" target="_blank" style="display: none;">
    <input type="hidden" name="chart_promedio_grupos" id="input_chart_promedio_grupos">
    <input type="hidden" name="chart_aprobados" id="input_chart_aprobados">
    <input type="hidden" name="chart_asistencia_grupos" id="input_chart_asistencia_grupos">
    <input type="hidden" name="chart_genero" id="input_chart_genero">
</form>

<script>
// Configuración de Gráficas Chart.js
Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
Chart.defaults.font.size = 12;

// 1. Promedio por Grupo (Barras)
const ctx1 = document.getElementById('chartPromedioGrupos').getContext('2d');
const chart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?= json_encode($gruposLabels) ?>,
        datasets: [{
            label: 'Promedio (0-100)',
            data: <?= json_encode($gruposPromedios) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.75)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1.5,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false, // Vital para exportar inmediatamente sin esperar animación
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

// 2. Aprobados vs Reprobados (Dona)
const ctx2 = document.getElementById('chartAprobados').getContext('2d');
const chart2 = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Aprobados (>= 70)', 'Reprobados (< 70)'],
        datasets: [{
            data: [<?= $aprobados ?>, <?= $reprobados ?>],
            backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.8)'],
            borderColor: ['#fff', '#fff'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false
    }
});

// 3. Asistencia por Grupo (Línea)
const ctx3 = document.getElementById('chartAsistenciaGrupos').getContext('2d');
const chart3 = new Chart(ctx3, {
    type: 'line',
    data: {
        labels: <?= json_encode($gruposLabels) ?>,
        datasets: [{
            label: 'Asistencia (%)',
            data: <?= json_encode($gruposAsistencias) ?>,
            borderColor: 'rgba(23, 162, 184, 1)',
            backgroundColor: 'rgba(23, 162, 184, 0.2)',
            fill: true,
            tension: 0.3,
            pointRadius: 5,
            pointBackgroundColor: 'rgba(23, 162, 184, 1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: false, min: 50, max: 100 }
        }
    }
});

// 4. Género (Pastel)
const ctx4 = document.getElementById('chartGenero').getContext('2d');
const chart4 = new Chart(ctx4, {
    type: 'pie',
    data: {
        labels: ['Femenino (F)', 'Masculino (M)'],
        datasets: [{
            data: [<?= $generoConteo['F'] ?>, <?= $generoConteo['M'] ?>],
            backgroundColor: ['rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)'],
            borderColor: ['#fff', '#fff'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false
    }
});

// Manejo de la exportación a DomPDF
document.getElementById('btnExportarPdf').addEventListener('click', function() {
    const btn = this;
    const textoOriginal = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando PDF...';
    btn.disabled = true;

    // Convertir canvas a base64
    document.getElementById('input_chart_promedio_grupos').value = chart1.toBase64Image();
    document.getElementById('input_chart_aprobados').value = chart2.toBase64Image();
    document.getElementById('input_chart_asistencia_grupos').value = chart3.toBase64Image();
    document.getElementById('input_chart_genero').value = chart4.toBase64Image();

    // Enviar formulario
    document.getElementById('pdfExportForm').submit();

    setTimeout(() => {
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }, 1500);
});
</script>
</body>
</html>
