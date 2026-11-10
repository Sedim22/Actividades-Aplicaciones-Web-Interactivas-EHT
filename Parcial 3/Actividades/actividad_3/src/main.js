import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';
import './style.css';
import { chartDatasets } from './data.js';

// Guardamos referencias para permitir actualización dinámica
const instances = {
  chartjs: {},
  apex: {}
};

// Renderizar la interfaz estructural
function setupLayout() {
  const app = document.querySelector('#app');
  app.innerHTML = `
    <header class="header">
      <h1><span class="gradient-text">Chart.js</span> vs <span class="gradient-text">ApexCharts</span></h1>
    
      <div class="actions-bar">
        <button id="btn-randomize" class="btn-action btn-primary">
          <span>🎲</span> Regenerar Datos Aleatorios
        </button>
        <button id="btn-reset" class="btn-action">
          <span>🔄</span> Restaurar Originales
        </button>
      </div>

      <nav class="category-nav">
        <a href="#section-bar" class="nav-link">1. Barras</a>
        <a href="#section-line" class="nav-link">2. Líneas / Área</a>
        <a href="#section-pie" class="nav-link">3. Circular / Dona</a>
        <a href="#section-radar" class="nav-link">4. Radar</a>
        <a href="#section-scatter" class="nav-link">5. Dispersión</a>
      </nav>
    </header>

    <main class="container">
      <!-- 1. Barras -->
      <section id="section-bar" class="comparison-card">
        <div class="card-header">
          <div class="card-title-group">
            <span class="card-index">01</span>
            <div>
              <h2>${chartDatasets.bar.title}</h2>
              <div class="card-subtitle">${chartDatasets.bar.subtitle}</div>
            </div>
          </div>
          <div class="data-summary-badge">Métrica: 2 series × 6 categorías</div>
        </div>
        <div class="charts-grid">
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag chartjs"><span class="tech-dot"></span> Chart.js</span>
              <span class="chart-render-type">HTML5 Canvas</span>
            </div>
            <div class="chart-wrapper">
              <canvas id="chartjs-bar"></canvas>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Renderizado directo en bitmap vía Canvas 2D.</li>
                <li><span class="check">✓</span> Configuración limpia y directa mediante arreglos en datasets.</li>
              </ul>
            </div>
          </div>
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag apex"><span class="tech-dot"></span> ApexCharts</span>
              <span class="chart-render-type">SVG Interactivo</span>
            </div>
            <div class="chart-wrapper">
              <div id="apex-bar" class="apex-wrapper"></div>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Bordes redondeados integrados por defecto (border-radius).</li>
                <li><span class="check">✓</span> Menú superior con exportación a PNG/SVG y tooltips enriquecidos.</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. Líneas / Área -->
      <section id="section-line" class="comparison-card">
        <div class="card-header">
          <div class="card-title-group">
            <span class="card-index">02</span>
            <div>
              <h2>${chartDatasets.line.title}</h2>
              <div class="card-subtitle">${chartDatasets.line.subtitle}</div>
            </div>
          </div>
          <div class="data-summary-badge">Métrica: 2 series temporales × 7 puntos</div>
        </div>
        <div class="charts-grid">
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag chartjs"><span class="tech-dot"></span> Chart.js</span>
              <span class="chart-render-type">HTML5 Canvas</span>
            </div>
            <div class="chart-wrapper">
              <canvas id="chartjs-line"></canvas>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Tensión Bézier configurada (tension: 0.4) para curvas suaves.</li>
                <li><span class="check">✓</span> Relleno semi-transparente mediante configuración de background.</li>
              </ul>
            </div>
          </div>
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag apex"><span class="tech-dot"></span> ApexCharts</span>
              <span class="chart-render-type">SVG Interactivo</span>
            </div>
            <div class="chart-wrapper">
              <div id="apex-line" class="apex-wrapper"></div>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Curva suavizada nativa (curve: 'smooth') con degradado automático.</li>
                <li><span class="check">✓</span> Modos de zoom interactivo en eje X y eje Y de serie.</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 3. Circular / Dona -->
      <section id="section-pie" class="comparison-card">
        <div class="card-header">
          <div class="card-title-group">
            <span class="card-index">03</span>
            <div>
              <h2>${chartDatasets.pie.title}</h2>
              <div class="card-subtitle">${chartDatasets.pie.subtitle}</div>
            </div>
          </div>
          <div class="data-summary-badge">Métrica: 5 segmentos proporcionales (100%)</div>
        </div>
        <div class="charts-grid">
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag chartjs"><span class="tech-dot"></span> Chart.js</span>
              <span class="chart-render-type">HTML5 Canvas</span>
            </div>
            <div class="chart-wrapper">
              <canvas id="chartjs-pie"></canvas>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Animación de giro rotacional al cargar (animación Canvas).</li>
                <li><span class="check">✓</span> Soporte nativo para recorte con cutout / donut.</li>
              </ul>
            </div>
          </div>
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag apex"><span class="tech-dot"></span> ApexCharts</span>
              <span class="chart-render-type">SVG Interactivo</span>
            </div>
            <div class="chart-wrapper">
              <div id="apex-pie" class="apex-wrapper"></div>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Efecto hover de desprendimiento del segmento en SVG.</li>
                <li><span class="check">✓</span> Muestra valor total central configurable en donut sin plugins extra.</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Radar -->
      <section id="section-radar" class="comparison-card">
        <div class="card-header">
          <div class="card-title-group">
            <span class="card-index">04</span>
            <div>
              <h2>${chartDatasets.radar.title}</h2>
              <div class="card-subtitle">${chartDatasets.radar.subtitle}</div>
            </div>
          </div>
          <div class="data-summary-badge">Métrica: 2 perfiles comparados en 6 ejes polares</div>
        </div>
        <div class="charts-grid">
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag chartjs"><span class="tech-dot"></span> Chart.js</span>
              <span class="chart-render-type">HTML5 Canvas</span>
            </div>
            <div class="chart-wrapper">
              <canvas id="chartjs-radar"></canvas>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Escala radial con círculos o polígonos concéntricos.</li>
                <li><span class="check">✓</span> Relleno poligonal con transparencia y etiquetas en vértices.</li>
              </ul>
            </div>
          </div>
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag apex"><span class="tech-dot"></span> ApexCharts</span>
              <span class="chart-render-type">SVG Interactivo</span>
            </div>
            <div class="chart-wrapper">
              <div id="apex-radar" class="apex-wrapper"></div>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Marcadores SVG en cada nodo con transiciones fluidas.</li>
                <li><span class="check">✓</span> Resaltado interactivo de serie completa al pasar el mouse por la leyenda.</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. Dispersión -->
      <section id="section-scatter" class="comparison-card">
        <div class="card-header">
          <div class="card-title-group">
            <span class="card-index">05</span>
            <div>
              <h2>${chartDatasets.scatter.title}</h2>
              <div class="card-subtitle">${chartDatasets.scatter.subtitle}</div>
            </div>
          </div>
          <div class="data-summary-badge">Métrica: 2 clusters de coordenadas (X, Y)</div>
        </div>
        <div class="charts-grid">
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag chartjs"><span class="tech-dot"></span> Chart.js</span>
              <span class="chart-render-type">HTML5 Canvas</span>
            </div>
            <div class="chart-wrapper">
              <canvas id="chartjs-scatter"></canvas>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Desempeño óptimo para miles de puntos debido a Canvas.</li>
                <li><span class="check">✓</span> Coordenadas directas {x, y} en datasets independientes.</li>
              </ul>
            </div>
          </div>
          <div class="chart-column">
            <div class="chart-header">
              <span class="chart-tech-tag apex"><span class="tech-dot"></span> ApexCharts</span>
              <span class="chart-render-type">SVG Interactivo</span>
            </div>
            <div class="chart-wrapper">
              <div id="apex-scatter" class="apex-wrapper"></div>
            </div>
            <div class="features-footer">
              <ul>
                <li><span class="check">✓</span> Capacidad de zoom con caja de selección (box selection) en tiempo real.</li>
                <li><span class="check">✓</span> Tooltip SVG detallado con ejes formateados y efectos hover.</li>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </main>

  `;
}

// Configuración de tema oscuro compartido
const chartTheme = {
  fontFamily: 'Inter, sans-serif',
  textColor: '#94a3b8',
  gridColor: 'rgba(255, 255, 255, 0.08)'
};

// 1. GRÁFICO DE BARRAS
function initBarCharts(data) {
  // Chart.js
  const ctx = document.getElementById('chartjs-bar').getContext('2d');
  instances.chartjs.bar = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.categories,
      datasets: data.series.map(s => ({
        label: s.name,
        data: [...s.data],
        backgroundColor: s.color + 'cc',
        borderColor: s.color,
        borderWidth: 1.5,
        borderRadius: 6
      }))
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: chartTheme.textColor, font: { family: chartTheme.fontFamily } }
        }
      },
      scales: {
        x: {
          ticks: { color: chartTheme.textColor },
          grid: { color: chartTheme.gridColor }
        },
        y: {
          ticks: { color: chartTheme.textColor },
          grid: { color: chartTheme.gridColor }
        }
      }
    }
  });

  // ApexCharts
  const apexOptions = {
    chart: {
      type: 'bar',
      height: '100%',
      fontFamily: chartTheme.fontFamily,
      background: 'transparent',
      toolbar: { show: true }
    },
    theme: { mode: 'dark' },
    colors: data.series.map(s => s.color),
    series: data.series.map(s => ({
      name: s.name,
      data: [...s.data]
    })),
    xaxis: {
      categories: data.categories,
      labels: { style: { colors: chartTheme.textColor } },
      axisBorder: { color: chartTheme.gridColor },
      axisTicks: { color: chartTheme.gridColor }
    },
    yaxis: {
      labels: { style: { colors: chartTheme.textColor } }
    },
    grid: {
      borderColor: chartTheme.gridColor,
      strokeDashArray: 3
    },
    plotOptions: {
      bar: {
        borderRadius: 6,
        columnWidth: '55%'
      }
    },
    legend: {
      labels: { colors: chartTheme.textColor }
    }
  };
  instances.apex.bar = new ApexCharts(document.querySelector('#apex-bar'), apexOptions);
  instances.apex.bar.render();
}

// 2. GRÁFICO DE LÍNEAS / ÁREA
function initLineCharts(data) {
  // Chart.js
  const ctx = document.getElementById('chartjs-line').getContext('2d');
  instances.chartjs.line = new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.categories,
      datasets: data.series.map(s => ({
        label: s.name,
        data: [...s.data],
        borderColor: s.color,
        backgroundColor: s.color + '26',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 7
      }))
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { labels: { color: chartTheme.textColor, font: { family: chartTheme.fontFamily } } }
      },
      scales: {
        x: { ticks: { color: chartTheme.textColor }, grid: { color: chartTheme.gridColor } },
        y: { ticks: { color: chartTheme.textColor }, grid: { color: chartTheme.gridColor } }
      }
    }
  });

  // ApexCharts
  const apexOptions = {
    chart: {
      type: 'area',
      height: '100%',
      fontFamily: chartTheme.fontFamily,
      background: 'transparent',
      toolbar: { show: true }
    },
    theme: { mode: 'dark' },
    stroke: { curve: 'smooth', width: 3 },
    colors: data.series.map(s => s.color),
    series: data.series.map(s => ({
      name: s.name,
      data: [...s.data]
    })),
    xaxis: {
      categories: data.categories,
      labels: { style: { colors: chartTheme.textColor } },
      axisBorder: { color: chartTheme.gridColor }
    },
    yaxis: {
      labels: { style: { colors: chartTheme.textColor } }
    },
    grid: {
      borderColor: chartTheme.gridColor,
      strokeDashArray: 3
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.5,
        opacityTo: 0.05,
        stops: [0, 90, 100]
      }
    },
    legend: { labels: { colors: chartTheme.textColor } }
  };
  instances.apex.line = new ApexCharts(document.querySelector('#apex-line'), apexOptions);
  instances.apex.line.render();
}

// 3. GRÁFICO CIRCULAR / DONUT
function initPieCharts(data) {
  // Chart.js
  const ctx = document.getElementById('chartjs-pie').getContext('2d');
  instances.chartjs.pie = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.labels,
      datasets: [{
        data: [...data.data],
        backgroundColor: data.colors,
        borderColor: '#131b2e',
        borderWidth: 3,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: { color: chartTheme.textColor, font: { family: chartTheme.fontFamily } }
        }
      },
      cutout: '60%'
    }
  });

  // ApexCharts
  const apexOptions = {
    chart: {
      type: 'donut',
      height: '100%',
      fontFamily: chartTheme.fontFamily,
      background: 'transparent'
    },
    theme: { mode: 'dark' },
    series: [...data.data],
    labels: data.labels,
    colors: data.colors,
    stroke: { show: true, colors: ['#131b2e'], width: 3 },
    legend: {
      position: 'right',
      labels: { colors: chartTheme.textColor }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '60%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Total',
              color: '#f1f5f9'
            }
          }
        }
      }
    }
  };
  instances.apex.pie = new ApexCharts(document.querySelector('#apex-pie'), apexOptions);
  instances.apex.pie.render();
}

// 4. GRÁFICO RADAR
function initRadarCharts(data) {
  // Chart.js
  const ctx = document.getElementById('chartjs-radar').getContext('2d');
  instances.chartjs.radar = new Chart(ctx, {
    type: 'radar',
    data: {
      labels: data.categories,
      datasets: data.series.map(s => ({
        label: s.name,
        data: [...s.data],
        borderColor: s.color,
        backgroundColor: s.color + '33',
        pointBackgroundColor: s.color,
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: s.color
      }))
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { labels: { color: chartTheme.textColor, font: { family: chartTheme.fontFamily } } }
      },
      scales: {
        r: {
          angleLines: { color: chartTheme.gridColor },
          grid: { color: chartTheme.gridColor },
          pointLabels: { color: chartTheme.textColor, font: { size: 12 } },
          ticks: { color: chartTheme.textColor, backdropColor: 'transparent' },
          suggestedMin: 0,
          suggestedMax: 100
        }
      }
    }
  });

  // ApexCharts
  const apexOptions = {
    chart: {
      type: 'radar',
      height: '100%',
      fontFamily: chartTheme.fontFamily,
      background: 'transparent',
      toolbar: { show: true }
    },
    theme: { mode: 'dark' },
    series: data.series.map(s => ({
      name: s.name,
      data: [...s.data]
    })),
    colors: data.series.map(s => s.color),
    xaxis: {
      categories: data.categories,
      labels: { style: { colors: [chartTheme.textColor] } }
    },
    yaxis: {
      show: true,
      min: 0,
      max: 100,
      tickAmount: 4
    },
    markers: {
      size: 4,
      hover: { size: 7 }
    },
    stroke: { width: 2 },
    fill: { opacity: 0.2 },
    legend: { labels: { colors: chartTheme.textColor } }
  };
  instances.apex.radar = new ApexCharts(document.querySelector('#apex-radar'), apexOptions);
  instances.apex.radar.render();
}

// 5. GRÁFICO DE DISPERSIÓN (SCATTER)
function initScatterCharts(data) {
  // Chart.js
  const ctx = document.getElementById('chartjs-scatter').getContext('2d');
  instances.chartjs.scatter = new Chart(ctx, {
    type: 'scatter',
    data: {
      datasets: data.series.map(s => ({
        label: s.name,
        data: s.data.map(pt => ({ ...pt })),
        backgroundColor: s.color,
        borderColor: s.color,
        pointRadius: 6,
        pointHoverRadius: 9
      }))
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { labels: { color: chartTheme.textColor, font: { family: chartTheme.fontFamily } } }
      },
      scales: {
        x: {
          title: { display: true, text: 'Horas de Estudio', color: chartTheme.textColor },
          ticks: { color: chartTheme.textColor },
          grid: { color: chartTheme.gridColor }
        },
        y: {
          title: { display: true, text: 'Calificación', color: chartTheme.textColor },
          ticks: { color: chartTheme.textColor },
          grid: { color: chartTheme.gridColor },
          min: 40,
          max: 105
        }
      }
    }
  });

  // ApexCharts
  const apexOptions = {
    chart: {
      type: 'scatter',
      height: '100%',
      fontFamily: chartTheme.fontFamily,
      background: 'transparent',
      toolbar: { show: true }
    },
    theme: { mode: 'dark' },
    colors: data.series.map(s => s.color),
    series: data.series.map(s => ({
      name: s.name,
      data: s.data.map(pt => [pt.x, pt.y])
    })),
    xaxis: {
      title: { text: 'Horas de Estudio', style: { color: chartTheme.textColor } },
      labels: { style: { colors: chartTheme.textColor } },
      axisBorder: { color: chartTheme.gridColor }
    },
    yaxis: {
      title: { text: 'Calificación', style: { color: chartTheme.textColor } },
      labels: { style: { colors: chartTheme.textColor } },
      min: 40,
      max: 105
    },
    grid: {
      borderColor: chartTheme.gridColor,
      strokeDashArray: 3
    },
    markers: {
      size: 7,
      hover: { size: 10 }
    },
    legend: { labels: { colors: chartTheme.textColor } }
  };
  instances.apex.scatter = new ApexCharts(document.querySelector('#apex-scatter'), apexOptions);
  instances.apex.scatter.render();
}

// Inicializar todos los gráficos
function initAllCharts(data) {
  initBarCharts(data.bar);
  initLineCharts(data.line);
  initPieCharts(data.pie);
  initRadarCharts(data.radar);
  initScatterCharts(data.scatter);
}

// Función para actualizar ambos con nuevos datos
function updateAllCharts(newData) {
  // 1. Barras
  instances.chartjs.bar.data.datasets.forEach((ds, idx) => {
    ds.data = [...newData.bar.series[idx].data];
  });
  instances.chartjs.bar.update();
  instances.apex.bar.updateSeries(newData.bar.series.map(s => ({ name: s.name, data: [...s.data] })));

  // 2. Líneas
  instances.chartjs.line.data.datasets.forEach((ds, idx) => {
    ds.data = [...newData.line.series[idx].data];
  });
  instances.chartjs.line.update();
  instances.apex.line.updateSeries(newData.line.series.map(s => ({ name: s.name, data: [...s.data] })));

  // 3. Pie
  instances.chartjs.pie.data.datasets[0].data = [...newData.pie.data];
  instances.chartjs.pie.update();
  instances.apex.pie.updateSeries([...newData.pie.data]);

  // 4. Radar
  instances.chartjs.radar.data.datasets.forEach((ds, idx) => {
    ds.data = [...newData.radar.series[idx].data];
  });
  instances.chartjs.radar.update();
  instances.apex.radar.updateSeries(newData.radar.series.map(s => ({ name: s.name, data: [...s.data] })));

  // 5. Scatter
  instances.chartjs.scatter.data.datasets.forEach((ds, idx) => {
    ds.data = newData.scatter.series[idx].data.map(pt => ({ ...pt }));
  });
  instances.chartjs.scatter.update();
  instances.apex.scatter.updateSeries(newData.scatter.series.map(s => ({
    name: s.name,
    data: s.data.map(pt => [pt.x, pt.y])
  })));
}

// Generador de datos aleatorios para pruebas
function generateRandomData() {
  const rand = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
  
  return {
    bar: {
      categories: chartDatasets.bar.categories,
      series: chartDatasets.bar.series.map(s => ({
        ...s,
        data: s.data.map(() => rand(20, 95))
      }))
    },
    line: {
      categories: chartDatasets.line.categories,
      series: chartDatasets.line.series.map(s => ({
        ...s,
        data: s.data.map(() => rand(15, 95))
      }))
    },
    pie: {
      ...chartDatasets.pie,
      data: chartDatasets.pie.data.map(() => rand(10, 80))
    },
    radar: {
      categories: chartDatasets.radar.categories,
      series: chartDatasets.radar.series.map(s => ({
        ...s,
        data: s.data.map(() => rand(40, 99))
      }))
    },
    scatter: {
      series: chartDatasets.scatter.series.map(s => ({
        ...s,
        data: s.data.map(pt => ({
          x: pt.x + rand(-1, 1),
          y: Math.min(100, Math.max(45, pt.y + rand(-8, 8)))
        }))
      }))
    }
  };
}

// Iniciar aplicación
document.addEventListener('DOMContentLoaded', () => {
  setupLayout();
  initAllCharts(chartDatasets);

  // Manejadores de botones
  const btnRandom = document.getElementById('btn-randomize');
  const btnReset = document.getElementById('btn-reset');

  btnRandom?.addEventListener('click', () => {
    const randomized = generateRandomData();
    updateAllCharts(randomized);
  });

  btnReset?.addEventListener('click', () => {
    updateAllCharts(chartDatasets);
  });
});
