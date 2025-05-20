<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/Table.vue";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const typeActions = ref([]);
const totalTypeActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);

// Charger les types d'actions depuis l'API
const chargerTypeActions = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/typeactions", {
            params: { page, search },
        });
        typeActions.value = response.data.data;
        totalTypeActions.value = Number(response.data.total) || 0;
        currentPage.value = Number(response.data.current_page) || 1;
        lastPage.value = Number(response.data.last_page) || 1;
        perPage.value = Number(response.data.per_page) || 10;
    } catch (error) {
        toast.error("Erreur lors du chargement des types d'actions", error);
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
                totalPages - 3,
                totalPages - 2,
                totalPages - 1,
                totalPages,
            ];
        } else {
            // Milieu: ..., n-2, n-1, n, n+1, n+2
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
        chargerTypeActions(page, searchQuery.value);
    }
};

// Fonction pour gérer la recherche
const rechercherTypeActions = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    chargerTypeActions(currentPage.value, searchQuery.value);
};

// Fonction pour gérer la suppression
const supprimerTypeAction = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer ce type d'actions ?",
        async () => {
            try {
                await axios.delete(`/api/typeactions/${id}`);
                toast.success("Type d'action supprimé avec succès");
                chargerTypeActions(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression du type d'action",
                    error
                );
            }
        }
    );
};

const voirTypeAction = (id) => {
    router.push(`/admin/informations/typeactions/voir/${id}`);
};

const editerTypeAction = (id) => {
    router.push(`/admin/informations/typeactions/editer/${id}`);
};

// colonne pour le tableau
const columns = [
    { label: "Code", field: "code" },
    { label: "Libelle", field: "libelle" },
];

// Actions pour le tableau
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirTypeAction(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerTypeAction(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerTypeAction(row.id),
    },
];

// charger les types d'actions lors du montage
onMounted(() => {
    chargerTypeActions(currentPage.value, searchQuery.value);
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
                        Types d'actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace type d'actions, vous pouvez voir et gérer
                        les types d'actions, ajouter et de faire plus.
                    </p>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Bouton d'ajout -->
                    <router-link to="/admin/informations/typeactions/ajouter">
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
                            @input="rechercherTypeActions"
                        />
                    </div>
                </div>

                <!-- Tableau du type d'actions -->
                <div class="mt-5 ml-4">
                    <Table
                        :columns="columns"
                        :data="typeActions"
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
                        <strong>{{
                            totalTypeActions === 0
                                ? 0
                                : (currentPage - 1) * perPage + 1
                        }}</strong>
                        <span>à</span>
                        <strong>
                            {{
                                totalTypeActions === 0
                                    ? 0
                                    : Math.min(
                                          currentPage * perPage,
                                          totalTypeActions
                                      )
                            }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalTypeActions }}</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-2">
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="
                                currentPage <= 1 || totalTypeActions === 0
                            "
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
                            :disabled="
                                currentPage >= lastPage ||
                                totalTypeActions === 0
                            "
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
