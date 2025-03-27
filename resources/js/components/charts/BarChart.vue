<!-- filepath: h:\Salama_TSAA\resources\js\components\charts\BarChart.vue -->
<template>
    <div>
        <Chart type="bar" :data="chartData" :options="chartOptions" />
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

// Register Chart.js components
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

// Chart data
const chartData = {
    labels: ["A.A", "N.R", "R", "R.P"], // Horizontal labels
    datasets: [
        {
            label: "Constat",
            data: [10, 20, 30, 40, 50], // Vertical values
            backgroundColor: ["#0062ff", "#ff6384", "#36a2eb", "#ffcd56"], // Bar colors
        },
    ],
};

// Chart options
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => `${value}%`, // Add percentage to y-axis labels
            },
        },
    },
};
</script>

<style scoped>
/* Optional: Add custom styles for the chart container */
</style>
