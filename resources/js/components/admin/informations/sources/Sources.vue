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
const sources = ref([]);
const totalSources = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(4); // Même valeur que dans votre backend
const lastPage = ref(1);

// Charger les sources depuis l'API
const chargerSources = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/sources", {
            params: { page, search },
        });
        sources.value = response.data.data;
        totalSources.value = response.data.total;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
    } catch (error) {
        console.error("Erreur lors du chargement des sources :", error);
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
            // Milieu: ..., n-2, n-1, n, n+1, ...
            pageNumbers = [1, current - 1, current, current + 1, totalPages];
        }
    }

    return pageNumbers;
});

// Fonction pour changer de page
const changerPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        chargerSources(page, searchQuery.value);
    }
};

// Fonction pour gérer la recherche
const rechercherSources = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    chargerSources(currentPage.value, searchQuery.value);
};

// Fonction pour supprimer une source
const supprimerSource = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer cette source ?",
        async () => {
            try {
                await axios.delete(`/api/sources/${id}`);
                chargerSources(currentPage.value, searchQuery.value);
                toast.success("Source supprimée avec succès !");
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression de la source",
                    error
                );
            }
        }
    );
};

const voirSource = (id) => {
    router.push(`/admin/informations/sources/voir/${id}`);
};

const editerSource = (id) => {
    router.push(`/admin/informations/sources/editer/${id}`);
};

// Colonne pour le tableau
const columns = [
    { label: "Code", field: "code" },
    { label: "Libelle", field: "libelle" },
];

// Actions pour le tableau
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirSource(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerSource(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerSource(row.id),
    },
];

// Charger les sources lors du montage du composant
onMounted(() => {
    chargerSources();
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
                        Sources
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace sources, vous pouvez voir et gérer les
                        sources, ajouter et de faire plus.
                    </p>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Bouton d'ajout -->
                    <router-link to="/admin/informations/sources/ajouter">
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
                            @input="rechercherSources"
                        />
                    </div>
                </div>

                <!-- Tableau des membres -->
                <div class="mt-5 ml-4">
                    <Table
                        :columns="columns"
                        :data="sources"
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
                            {{ Math.min(currentPage * perPage, totalSources) }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalSources }}</strong>
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
