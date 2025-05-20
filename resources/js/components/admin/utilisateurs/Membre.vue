<script setup>
import Sidebar from "../../assets/Sidebar.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";
import Table from "../../assets/Table.vue";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref, onMounted, computed } from "vue";
import axios from "axios";

const membres = ref([]);
const totalMembres = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);

// Charger les membres depuis l'API
const chargerMembres = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/users/all", {
            params: { page, search },
        });
        membres.value = response.data.data;
        totalMembres.value = Number(response.data.total) || 0;
        currentPage.value = Number(response.data.current_page) || 1;
        lastPage.value = Number(response.data.last_page) || 1;
        perPage.value = Number(response.data.per_page) || 10;
    } catch (error) {
        console.error("Erreur lors du chargement des membres :", error);
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
            // Milieu: 1, ..., current-1, current, current+1, ..., n
            pageNumbers = [1, current - 1, current, current + 1, totalPages];
        }
    }

    return pageNumbers;
});

// Aller à une page spécifique
const goToPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        chargerMembres(page, searchQuery.value);
    }
};

// Supprimer un membre
const supprimerMembre = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer ce membre ?",
        async () => {
            try {
                await axios.delete(`/api/users/${id}`);
                toast.success("Membre supprimé avec succès !");
                chargerMembres(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error("Erreur lors de la suppression du membre.");
                console.error(
                    "Erreur lors de la suppression du membre :",
                    error
                );
            }
        }
    );
};

// Fonction de recherche
const rechercherMembres = () => {
    chargerMembres(1, searchQuery.value);
};

// Colonnes pour le tableau
const columns = [
    { label: "Nom", field: "nom" },
    { label: "Nom d'utilisateur", field: "nom_utilisateur" },
    { label: "Email", field: "email" },
    { label: "Département", field: "departement" },
    { label: "Rôle", field: "role" },
];

// Actions pour le tableau
const actions = [
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerMembre(row.id),
    },
];

// Fonction pour filtrer les actions selon le rôle
const filterActions = (row, actions) => {
    if (row.role === "admin") {
        return []; // Pas d'actions pour les admins
    }
    return actions; // Toutes les actions pour les autres rôles
};

// Charger les membres au montage du composant
onMounted(() => {
    chargerMembres();
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

            <!-- Contenu principal -->
            <div class="flex-1 p-5 bg-gray-50 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Membres
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Description -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace membres, vous pouvez voir la liste de tous
                        les membres de l'application, d'y ajouter et de faire
                        plus.
                    </p>
                </div>

                <!-- Barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <router-link to="/admin/utilisateurs/membres/ajouter">
                        <button
                            class="flex items-center justify-center bg-[#0062ff] text-white px-4 py-2 rounded-md w-38"
                        >
                            <Plus class="w-5 h-5 mr-2" /> Ajouter
                        </button>
                    </router-link>

                    <div
                        class="flex items-center ml-6 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                    >
                        <Search class="w-5 h-5 mr-2 text-gray-500" />
                        <input
                            type="text"
                            placeholder="Rechercher...."
                            class="outline-none bg-transparent text-gray-800 placeholder-gray-500"
                            v-model="searchQuery"
                            @input="rechercherMembres"
                        />
                    </div>
                </div>

                <!-- Tableau des membres -->
                <div class="mt-5 ml-4">
                    <Table
                        :columns="columns"
                        :data="membres"
                        :actions="actions"
                        :filterActions="filterActions"
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
                            totalMembres === 0
                                ? 0
                                : (currentPage - 1) * perPage + 1
                        }}</strong>
                        <span>à</span>
                        <strong>{{
                            totalMembres === 0
                                ? 0
                                : Math.min(currentPage * perPage, totalMembres)
                        }}</strong>
                        <span>sur</span>
                        <strong>{{ totalMembres }}</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-2">
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage <= 1 || totalMembres === 0"
                            @click="goToPage(currentPage - 1)"
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
                                @click="goToPage(page)"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="
                                currentPage >= lastPage || totalMembres === 0
                            "
                            @click="goToPage(currentPage + 1)"
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
