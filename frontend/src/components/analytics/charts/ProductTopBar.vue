<script setup>
import { computed, ref, onMounted, onUnmounted, nextTick } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import { Bar } from 'vue-chartjs';

// Registrar componentes de Chart.js
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    productsData: {
        type: Object,
        required: true,
        default: () => ({
            labels: [],
            data: []
        })
    }
});

// Referencias reactivas para el contenedor y dimensiones
const containerRef = ref(null);
const chartRef = ref(null);
const containerWidth = ref(0);
const containerHeight = ref(0);
const isInitialized = ref(false);

// DESIGN REFINEMENT: The color palette is excellent. No changes needed here.
const premiumColors = [
    { primary: '#3B82F6', secondary: '#1E40AF', accent: '#60A5FA' }, // Blue
    { primary: '#10B981', secondary: '#059669', accent: '#34D399' }, // Emerald
    { primary: '#F59E0B', secondary: '#D97706', accent: '#FBBF24' }, // Amber
    { primary: '#8B5CF6', secondary: '#7C3AED', accent: '#A78BFA' }, // Purple
    { primary: '#EF4444', secondary: '#DC2626', accent: '#F87171' }, // Red
    { primary: '#06B6D4', secondary: '#0891B2', accent: '#22D3EE' }, // Cyan
    { primary: '#EC4899', secondary: '#DB2777', accent: '#F472B6' }, // Pink
    { primary: '#84CC16', secondary: '#65A30D', accent: '#A3E635' }, // Lime
];

// Detectores de tamaño de pantalla (unchanged)
const isMobile = computed(() => containerWidth.value < 640);
const isTablet = computed(() => containerWidth.value >= 640 && containerWidth.value < 1024);
const isSmallContainer = computed(() => containerWidth.value < 400);

// Determinar orientación del gráfico (unchanged)
const shouldUseHorizontal = computed(() => {
    const hasLongLabels = props.productsData.labels.some(label => label.length > 12);
    const hasManyItems = props.productsData.labels.length > 6;
    return (isMobile.value && hasLongLabels) || (isSmallContainer.value && hasManyItems);
});

// Observer para responsive con debounce (unchanged)
const resizeObserver = ref(null);
let resizeTimeout = null;

const updateDimensions = () => {
    if (containerRef.value) {
        const rect = containerRef.value.getBoundingClientRect();
        containerWidth.value = rect.width;
        containerHeight.value = rect.height;
        if (chartRef.value?.chart && isInitialized.value) {
            nextTick(() => {
                chartRef.value.chart.resize();
            });
        }
    }
};

const debouncedResize = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateDimensions, 100);
};

onMounted(async () => {
    if (containerRef.value) {
        updateDimensions();
        resizeObserver.value = new ResizeObserver(debouncedResize);
        resizeObserver.value.observe(containerRef.value);
        await nextTick();
        isInitialized.value = true;
    }
});

onUnmounted(() => {
    if (resizeObserver.value) {
        resizeObserver.value.disconnect();
    }
    clearTimeout(resizeTimeout);
});

// DESIGN REFINEMENT: Created a more subtle and elegant gradient.
// Instead of fading to transparent, it transitions from the primary color to its darker 'secondary' counterpart, adding depth.
const createPremiumGradient = (ctx, colorSet) => {
    const chart = ctx.chart;
    const chartArea = chart.chartArea;
    if (!chartArea) return colorSet.primary;

    const canvas = chart.ctx;
    const isHorizontal = shouldUseHorizontal.value;

    const gradient = isHorizontal
        ? canvas.createLinearGradient(chartArea.left, 0, chartArea.right, 0)
        : canvas.createLinearGradient(0, chartArea.bottom, 0, chartArea.top); // Flipped for a "lit from above" effect

    gradient.addColorStop(0, colorSet.secondary); // Start with the darker shade for depth
    gradient.addColorStop(1, colorSet.primary);   // End with the lighter, primary shade

    return gradient;
};

// DESIGN REFINEMENT: Enhanced bar styling, spacing, and hover effects for a cleaner, more modern look.
const chartData = computed(() => {
    if (!props.productsData.labels.length) return { labels: [], datasets: [] };

    return {
        labels: props.productsData.labels,
        datasets: [
            {
                label: 'Ventas',
                data: props.productsData.data,
                backgroundColor: (ctx) => {
                    if (ctx.dataIndex === undefined) return premiumColors[0].primary;
                    const colorIndex = ctx.dataIndex % premiumColors.length;
                    return createPremiumGradient(ctx, premiumColors[colorIndex]);
                },
                hoverBackgroundColor: (ctx) => {
                    if (ctx.dataIndex === undefined) return premiumColors[0].accent;
                    const colorIndex = ctx.dataIndex % premiumColors.length;
                    return premiumColors[colorIndex].accent; // Use solid accent color for a clean hover
                },
                borderWidth: 0,
                borderRadius: shouldUseHorizontal.value
                    ? { topLeft: 0, topRight: 8, bottomLeft: 0, bottomRight: 8 } // Increased radius for softer edges
                    : { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                borderSkipped: false,
                maxBarThickness: isMobile.value ? 28 : isTablet.value ? 36 : 48, // Slightly slimmer bars
                minBarLength: 4, // Ensures even tiny values are visible
                barPercentage: 0.7, // Increased space between bars within a category
                categoryPercentage: 0.8, // Increased space between categories
            }
        ]
    };
});

// DESIGN REFINEMENT: Overhauled animations, tooltips, and scales for visual elegance and clarity.
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: shouldUseHorizontal.value ? 'y' : 'x',

    interaction: {
        intersect: false,
        mode: 'index'
    },


    animation: {
        duration: 600,
        easing: 'easeOutCubic',
        delay: (context) => {
            // Animación escalonada más rápida y fluida
            if (context.type === 'data' && context.dataIndex !== undefined) {
                return context.dataIndex * 60;
            }
            return 0;
        }
    },
    transitions: {
        active: {
            animation: {
                duration: 300
            }
        },
        resize: {
            animation: {
                duration: 400
            }
        }
    },

    layout: {
        padding: { // Adjusted padding for better balance
            left: shouldUseHorizontal.value ? 0 : 4,
            right: shouldUseHorizontal.value ? 12 : 4,
            top: 20,
            bottom: 4
        }
    },

    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            enabled: true,
            backgroundColor: 'rgba(15, 23, 42, 0.95)', // A slightly more solid dark slate background
            titleColor: '#F8FAFC',
            bodyColor: '#CBD5E1', // Softer body color for better contrast
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            cornerRadius: 12, // Larger radius for a modern feel
            padding: 14, // More internal spacing
            titleFont: {
                size: 14,
                weight: '600',
                family: '"Inter", system-ui, sans-serif'
            },
            bodyFont: {
                size: 13,
                weight: '500',
                family: '"Inter", system-ui, sans-serif'
            },
            displayColors: true,
            usePointStyle: true,
            pointStyle: 'circle',
            callbacks: {
                title: (tooltipItems) => tooltipItems[0].label,
                label: (context) => {
                    const value = context.parsed[shouldUseHorizontal.value ? 'x' : 'y'];
                    // Cleaner label format
                    return `Ventas: ${value.toLocaleString('es-CO')}`;
                },
                labelColor: (context) => {
                    const colorIndex = context.dataIndex % premiumColors.length;
                    return {
                        borderColor: premiumColors[colorIndex].accent,
                        backgroundColor: premiumColors[colorIndex].accent,
                        borderWidth: 2,
                        borderRadius: 4,
                    };
                }
            },
            caretSize: 8,
            caretPadding: 8,
            titleMarginBottom: 8,
        }
    },

    scales: {
        x: {
            type: shouldUseHorizontal.value ? 'linear' : 'category',
            ...(shouldUseHorizontal.value ? {
                beginAtZero: true,
                grace: '5%', // Ensures bar doesn't touch the edge
                grid: {
                    display: true,
                    color: 'rgba(148, 163, 184, 0.15)', // Subtle grid lines
                    drawBorder: false,
                    borderDash: [3, 5], // Dashed lines to reduce visual weight
                },
                border: { display: false },
                ticks: {
                    color: '#64748B', // Softer tick color (Slate 500)
                    font: {
                        size: 12,
                        weight: '500',
                        family: '"Inter", system-ui, sans-serif'
                    },
                    padding: 10,
                    maxTicksLimit: isMobile.value ? 5 : 7,
                    callback: (value) => value >= 1000 ? `${value / 1000}k` : value.toString(),
                }
            } : {
                grid: { display: false },
                border: { display: false },
                ticks: {
                    color: '#64748B',
                    font: {
                        size: 12,
                        weight: '500',
                        family: '"Inter", system-ui, sans-serif'
                    },
                    padding: 10,
                    maxRotation: 0,
                    minRotation: 0,
                    callback: function (value) {
                        const label = this.getLabelForValue(value);
                        const maxLength = isMobile.value ? 9 : isTablet.value ? 11 : 14;
                        return label.length > maxLength ? `${label.substring(0, maxLength - 1)}…` : label;
                    }
                }
            })
        },
        y: {
            type: shouldUseHorizontal.value ? 'category' : 'linear',
            ...(shouldUseHorizontal.value ? {
                grid: { display: false },
                border: { display: false },
                ticks: {
                    color: '#64748B',
                    font: {
                        size: 12,
                        weight: '500',
                        family: '"Inter", system-ui, sans-serif'
                    },
                    padding: 10,
                    callback: function (value) {
                        const label = this.getLabelForValue(value);
                        const maxLength = isMobile.value ? 14 : 18;
                        return label.length > maxLength ? `${label.substring(0, maxLength - 1)}…` : label;
                    }
                }
            } : {
                beginAtZero: true,
                grace: '5%',
                grid: {
                    display: true,
                    color: 'rgba(148, 163, 184, 0.15)',
                    drawBorder: false,
                    borderDash: [3, 5], // Dashed lines are less intrusive
                },
                border: { display: false },
                ticks: {
                    color: '#64748B',
                    font: {
                        size: 12,
                        weight: '500',
                        family: '"Inter", system-ui, sans-serif'
                    },
                    padding: 10,
                    maxTicksLimit: isMobile.value ? 5 : 7,
                    callback: (value) => value >= 1000 ? `${value / 1000}k` : value.toString(),
                }
            })
        }
    },

    onHover: (event, elements) => {
        const canvas = event.native.target;
        canvas.style.cursor = elements.length > 0 ? 'pointer' : 'default';
        // Add a subtle transition for the cursor change
        canvas.style.transition = 'cursor 150ms ease-out';
    },
}));
</script>

<template>
    <div ref="containerRef" class="bar-chart-container">
        <div class="chart-wrapper">
            <Bar ref="chartRef" :data="chartData" :options="chartOptions" class="chart-instance" />
        </div>
    </div>
</template>

<style scoped>
@reference '../../../style.css';

.bar-chart-container {
    @apply w-full h-full relative;
    min-height: 300px;
    min-width: 280px;
    background: transparent;
    padding: 0;
}

.chart-wrapper {
    @apply w-full h-full relative;
    min-height: 260px;
    height: 100%;
}

.chart-instance {
    @apply w-full h-full;
    position: relative !important;
}

/* Responsive */
@media (max-width: 400px) {
    .chart-wrapper {
        min-height: 240px;
    }
}

@media (min-width: 401px) and (max-width: 640px) {
    .chart-wrapper {
        min-height: 280px;
    }
}

@media (min-width: 641px) {
    .chart-wrapper {
        min-height: 320px;
    }
}

/* No-op hover effects on the container */
.bar-chart-container,
.bar-chart-container:hover {
    transform: none;
    box-shadow: none;
    animation: none;
}

/* Canvas responsiveness fix */
.bar-chart-container :deep(canvas) {
    max-width: 100% !important;
    max-height: 100% !important;
    width: 100% !important;
    height: 100% !important;
}
</style>
