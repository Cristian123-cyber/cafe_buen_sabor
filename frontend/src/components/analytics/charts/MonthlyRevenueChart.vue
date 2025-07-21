<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler // <-- MUY IMPORTANTE: Importar 'Filler' para el degradado de área
} from 'chart.js';

// Registrar todos los componentes necesarios de Chart.js
ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler
);

const props = defineProps({
  // El componente recibe los datos ya formateados a través de props
  chartData: {
    type: Object,
    required: true,
    validator: (value) => 
      value && Array.isArray(value.labels) && Array.isArray(value.data)
  }
});

// Opciones de configuración para el gráfico, con un diseño premium
const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      enabled: true,
      backgroundColor: 'rgba(255, 255, 255, 0.95)',
      titleColor: '#1f2937',
      bodyColor: '#4b5563',
      titleFont: { size: 14, weight: 'bold', family: 'Plus Jakarta Sans' },
      bodyFont: { size: 13, family: 'Plus Jakarta Sans' },
      padding: 16,
      cornerRadius: 12,
      borderColor: 'rgba(229, 231, 235, 0.8)',
      borderWidth: 1,
      displayColors: false,
      shadowOffsetX: 0,
      shadowOffsetY: 4,
      shadowBlur: 12,
      shadowColor: 'rgba(0, 0, 0, 0.1)',
      callbacks: {
        label: (context) => `Ingresos: ${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(context.raw)}`
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: { 
        color: '#6b7280',
        font: {
          size: 12,
          family: 'Plus Jakarta Sans',
          weight: '500'
        },
        padding: 12
      },
      border: {
        display: false
      }
    },
    y: {
      border: {
        display: false
      },
      grid: {
        color: 'rgba(229, 231, 235, 0.6)',
        lineWidth: 1,
        drawBorder: false
      },
      ticks: {
        color: '#6b7280',
        font: {
          size: 12,
          family: 'Plus Jakarta Sans',
          weight: '500'
        },
        padding: 16,
        callback: (value) => value >= 1000 ? `${value / 1000}k` : value
      }
    }
  },
  elements: {
    line: {
      tension: 0.4
    },
    point: {
      radius: 0,
      hitRadius: 12,
      hoverRadius: 8,
      hoverBorderWidth: 3,
      hoverBorderColor: '#ffffff',
      hoverBackgroundColor: '#3b82f6'
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  },
  animation: {
    duration: 1200,
    easing: 'easeInOutQuart'
  }
}));

// Datos del gráfico, configurados para usar el degradado
const internalChartData = computed(() => {
  return {
    labels: props.chartData.labels,
    datasets: [
      {
        label: 'Ingresos',
        borderColor: '#3b82f6',
        borderWidth: 3,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointHoverBackgroundColor: '#3b82f6',
        pointHoverBorderColor: '#ffffff',
        data: props.chartData.data,
        fill: true,
        backgroundColor: (context) => {
          const ctx = context.chart.ctx;
          if (!ctx) return null;
          const gradient = ctx.createLinearGradient(0, 0, 0, 400);
          gradient.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
          gradient.addColorStop(0.6, 'rgba(59, 130, 246, 0.05)');
          gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
          return gradient;
        }
      }
    ]
  };
});
</script>

<template>
  <!-- El componente <Line> de vue-chartjs se encarga de todo el renderizado -->
  <Line :options="chartOptions" :data="internalChartData" class="aspect-video" />
</template>

