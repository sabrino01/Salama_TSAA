<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-5 bg-gray-100 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Notifications
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace Notifications, vous pouvez voir et gérer
                        les membres de votre association, ajouter des membres et
                        de les supprimer.
                    </p>
                </div>

                <!-- Tableau notifications par récente-->
                <div class="mt-5 ml-4">
                    <table class="table-fixed w-[60%] h-[11.5rem]">
                        <thead class="bg-gray-200 text-lg h-[2.5rem]">
                            <tr>
                                <th class="text-start">
                                    <button
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                    >
                                        <ChevronLeft class="w-5 h-5" />
                                    </button>
                                </th>
                                <th>
                                    <div
                                        class="items-center text-gray-500 justify-start px-4 space-x-2"
                                    >
                                        <strong>1-10</strong>
                                        <span>sur</span>
                                        <strong>50</strong>
                                    </div>
                                </th>
                                <th class="text-end">
                                    <button
                                        class="items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                    >
                                        <ChevronRight class="w-5 h-5" />
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="font-poppins text-center">
                            <tr
                                v-for="(item, index) in tableData"
                                :key="index"
                                :class="{
                                    'border-b-4 border-green-500 bg-white text-black':
                                        isFutureDate(item.datesuivi),
                                    'border-b-4 border-red-500 bg-white text-black':
                                        !isFutureDate(item.datesuivi),
                                }"
                                class="shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2"
                            >
                                <td class="px-4 py-2">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{ item.sources }}
                                    </router-link>
                                </td>
                                <td class="truncate px-4 py-4">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{
                                            item.sources === "Audit Interne"
                                                ? item.action
                                                : item.dns
                                        }}
                                    </router-link>
                                </td>
                                <td class="px-4 py-2">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{ item.datesuivi }}
                                    </router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "./assets/Sidebar.vue";
import Navbar from "./assets/Navbar.vue";
import Footer from "./assets/Footer.vue";
import { Info, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref } from "vue";

import { ptaData } from "./actions/pta/Pta.vue"; // Import des données depuis Pta.vue
import { auditInterneData } from "./actions/auditinterne/AuditInterne.vue"; // Import des données depuis AuditInterne.vue

const tableData = ref([
    {
        sources: "Audit Interne",
        action: "Demande le statut",
        datesuivi: "14/10/2025",
    },
    {
        sources: "Audit Interne",
        action: "Exploiter les informations",
        datesuivi: "13/10/2025",
    },
    {
        sources: "PTA",
        dns: "Planifier les réunions",
        datesuivi: "15/11/2024",
    },
]);

// Fonction pour convertir une date au format JJ/MM/YYYY en un objet Date
const parseDate = (dateString) => {
    const [day, month, year] = dateString.split("/").map(Number); // Sépare JJ/MM/YYYY
    return new Date(year, month - 1, day); // Crée un objet Date (mois commence à 0)
};

// Fonction pour vérifier si une date est dans le futur
const isFutureDate = (dateString) => {
    const date = parseDate(dateString); // Convertit la date
    return date > new Date(); // Compare avec la date actuelle
};

// Fonction pour vérifier si les données existent et générer le lien
const getSourceLink = (sources, actionOrDns, datesuivi) => {
    // Vérifie dans audit interne
    const existsInAuditInterne = auditInterneData.value.some(
        (auditItem) =>
            auditItem.sources === sources &&
            auditItem.action === actionOrDns &&
            auditItem.datesuivi === datesuivi
    );

    if (existsInAuditInterne) {
        return "/admin/actions/auditinterne";
    }

    // Vérifie dans PTA
    const existsInPta = ptaData.value.some(
        (ptaItem) =>
            ptaItem.sources === sources &&
            ptaItem.dns === actionOrDns &&
            ptaItem.datesuivi === datesuivi
    );

    if (existsInPta) {
        return "/admin/actions/pta";
    }

    // Redirection par défaut si aucune correspondance
    return "/";
};
</script>
