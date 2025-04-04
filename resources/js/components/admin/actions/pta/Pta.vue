<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-5 bg-gray-50 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace actions, vous pourriez voir et gérer les
                        informations sur les actions, que ce soit Audit Interne
                        ou PTA, d'y ajouter et encore plus.
                    </p>
                </div>

                <!-- Choix d'action à faire(A.I ou PTA) -->
                <div class="flex w-full mt-5 ml-4 justify-center space-x-4">
                    <!-- Lien Audit Interne -->
                    <router-link
                        to="/admin/actions/auditinterne"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/admin/actions/auditinterne',
                        }"
                    >
                        Audit Interne
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien PTA -->
                    <router-link
                        to="/admin/actions/pta"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/admin/actions/pta',
                        }"
                    >
                        PTA
                    </router-link>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Bouton d'ajout -->
                    <router-link to="/admin/actions/pta/ajouter">
                        <button
                            class="flex items-center justify-center bg-[#0062ff] text-white px-4 py-2 rounded-md w-38"
                        >
                            <Plus class="w-5 h-5 mr-2" /> Ajouter
                        </button></router-link
                    >

                    <!-- Barre de recherche -->
                    <div
                        class="flex items-center ml-6 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                    >
                        <Search class="w-5 h-5 mr-2 text-gray-500" />
                        <input
                            type="text"
                            placeholder="Rechercher...."
                            class="outline-none bg-transparent text-gray-800 placeholder-gray-500"
                        />
                    </div>
                    <button
                        class="flex items-center justify-center border border-gray-400 ml-4 transparent text-black px-4 py-2 rounded-md w-38"
                    >
                        Importer
                    </button>
                    <div class="relative">
                        <!-- Bouton Exporter -->
                        <button
                            @click="toggleExportMenu"
                            class="flex items-center justify-center border border-gray-400 ml-4 text-black px-4 py-2 rounded-md w-38"
                        >
                            Exporter
                        </button>

                        <!-- Menu déroulant pour les options d'exportation -->
                        <div
                            v-if="showExportMenu"
                            class="absolute mt-2 right-0 bg-white border border-gray-300 rounded-md shadow-lg w-40"
                        >
                            <button
                                @click="exportToExcel"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-green-600"
                            >
                                Exporter en Excel
                            </button>
                            <button
                                @click="exportToPdf"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600"
                            >
                                Exporter en PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tableau PTA -->
                <div class="mt-5 ml-4">
                    <!-- Conteneur pour le défilement horizontal -->
                    <div class="overflow-x-auto">
                        <table
                            class="table-fixed w-full h-[11.5rem] border-separate border-spacing-y-2"
                        >
                            <thead class="bg-gray-200 text-lg h-[2.5rem]">
                                <tr>
                                    <!-- Checkbox pour sélectionner tous les éléments -->
                                    <th class="w-10">
                                        <input
                                            type="checkbox"
                                            @change="toggleSelectAll"
                                            v-model="selectAll"
                                            class="form-checkbox h-5 w-5 text-blue-500"
                                        />
                                    </th>
                                    <th class="w-3">N°</th>
                                    <th>Date</th>
                                    <th>Sources</th>
                                    <th>Type Actions</th>
                                    <th>Fréquence</th>
                                    <th>DNS</th>
                                    <th>Suivi</th>
                                    <th class="w-40">Action</th>
                                    <th>Mesure</th>
                                    <th>Date suivi</th>
                                    <th class="w-40">Obsérvation</th>
                                    <th>Statut</th>
                                    <th class="w-56">-</th>
                                </tr>
                            </thead>
                            <tbody class="font-poppins text-center">
                                <tr
                                    v-for="(item, index) in tableData"
                                    :key="index"
                                    :class="{
                                        'bg-green-300 text-black':
                                            item.statut === 'En cours',
                                        'bg-red-300 text-black':
                                            item.statut === 'En retard',
                                        'bg-gray-300 text-black':
                                            item.statut === 'Clôturé',
                                        'bg-purple-300 text-black':
                                            item.statut === 'Abandonné',
                                    }"
                                    class="shadow-md rounded-md"
                                >
                                    <!-- Checkbox pour chaque ligne -->
                                    <td>
                                        <input
                                            type="checkbox"
                                            v-model="selectedItems"
                                            :value="item.numero"
                                            class="form-checkbox h-5 w-5 text-blue-500"
                                        />
                                    </td>
                                    <td>{{ item.numero }}</td>
                                    <td>{{ item.date }}</td>
                                    <td>{{ item.sources }}</td>
                                    <td>{{ item.typeactions }}</td>
                                    <td>{{ item.frequence }}</td>
                                    <td class="truncate">
                                        {{ item.dns }}
                                    </td>
                                    <td>{{ item.suivi }}</td>
                                    <td
                                        class="flex p-4 items-center space-x-2 underline"
                                    >
                                        <span
                                            :class="{
                                                'text-red-500':
                                                    item.actions ===
                                                    'Non réalisé',
                                                'text-green-500':
                                                    item.actions === 'Réalisé',
                                                'text-purple-500':
                                                    item.actions ===
                                                    'Abandonné',
                                            }"
                                        >
                                            <template
                                                v-if="
                                                    item.actions ===
                                                    'Non réalisé'
                                                "
                                            >
                                                <X />
                                            </template>
                                            <template
                                                v-else-if="
                                                    item.actions === 'Réalisé'
                                                "
                                            >
                                                <Check />
                                            </template>
                                            <template
                                                v-else-if="
                                                    item.actions === 'Abandonné'
                                                "
                                            >
                                                <Ban />
                                            </template>
                                        </span>
                                        <span>{{ item.actions }}</span>
                                    </td>
                                    <td>{{ item.mesure }}</td>
                                    <td>{{ item.datesuivi }}</td>
                                    <td class="truncate">
                                        {{ item.observation }}
                                    </td>
                                    <td>
                                        {{ item.statut }}
                                    </td>
                                    <td class="space-x-2 items-center">
                                        <button
                                            type="button"
                                            class="text-blue-600"
                                        >
                                            <router-link :to="item.viewLink"
                                                >Voir</router-link
                                            >
                                        </button>
                                        <button
                                            type="button"
                                            class="text-green-600"
                                        >
                                            <router-link :to="item.editLink"
                                                >Editer</router-link
                                            >
                                        </button>
                                        <button
                                            type="button"
                                            class="text-red-600"
                                        >
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex w-full mt-5 justify-between">
                    <!-- Résultat -->
                    <div
                        class="flex items-center text-gray-500 justify-start px-4 space-x-2"
                    >
                        <span>Résultat</span>
                        <strong>1-10</strong>
                        <span>sur</span>
                        <strong>50</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-4">
                        <!-- Bouton Précédent -->
                        <button
                            class="flex items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                        >
                            <ChevronLeft class="w-5 h-5" /> Préc.
                        </button>

                        <!-- Pagination -->
                        <div class="flex items-center space-x-2">
                            <button
                                class="px-3 py-1 rounded-md bg-gray-200 text-black font-medium"
                            >
                                1
                            </button>
                            <span class="text-gray-500">...</span>
                            <button
                                class="px-3 py-1 rounded-md bg-gray-200 text-black font-medium"
                            >
                                5
                            </button>
                        </div>

                        <!-- Bouton Suivant -->
                        <button
                            class="flex items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                        >
                            Suiv. <ChevronRight class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script>
export const ptaData = ref([
    {
        sources: "PTA",
        dns: "Planifier les réunions",
        datesuivi: "15/11/2024",
    },
]);
</script>

<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import {
    Info,
    Plus,
    Search,
    ChevronLeft,
    ChevronRight,
    X,
    Check,
    Ban,
} from "lucide-vue-next";

import { ref } from "vue";

const tableData = ref([
    {
        numero: "1",
        date: "12/10/2024",
        sources: "PTA",
        typeactions: "Action à planifier",
        frequence: "Mensuel",
        dns: "Planifier les réunions",
        suivi: "Dominique",
        actions: "Non réalisé",
        mesure: "Alerte",
        datesuivi: "15/11/2024",
        observation: "C'est pas un problème très grave",
        statut: "En cours",
        viewLink: "/admin/actions/pta/voir",
        editLink: "/admin/actions/pta/editer",
    },
    {
        numero: "2",
        date: "10/10/2024",
        sources: "P24",
        typeactions: "Action à planifier",
        frequence: "Mensuel",
        dns: "Exploiter les informations",
        suivi: "Nasandratra",
        actions: "Non réalisé",
        mesure: "Alerte",
        datesuivi: "13/10/2025",
        observation: "C'est pas un problème assez grave",
        statut: "En retard",
        viewLink: "/admin/actions/pta/voir",
        editLink: "/admin/actions/pta/editer",
    },
    {
        numero: "3",
        date: "08/10/2024",
        sources: "P24",
        typeactions: "Action à planifier",
        frequence: "Mensuel",
        dns: "Renforcer la coordination",
        suivi: "Malala",
        actions: "Abandonné",
        mesure: "Alerte",
        datesuivi: "10/10/2024",
        observation: "C'est pas un problème grave",
        statut: "Abandonné",
        viewLink: "/admin/actions/pta/voir",
        editLink: "/admin/actions/pta/editer",
    },
]);

const selectedItems = ref([]); // Liste des éléments sélectionnés
const selectAll = ref(false); // État de la case "Tout sélectionner"

// Fonction pour sélectionner ou désélectionner tous les éléments
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedItems.value = tableData.value.map((item) => item.code);
    } else {
        selectedItems.value = [];
    }
};

const showExportMenu = ref(false); // État pour afficher ou masquer le menu

// Fonction pour basculer l'affichage du menu
const toggleExportMenu = () => {
    showExportMenu.value = !showExportMenu.value;
};

// Fonction pour exporter en Excel
const exportToExcel = () => {
    console.log("Exportation en Excel...");
    // Ajoutez ici la logique pour exporter en Excel
    showExportMenu.value = false; // Masquer le menu après l'action
};

// Fonction pour exporter en PDF
const exportToPdf = () => {
    console.log("Exportation en PDF...");
    // Ajoutez ici la logique pour exporter en PDF
    showExportMenu.value = false; // Masquer le menu après l'action
};
</script>
