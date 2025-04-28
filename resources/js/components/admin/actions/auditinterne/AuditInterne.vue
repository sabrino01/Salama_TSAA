<script>
export const auditInterneData = ref([
    {
        sources: "Audit Interne",
        action: "Demande le statut",
        datesuivi: "14/04/2025",
    },
    {
        sources: "Audit Interne",
        action: "Exploiter les informations",
        datesuivi: "13/10/2025",
    },
    {
        sources: "Audit Interne",
        action: "Renforcer la coordination",
        datesuivi: "10/10/2024",
    },
]);
</script>

<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/Table.vue";
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

import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const actionsAI = ref([]);
const totalActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);

// Charger les types d'actions depuis l'API
const chargerActions = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/actions/auditinterne", {
            params: { page, search },
        });
        actionsAI.value = response.data.data;
        totalActions.value = response.data.total;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
    } catch (error) {
        toast.error(
            "Erreur lors du chargement de l'audit interne ou de l'action",
            error
        );
    }
};

// Générer les numéros de page pour la pagination
const pages = computed(() => {
    // Limitons à 5 numéros de page maximum pour éviter trop de boutons
    const totalPages = lastPage.value;
    const current = currentPage.value;
    let pageNumbers = [];

    if (totalPages <= 5) {
        // Si moins de 5 pages, affichons-les toutes
        for (let i = 1; i <= totalPages; i++) {
            pageNumbers.push(i);
        }
    } else {
        // Sinon, affichons les 5 pages pertinentes
        // Toujours inclure la première et la dernière page
        if (current <= 3) {
            // Début: 1, 2, 3, 4, ..., n
            pageNumbers = [1, 2, 3, 4, totalPages];
        } else if (current >= totalPages - 2) {
            // Fin: 1, ..., n-3, n-2, n-1, n
            pageNumbers = [
                1,
                totalPages - 4,
                totalPages - 3,
                totalPages - 2,
                totalPages,
            ];
        } else {
            // Milieu: n-2, n-1, n, n+1, n+2
            pageNumbers = [
                current - 2,
                current - 1,
                current,
                current + 1,
                current + 2,
            ];
        }
    }

    return pageNumbers;
});

// Fonction pour changer de page
const changerPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        chargerActions(page, searchQuery.value);
    }
};

// Fonction pour gérer la recherche
const rechercherActions = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    chargerActions(currentPage.value, searchQuery.value);
};

// Fonction pour supprimer une action
const supprimerAction = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer cette action pour Audit Interne ?",
        async () => {
            try {
                await axios.delete(`/api/actions/${id}`);
                toast.success("Action Audit Interne supprimée avec succès");
                chargerActions(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression de l'action de l'Audit Interne",
                    error
                );
            }
        }
    );
};

const voirAuditInterne = (id) => {
    router.push(`/admin/actions/auditinterne/voir/${id}`);
};

const editerAuditInterne = (id) => {
    router.push(`/admin/actions/auditinterne/editer/${id}`);
};
// Colonne pour la table
const columns = [
    { label: "N°", field: "num_actions" },
    { label: "Date", field: "date" },
    { label: "Action", field: "description" },
    { label: "Constat", field: "constat_libelle" },
    { label: "Frequence", field: "frequence" },
    { label: "Statut", field: "statut" },
    { label: "Ajouter par", field: "nom_utilisateur" },
];

// Actions pour la table
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirAuditInterne(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerAuditInterne(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerAction(row.id),
    },
];

const showExportMenu = ref(false); // État pour afficher ou masquer le menu exporter

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

// Fonction pour charger les actions au démarrage
onMounted(() => {
    chargerActions(currentPage.value, searchQuery.value);
});
</script>

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
                    <router-link to="/admin/actions/auditinterne/ajouter">
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
                            v-model="searchQuery"
                            @input="rechercherActions"
                        />
                    </div>
                    <button
                        class="flex items-center justify-center border border-gray-400 ml-4 transparent text-black px-4 py-2 rounded-md w-38"
                    >
                        Importer
                    </button>
                    <div class="relative">
                        <button
                            @click="toggleExportMenu"
                            class="flex items-center justify-center border border-gray-400 ml-4 text-black px-4 py-2 rounded-md w-38"
                        >
                            Exporter
                        </button>

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

                <!-- Tableau des membres -->
                <div class="mt-5 ml-4">
                    <Table
                        :columns="columns"
                        :data="actionsAI"
                        :actions="actions"
                    />
                </div>

                <!-- Footer -->
                <div class="flex w-full mt-5 justify-between">
                    <!-- Résultat -->
                    <div
                        class="flex items-center text-gray-500 justify-start px-4 space-x-2"
                    >
                        <span>Résultat</span>
                        <strong>{{ (currentPage - 1) * perPage + 1 }}</strong>
                        <span>à</span>
                        <strong>
                            {{ Math.min(currentPage * perPage, totalActions) }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalActions }}</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-2">
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage === 1"
                            @click="changerPage(currentPage - 1)"
                        >
                            <ChevronLeft class="w-4 h-4" /> Préc.
                        </button>

                        <!-- Numéros de page -->
                        <div class="flex space-x-1">
                            <button
                                v-for="page in pages"
                                :key="page"
                                class="flex items-center justify-center w-8 h-8 rounded-md border"
                                :class="
                                    page === currentPage
                                        ? 'bg-[#0062ff] text-white'
                                        : 'bg-white text-black border-gray-300'
                                "
                                @click="changerPage(page)"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage >= lastPage"
                            @click="changerPage(currentPage + 1)"
                        >
                            Suiv. <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
