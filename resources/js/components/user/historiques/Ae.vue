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
            <Navbar :isSidebarCollapsed="isSidebarCollapsed" />

            <!-- Contenu principal -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-5">
                    <!-- Titre -->
                    <div class="flex w-full">
                        <div
                            class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                        >
                            Historiques des actions
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <!-- Phrase introductive -->
                    <div class="w-full text-gray-600 mt-5">
                        <p class="indent-4 font-poppins">
                            Dans l'espace historique, vous pouvez voir les
                            historiques des actions et encore plus pour Audit
                            Interne, PTA, Audit Externe, SWOT, CAC, Enquête de
                            Satisfaction et Audit Interne et Inspection.
                        </p>
                    </div>

                    <!-- Liens vers les actions -->
                    <div class="flex w-full mt-5 ml-4 justify-center space-x-4">
                        <router-link
                            to="/user/historiques/auditinterne"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path ===
                                    '/user/historiques/auditinterne',
                            }"
                        >
                            Audit Interne
                        </router-link>

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/pta"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/pta',
                            }"
                        >
                            PTA
                        </router-link>

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/ae"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/ae',
                            }"
                        >
                            Audit Externe
                        </router-link>

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/cac"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/cac',
                            }"
                        >
                            CAC
                        </router-link>

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/enquete"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/enquete',
                            }"
                        >
                            ENQUETE
                        </router-link>

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/swot"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/swot',
                            }"
                        >
                            SWOT
                        </router-link>
                    </div>

                    <div class="min-h-screen bg-gray-50 py-8">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <!-- Header avec toggle pagination -->
                            <div class="flex justify-between items-center mb-6">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Historique des Actions Audit Externe
                                </h1>
                                <!-- Toggle de pagination -->
                                <div class="relative">
                                    <button
                                        @click="togglePagination"
                                        class="flex items-center justify-center border border-gray-400 text-black px-4 py-2 rounded-md"
                                        :class="{
                                            'bg-blue-100': !paginationEnabled,
                                            'bg-white': paginationEnabled,
                                        }"
                                    >
                                        {{
                                            paginationEnabled
                                                ? "Désactiver Pagination"
                                                : "Activer Pagination"
                                        }}
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div
                                v-if="loading"
                                class="flex justify-center items-center py-12"
                            >
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                                ></div>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-else-if="historique.length === 0"
                                class="text-center py-12"
                            >
                                <div class="text-gray-400 text-xl mb-4">📋</div>
                                <h3
                                    class="text-lg font-medium text-gray-900 mb-2"
                                >
                                    Aucun historique trouvé
                                </h3>
                                <p class="text-gray-500">
                                    Il n'y a aucune modification enregistrée
                                    pour les actions Audit Externe.
                                </p>
                            </div>

                            <!-- Historique Cards -->
                            <div v-else class="space-y-4">
                                <div
                                    v-for="item in historique"
                                    :key="item.id"
                                    @click="openModal(item)"
                                    class="bg-white rounded-lg shadow-md border border-gray-200 p-6 cursor-pointer hover:shadow-lg hover:border-blue-300 transition-all duration-200"
                                >
                                    <div
                                        class="flex justify-between items-start"
                                    >
                                        <div class="flex-1">
                                            <!-- Type d'événement -->
                                            <h2
                                                class="text-xl font-semibold text-gray-900 mb-2"
                                            >
                                                <span
                                                    v-if="
                                                        item.type === 'creation'
                                                    "
                                                    class="text-green-600"
                                                >
                                                    ✨ Nouvelle Action
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification'
                                                    "
                                                    class="text-blue-600"
                                                >
                                                    🔄 Modification ou mise à
                                                    jour
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'creation_responsable'
                                                    "
                                                    class="text-orange-600"
                                                >
                                                    👤 Nouvelle Action pour le
                                                    Responsable
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_responsable'
                                                    "
                                                    class="text-orange-600"
                                                >
                                                    👤 Modification du Constat
                                                    par le Responsable
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'creation_suivi'
                                                    "
                                                    class="text-purple-600"
                                                >
                                                    📊 Nouvelle Action qui doit
                                                    être suivi
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_suivi'
                                                    "
                                                    class="text-purple-600"
                                                >
                                                    📊 Modification du Statut
                                                    par le Suivi
                                                </span>
                                            </h2>

                                            <!-- Description -->
                                            <h3
                                                class="text-lg font-medium text-gray-700 mb-2"
                                            >
                                                <span
                                                    v-if="
                                                        item.type === 'creation'
                                                    "
                                                >
                                                    Création de nouvelle action
                                                    :
                                                    {{
                                                        item.action_description
                                                    }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification'
                                                    "
                                                >
                                                    Modification des champs :
                                                    {{
                                                        getModifiedFieldsText(
                                                            item.modified_fields
                                                        )
                                                    }}
                                                    de l'action
                                                    {{
                                                        item.action_description
                                                    }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'creation_responsable'
                                                    "
                                                >
                                                    Ajout d'un responsable à
                                                    l'action
                                                    {{
                                                        item.action_description
                                                    }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_responsable'
                                                    "
                                                >
                                                    Modification du Constat de
                                                    l'action
                                                    {{
                                                        getModifiedFieldsText(
                                                            item.modified_fields
                                                        )
                                                    }}
                                                    de l'action
                                                    {{
                                                        item.action_description
                                                    }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'creation_suivi'
                                                    "
                                                >
                                                    Ajout d'un suivi à l'action
                                                    {{
                                                        item.action_description
                                                    }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_suivi'
                                                    "
                                                >
                                                    Modification du Statut de
                                                    l'action
                                                    {{
                                                        getModifiedFieldsText(
                                                            item.modified_fields
                                                        )
                                                    }}
                                                </span>
                                            </h3>

                                            <!-- Utilisateur -->
                                            <p class="text-gray-600">
                                                <span
                                                    v-if="
                                                        item.type ===
                                                            'creation' ||
                                                        item.type ===
                                                            'creation_responsable' ||
                                                        item.type ===
                                                            'creation_suivi' ||
                                                        item.type ===
                                                            'modification'
                                                    "
                                                >
                                                    par
                                                    {{ item.user_name }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_responsable'
                                                    "
                                                >
                                                    faite par
                                                    {{ item.responsable_name }}
                                                </span>
                                                <span
                                                    v-else-if="
                                                        item.type ===
                                                        'modification_suivi'
                                                    "
                                                >
                                                    faite par
                                                    {{ item.suivi_name }}
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Date -->
                                        <div class="text-right">
                                            <span
                                                v-if="item.type === 'creation'"
                                            >
                                                {{
                                                    formatDate(item.created_at)
                                                }}
                                            </span>
                                            <!-- Pour les modifications, afficher updated_at -->
                                            <span v-else>
                                                {{
                                                    formatDate(item.updated_at)
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer avec pagination -->
                            <div
                                v-if="!loading && historique.length > 0"
                                class="flex w-full mt-5 justify-between"
                            >
                                <!-- Résultat -->
                                <div
                                    class="flex items-center text-gray-500 justify-start px-4 space-x-2"
                                >
                                    <span>Résultat</span>
                                    <strong>{{
                                        totalHistorique === 0
                                            ? 0
                                            : paginationEnabled
                                            ? (currentPage - 1) * perPage + 1
                                            : 1
                                    }}</strong>
                                    <span>à</span>
                                    <strong>
                                        {{
                                            totalHistorique === 0
                                                ? 0
                                                : paginationEnabled
                                                ? Math.min(
                                                      currentPage * perPage,
                                                      totalHistorique
                                                  )
                                                : totalHistorique
                                        }}
                                    </strong>
                                    <span>sur</span>
                                    <strong>{{ totalHistorique }}</strong>
                                </div>

                                <!-- Pagination - masquer quand elle est désactivée -->
                                <div
                                    v-if="paginationEnabled"
                                    class="flex items-center justify-end space-x-2"
                                >
                                    <button
                                        class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                                        :disabled="
                                            currentPage <= 1 ||
                                            totalHistorique === 0
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
                                            totalHistorique === 0
                                        "
                                        @click="changerPage(currentPage + 1)"
                                    >
                                        Suiv. <ChevronRight class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal (reste identique) -->
                        <div
                            v-if="showModal"
                            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                            @click="closeModal"
                        >
                            <div
                                class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
                                @click.stop
                            >
                                <!-- Header -->
                                <div class="border-b pb-4 mb-4">
                                    <h2
                                        class="text-2xl font-bold text-gray-900"
                                    >
                                        <span
                                            v-if="
                                                selectedItem?.type ===
                                                'creation'
                                            "
                                            class="text-green-600"
                                        >
                                            Nouvelle action
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification'
                                            "
                                            class="text-blue-600"
                                        >
                                            Modification ou mise à jour
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'creation_responsable'
                                            "
                                            class="text-orange-600"
                                        >
                                            Nouvelle Action pour le responsable
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification_responsable'
                                            "
                                            class="text-orange-600"
                                        >
                                            Modification du Constat par
                                            responsable
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'creation_suivi'
                                            "
                                            class="text-purple-600"
                                        >
                                            Nouvelle Action qui doit être suivi
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification_suivi'
                                            "
                                            class="text-purple-600"
                                        >
                                            Modification du Statut par suivi
                                        </span>
                                    </h2>
                                </div>

                                <!-- Body -->
                                <div class="mb-6">
                                    <h3
                                        class="text-lg font-semibold text-gray-800 mb-3"
                                    >
                                        <span
                                            v-if="
                                                selectedItem?.type ===
                                                'creation'
                                            "
                                        >
                                            Création de l'action : "{{
                                                selectedItem?.action_description
                                            }}" par
                                            {{ selectedItem?.user_name }} ;
                                            faite le
                                            {{
                                                formatDate(
                                                    selectedItem?.created_at
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification'
                                            "
                                        >
                                            {{
                                                getModalModificationText(
                                                    selectedItem
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'creation_responsable'
                                            "
                                        >
                                            Ajout d'un responsable à l'action
                                            {{
                                                selectedItem?.action_description
                                            }}
                                            pour
                                            {{ selectedItem?.responsable_name }}
                                            ; faite le
                                            {{
                                                formatDate(
                                                    selectedItem?.created_at
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification_responsable'
                                            "
                                        >
                                            Modification du Constat "{{
                                                getOldValue(
                                                    selectedItem,
                                                    "statut_resp"
                                                )
                                            }}" en "{{
                                                getNewValue(
                                                    selectedItem,
                                                    "statut_resp"
                                                )
                                            }}", par
                                            {{ selectedItem?.responsable_name }}
                                            ; faite le
                                            {{
                                                formatDate(
                                                    selectedItem?.updated_at
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'creation_suivi'
                                            "
                                        >
                                            Ajout d'un suivi à l'action
                                            {{
                                                selectedItem?.action_description
                                            }}
                                            pour
                                            {{ selectedItem?.suivi_name }} ;
                                            faite le
                                            {{
                                                formatDate(
                                                    selectedItem?.created_at
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-else-if="
                                                selectedItem?.type ===
                                                'modification_suivi'
                                            "
                                        >
                                            Modification du Statut "{{
                                                getOldValue(
                                                    selectedItem,
                                                    "statut_suivi"
                                                )
                                            }}" en "{{
                                                getNewValue(
                                                    selectedItem,
                                                    "statut_suivi"
                                                )
                                            }}", pour
                                            {{ selectedItem?.suivi_name }} ;
                                            faite le
                                            {{
                                                formatDate(
                                                    selectedItem?.updated_at
                                                )
                                            }}
                                        </span>
                                    </h3>

                                    <!-- Détails des modifications pour les modifications normales -->
                                    <div
                                        v-if="
                                            selectedItem?.type ===
                                                'modification' &&
                                            selectedItem?.modified_fields
                                                ?.length > 0
                                        "
                                        class="bg-gray-50 p-4 rounded-lg"
                                    >
                                        <h4
                                            class="font-medium text-gray-700 mb-2"
                                        >
                                            Détails des modifications :
                                        </h4>
                                        <div
                                            v-for="field in selectedItem.modified_fields"
                                            :key="field"
                                            class="mb-2"
                                        >
                                            <span class="font-medium"
                                                >{{
                                                    getFieldLabel(field)
                                                }}
                                                :</span
                                            >
                                            <span class="text-red-600"
                                                >"{{
                                                    getOldValue(
                                                        selectedItem,
                                                        field
                                                    )
                                                }}"</span
                                            >
                                            <span class="mx-2">→</span>
                                            <span class="text-green-600"
                                                >"{{
                                                    getNewValue(
                                                        selectedItem,
                                                        field
                                                    )
                                                }}"</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex justify-end">
                                    <button
                                        @click="closeModal"
                                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200"
                                    >
                                        Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Footer />
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import moment from "moment";
import "moment-timezone";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import Sidebar from "../../assets/SidebarUser.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";

export default {
    name: "Ae",
    components: {
        Sidebar,
        Navbar,
        Footer,
        ChevronLeft,
        ChevronRight,
    },
    setup() {
        const historique = ref([]);
        const loading = ref(true);
        const showModal = ref(false);
        const selectedItem = ref(null);
        const isSidebarCollapsed = ref(false);

        // Variables de pagination
        const totalHistorique = ref(0);
        const currentPage = ref(1);
        const perPage = ref(20);
        const lastPage = ref(1);
        const paginationEnabled = ref(true);
        const allHistorique = ref([]);

        const handleSidebarToggle = (collapsed) => {
            isSidebarCollapsed.value = collapsed;
            localStorage.setItem("sidebar-collapsed", collapsed);
        };

        const fieldLabels = {
            description: "Description",
            sources_id: "Source",
            type_actions_id: "Type d'action",
            responsables_id: "Responsables",
            suivis_id: "Suivis",
            frequence: "Fréquence",
            observation: "Observation",
            statut: "Statut",
            action: "Action",
            mesure: "Livrable",
            observation_par_suivi: "Observation par Suivi",
            indicateurs_utilisateurs: "Indicateurs Utilisateurs",
            partenaire: "Partenaire",
            statut_resp: "Constat Responsable",
            observation_resp: "Observation Responsable",
            statut_suivi: "Statut Suivi",
            observation_suivi: "Observation Suivi",
        };

        const fetchHistorique = async (page = 1) => {
            try {
                loading.value = true;

                if (paginationEnabled.value) {
                    // Comportement avec pagination
                    const response = await axios.get("/api/historiques/ae", {
                        params: { page, per_page: perPage.value },
                    });

                    if (response.data.success) {
                        historique.value = response.data.data;
                        totalHistorique.value =
                            Number(response.data.total) || 0;
                        currentPage.value =
                            Number(response.data.current_page) || 1;
                        lastPage.value = Number(response.data.last_page) || 1;
                        perPage.value = Number(response.data.per_page) || 20;
                    }
                } else {
                    // Charger toutes les données sans pagination
                    const response = await axios.get("/api/historiques/ae", {
                        params: { page: 1, per_page: 1000000 },
                    });

                    if (response.data.success) {
                        allHistorique.value = response.data.data || [];
                        historique.value = allHistorique.value;
                        totalHistorique.value = allHistorique.value.length;
                        currentPage.value = 1;
                        lastPage.value = 1;
                    }
                }
            } catch (error) {
                console.error(
                    "Erreur lors du chargement de l'historique:",
                    error
                );
            } finally {
                loading.value = false;
            }
        };

        const togglePagination = () => {
            paginationEnabled.value = !paginationEnabled.value;
            fetchHistorique(1);
        };

        // Générer les numéros de page pour la pagination
        const pages = computed(() => {
            const totalPages = lastPage.value;
            const current = currentPage.value;
            let pageNumbers = [];

            if (totalPages <= 5) {
                for (let i = 1; i <= totalPages; i++) {
                    pageNumbers.push(i);
                }
            } else {
                if (current <= 3) {
                    pageNumbers = [1, 2, 3, 4, totalPages];
                } else if (current >= totalPages - 2) {
                    pageNumbers = [
                        1,
                        totalPages - 4,
                        totalPages - 3,
                        totalPages - 2,
                        totalPages,
                    ];
                } else {
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

        const changerPage = (page) => {
            if (page >= 1 && page <= lastPage.value) {
                currentPage.value = page;
                fetchHistorique(page);
            }
        };

        const formatDate = (dateString) => {
            return moment.utc(dateString).format("DD/MM/YYYY à HH:mm");
        };

        const getModifiedFieldsText = (fields) => {
            if (!fields || fields.length === 0) return "champ inconnu";
            return fields
                .map((field) => fieldLabels[field] || field)
                .join(", ");
        };

        const getFieldLabel = (field) => {
            return fieldLabels[field] || field;
        };

        const getOldValue = (item, field) => {
            const val = item?.old_values?.[field];
            return val !== null && val !== undefined && val !== ""
                ? val
                : "Valeur vide";
        };

        const getNewValue = (item, field) => {
            const val = item?.new_values?.[field];
            return val !== null && val !== undefined && val !== ""
                ? val
                : "Valeur vide";
        };

        const getModalModificationText = (item) => {
            if (!item?.modified_fields?.length) return "";
            return `Modifications effectuées par ${
                item.user_name
            } le ${formatDate(item.updated_at)} :`;
        };

        const openModal = (item) => {
            selectedItem.value = item;
            showModal.value = true;
        };

        const closeModal = () => {
            showModal.value = false;
            selectedItem.value = null;
        };

        onMounted(() => {
            const saved = localStorage.getItem("sidebar-collapsed");
            if (saved !== null) {
                isSidebarCollapsed.value = saved === "true";
            }
            fetchHistorique();
        });

        return {
            historique,
            loading,
            showModal,
            selectedItem,
            isSidebarCollapsed,
            totalHistorique,
            currentPage,
            perPage,
            lastPage,
            paginationEnabled,
            pages,
            handleSidebarToggle,
            fetchHistorique,
            togglePagination,
            changerPage,
            formatDate,
            getModifiedFieldsText,
            getFieldLabel,
            getOldValue,
            getNewValue,
            getModalModificationText,
            openModal,
            closeModal,
        };
    },
};
</script>
