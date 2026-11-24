<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Calificaciones y Métricas</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2d3748;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a202c;
            margin: 0;
            text-transform: uppercase;
        }
        .header-sub {
            font-size: 11px;
            color: #718096;
            margin: 3px 0 0 0;
        }
        .date-badge {
            text-align: right;
            font-size: 10px;
            color: #4a5568;
        }

        /* KPIs */
        .kpi-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .kpi-card {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .kpi-title {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #718096;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #2b6cb0;
        }
        .kpi-desc {
            font-size: 8px;
            color: #a0aec0;
        }

        /* Secciones */
        .section-heading {
            font-size: 13px;
            font-weight: bold;
            color: #2d3748;
            border-left: 4px solid #3182ce;
            padding-left: 8px;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }

        /* Gráficas */
        .charts-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .chart-box {
            width: 48%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }
        .chart-box img {
            max-width: 100%;
            height: auto;
            max-height: 190px;
        }
        .chart-caption {
            font-size: 10px;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 6px;
        }

        /* Tablas */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .data-table th {
            background-color: #edf2f7;
            color: #2d3748;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            border-bottom: 2px solid #cbd5e0;
        }
        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-danger { background-color: #fed7d7; color: #742a2a; }

        .page-break {
            page-break-before: always;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Sistema de Gestión Académica
    </div>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="header-title"><?= htmlspecialchars($titulo ?? 'Reporte de Calificaciones y Métricas') ?></h1>
                <p class="header-sub">Dashboard Ejecutivo y Analítica Académica (Unidad: <?= htmlspecialchars($unidad ?? 'Puntos') ?>)</p>
            </td>
            <td class="date-badge">
                <strong>Fecha:</strong> <?= date('d/m/Y H:i') ?><br>
                <strong>Total Evaluados:</strong> <?= $totalAlumnos ?> alumnos
            </td>
        </tr>
    </table>

    <!-- Tarjetas de Métricas (KPIs) -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Promedio General</div>
                <div class="kpi-value"><?= number_format($promedioGeneral, 1) ?></div>
                <div class="kpi-desc">Escala 0 - 100 pts</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Tasa de Aprobación</div>
                <div class="kpi-value" style="color: #2f855a;"><?= number_format($tasaAprobacion, 1) ?>%</div>
                <div class="kpi-desc"><?= $aprobados ?> de <?= $totalAlumnos ?> alumnos (>= 70)</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Asistencia Promedio</div>
                <div class="kpi-value" style="color: #6b46c1;"><?= number_format($promedioAsistencia, 1) ?>%</div>
                <div class="kpi-desc">Global de los 6 grupos</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Entrega de Tareas</div>
                <div class="kpi-value" style="color: #dd6b20;"><?= number_format($promedioTareas, 1) ?> / 20</div>
                <div class="kpi-desc"><?= number_format(($promedioTareas/20)*100, 1) ?>% de cumplimiento</div>
            </td>
        </tr>
    </table>

    <!-- Visualizaciones de Gráficas Exportadas -->
    <div class="section-heading">Visualizaciones del Dashboard</div>
    <table class="charts-table">
        <tr>
            <td class="chart-box">
                <div class="chart-caption">Promedio de Calificación por Grupo</div>
                <?php if (!empty($chartPromedioGrupos)): ?>
                    <img src="<?= $chartPromedioGrupos ?>" alt="Gráfica de Promedios">
                <?php else: ?>
                    <p style="color:#a0aec0; padding: 40px 0;">[Gráfica no disponible]</p>
                <?php endif; ?>
            </td>
            <td style="width: 4%;"></td>
            <td class="chart-box">
                <div class="chart-caption">Distribución: Aprobados vs Reprobados</div>
                <?php if (!empty($chartAprobados)): ?>
                    <img src="<?= $chartAprobados ?>" alt="Gráfica de Aprobación">
                <?php else: ?>
                    <p style="color:#a0aec0; padding: 40px 0;">[Gráfica no disponible]</p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 12px;"></td>
        </tr>
        <tr>
            <td class="chart-box">
                <div class="chart-caption">Asistencia Promedio (%) por Grupo</div>
                <?php if (!empty($chartAsistenciaGrupos)): ?>
                    <img src="<?= $chartAsistenciaGrupos ?>" alt="Gráfica de Asistencia">
                <?php else: ?>
                    <p style="color:#a0aec0; padding: 40px 0;">[Gráfica no disponible]</p>
                <?php endif; ?>
            </td>
            <td style="width: 4%;"></td>
            <td class="chart-box">
                <div class="chart-caption">Distribución por Género</div>
                <?php if (!empty($chartGenero)): ?>
                    <img src="<?= $chartGenero ?>" alt="Gráfica de Género">
                <?php else: ?>
                    <p style="color:#a0aec0; padding: 40px 0;">[Gráfica no disponible]</p>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Salto de Página para la Tabla Resumen -->
    <div class="page-break"></div>

    <div class="section-heading">Métricas y Resumen por Grupo</div>
    <table class="data-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Alumnos</th>
                <th>Promedio Calificación</th>
                <th>Asistencia Media</th>
                <th>Promedio Tareas</th>
                <th>Aprobados</th>
                <th>Estatus General</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metricasGrupos as $grp => $datos): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($grp) ?></strong></td>
                    <td><?= $datos['total'] ?></td>
                    <td><strong><?= number_format($datos['promedio'], 1) ?> pts</strong></td>
                    <td><?= number_format($datos['asistencia'], 1) ?>%</td>
                    <td><?= number_format($datos['tareas'], 1) ?> / 20</td>
                    <td><?= $datos['aprobados'] ?> (<?= number_format(($datos['aprobados']/$datos['total'])*100, 0) ?>%)</td>
                    <td>
                        <?php if ($datos['promedio'] >= 75): ?>
                            <span class="badge badge-success">Alto Rendimiento</span>
                        <?php else: ?>
                            <span class="badge badge-danger">En Observación</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="section-heading">Top 10 Alumnos con Mayor Desempeño</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Alumno</th>
                <th>Grupo</th>
                <th>Calificación</th>
                <th>Asistencia</th>
                <th>Tareas Entregadas</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topAlumnos as $idx => $alumno): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td><strong><?= htmlspecialchars($alumno['etiqueta']) ?></strong></td>
                    <td><?= htmlspecialchars($alumno['categoria']) ?></td>
                    <td><strong><?= $alumno['total'] ?> pts</strong></td>
                    <td><?= $alumno['asistencia'] ?>%</td>
                    <td><?= $alumno['tareas_entregadas'] ?> / 20</td>
                    <td><span class="badge badge-success">Aprobado</span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
