<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/Table.vue";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();
const constat = ref([]);
const totalConstat = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);

// Charger les types d'actions depuis l'API
const chargerConstat = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/constat", {
            params: { page, search },
        });
        constat.value = response.data.data;
        totalConstat.value = Number(response.data.total) || 0;
        currentPage.value = Number(response.data.current_page) || 1;
        lastPage.value = Number(response.data.last_page) || 1;
        perPage.value = Number(response.data.per_page) || 10;
    } catch (error) {
        toast.error(
            "Erreur lors du chargement du Constat ou de l'action",
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
        chargerConstat(page, searchQuery.value);
    }
};

// Fonction pour gérer la recherche
const rechercherConstat = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    chargerConstat(currentPage.value, searchQuery.value);
};

// Fonction pour gérer la suppression
const supprimerConstat = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer ce constat ou action ?",
        async () => {
            try {
                await axios.delete(`/api/constat/${id}`);
                toast.success("Constat ou Action supprimé avec succès");
                chargerConstat(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression du constat ou action",
                    error
                );
            }
        }
    );
};

const voirConstat = (id) => {
    router.push(`/admin/informations/constat/voir/${id}`);
};

const editerConstat = (id) => {
    router.push(`/admin/informations/constat/editer/${id}`);
};

// colonne pour le tableau
const columns = [
    { label: "Code", field: "code" },
    { label: "Libelle", field: "libelle" },
    { label: "Description", field: "description" },
];

// Actions pour le tableau
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirConstat(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerConstat(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerConstat(row.id),
    },
];

// charger les types d'actions lors du montage
onMounted(() => {
    chargerConstat(currentPage.value, searchQuery.value);
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});
</script>
<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar @sidebar-toggle="handleSidebarToggle" />

        <!-- Main Content -->
        <div
            :class="[
                'flex-1 flex flex-col transition-all duration-300',
                isSidebarCollapsed ? 'ml-16' : 'ml-64',
            ]"
        >
            <Navbar v-if="true" :isSidebarCollapsed="isSidebarCollapsed" />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-5">
                    <!-- Titre -->
                    <div class="flex w-full">
                        <div
                            class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                        >
                            Constat
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans l'espace constat, vous pouvez voir et gérer
                                les informations sur les constats, d'ajouter et
                                de faire plus.
                            </p>
                        </div>

                        <!-- Ajout et barre de recherche -->
                        <div class="flex w-full mt-5 ml-4">
                            <!-- Bouton d'ajout -->
                            <router-link
                                to="/admin/informations/constat/ajouter"
                            >
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
                                    @input="rechercherConstat"
                                />
                            </div>
                        </div>

                        <!-- Tableau des membres -->
                        <div class="mt-5 ml-4">
                            <Table
                                :columns="columns"
                                :data="constat"
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
                                    totalConstat === 0
                                        ? 0
                                        : (currentPage - 1) * perPage + 1
                                }}</strong>
                                <span>à</span>
                                <strong>
                                    {{
                                        totalConstat === 0
                                            ? 0
                                            : Math.min(
                                                  currentPage * perPage,
                                                  totalConstat
                                              )
                                    }}
                                </strong>
                                <span>sur</span>
                                <strong>{{ totalConstat }}</strong>
                            </div>

                            <!-- Pagination -->
                            <div
                                class="flex items-center justify-end space-x-2"
                            >
                                <button
                                    class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                                    :disabled="
                                        currentPage <= 1 || totalConstat === 0
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
                                        totalConstat === 0
                                    "
                                    @click="changerPage(currentPage + 1)"
                                >
                                    Suiv. <ChevronRight class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>
