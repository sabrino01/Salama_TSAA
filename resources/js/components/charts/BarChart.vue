<template>
    <div>
        <Chart
            v-if="isDataValid"
            type="bar"
            :data="safeChartData"
            :options="chartOptions"
        />
        <div
            v-else
            class="flex items-center justify-center h-64 bg-gray-100 rounded-lg"
        >
            <p class="text-gray-500">En attente de données...</p>
        </div>
    </div>
</template>

<script setup>
import { Chart } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";
import { defineProps, computed } from "vue";

// Register Chart.js components
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    },
    // Ajout d'une prop optionnelle pour personnaliser le titre du graphique
    chartTitle: {
        type: String,
        default: "Statistiques des Constats",
    },
});

// Vérifier si les données sont valides pour le graphique
const isDataValid = computed(() => {
    return (
        props.chartData &&
        props.chartData.labels &&
        props.chartData.datasets &&
        props.chartData.datasets.length > 0 &&
        props.chartData.labels.length > 0
    );
});

// Créer un objet de données sécurisé pour le graphique
const safeChartData = computed(() => {
    if (!isDataValid.value) {
        return {
            labels: ["Aucune donnée"],
            datasets: [
                {
                    label: props.chartTitle,
                    data: [0],
                    backgroundColor: ["#cccccc"],
                },
            ],
        };
    }
    return props.chartData;
});

// Chart options avec titre personnalisable
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: {
            display: true,
            text: props.chartTitle,
            font: {
                size: 16,
            },
        },
        legend: {
            display: true,
            position: "top",
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    return `${context.dataset.label}: ${context.raw.toFixed(
                        1
                    )}%`;
                },
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => `${value}%`,
            },
            title: {
                display: true,
                text: "Pourcentage (%)",
            },
        },
        x: {
            title: {
                display: true,
                text: "Types de constat",
            },
        },
    },
};
</script>
