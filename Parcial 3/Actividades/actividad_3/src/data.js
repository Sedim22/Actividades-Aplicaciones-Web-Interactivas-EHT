// Dataset centralizado para garantizar que ambos frameworks reciban exactamente los mismos datos

export const chartDatasets = {
  // 1. Barras: Ventas mensuales por trimestre (comparativa directa)
  bar: {
    title: 'Gráfico de Barras (Columnas)',
    subtitle: 'Ventas y Ganancias por Mes (1er Semestre)',
    categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
    series: [
      {
        name: 'Ventas (k$)',
        data: [45, 52, 38, 65, 74, 82],
        color: '#38bdf8'
      },
      {
        name: 'Ganancias (k$)',
        data: [25, 30, 20, 42, 48, 55],
        color: '#818cf8'
      }
    ]
  },

  // 2. Líneas: Tendencia temporal continua con puntos
  line: {
    title: 'Gráfico de Líneas con Área Suave',
    subtitle: 'Rendimiento y Tráfico de Servidores en tiempo real',
    categories: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
    series: [
      {
        name: 'Servidor Alpha',
        data: [31, 40, 28, 51, 42, 85, 77],
        color: '#00e396'
      },
      {
        name: 'Servidor Beta',
        data: [11, 32, 45, 32, 34, 52, 41],
        color: '#feb019'
      }
    ]
  },

  // 3. Pastel / Dona: Distribución porcentual
  pie: {
    title: 'Gráfico Circular / Dona (Donut)',
    subtitle: 'Distribución de Cuota de Mercado por Navegador',
    labels: ['Chrome', 'Safari', 'Edge', 'Firefox', 'Opera'],
    data: [58, 22, 10, 6, 4],
    colors: ['#38bdf8', '#ff6384', '#00e396', '#feb019', '#775dd0']
  },

  // 4. Radar / Araña: Análisis multidimensional de habilidades
  radar: {
    title: 'Gráfico Radar (Spider)',
    subtitle: 'Evaluación de Rendimiento de Desarrollador Full-Stack',
    categories: ['Frontend', 'Backend', 'DevOps', 'UI/UX', 'Testing', 'Bases de Datos'],
    series: [
      {
        name: 'Nivel Candidato A',
        data: [85, 90, 65, 70, 80, 88],
        color: '#ff4560'
      },
      {
        name: 'Nivel Candidato B',
        data: [65, 75, 88, 92, 70, 60],
        color: '#008ffb'
      }
    ]
  },

  // 5. Dispersión: Relación entre dos variables continuas (X vs Y)
  scatter: {
    title: 'Gráfico de Dispersión (Scatter Plot)',
    subtitle: 'Relación entre Horas de Estudio vs Calificación Obtenida',
    series: [
      {
        name: 'Grupo Matutino',
        color: '#ff6384',
        data: [
          { x: 5, y: 55 },
          { x: 8, y: 62 },
          { x: 12, y: 70 },
          { x: 15, y: 78 },
          { x: 18, y: 84 },
          { x: 22, y: 91 },
          { x: 25, y: 95 },
          { x: 28, y: 98 }
        ]
      },
      {
        name: 'Grupo Vespertino',
        color: '#00e396',
        data: [
          { x: 4, y: 50 },
          { x: 9, y: 58 },
          { x: 11, y: 65 },
          { x: 14, y: 72 },
          { x: 19, y: 80 },
          { x: 20, y: 85 },
          { x: 26, y: 89 },
          { x: 30, y: 94 }
        ]
      }
    ]
  }
}
