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
                            historiques des actions
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

                        <div class="border-r border-gray-300"></div>

                        <router-link
                            to="/user/historiques/aii"
                            class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                            :class="{
                                'border-b-4 border-blue-600':
                                    $route.path === '/user/historiques/aii',
                            }"
                        >
                            AII
                        </router-link>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="history in historyItems"
                            :key="history.id"
                            class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
                            @click="openModal(history)"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-bold">
                                        {{ history.title }}
                                    </h2>
                                    <h3 class="text-lg text-gray-600">
                                        {{ history.subtitle }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        par {{ history.user }}
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ formatDate(history.date) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div
                        v-if="showModal"
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center"
                    >
                        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
                            <div class="border-b pb-2 mb-4">
                                <h2 class="text-2xl font-bold">
                                    {{ modalContent.title }}
                                </h2>
                            </div>
                            <div class="mb-4">
                                <h3 class="text-lg">{{ modalContent.body }}</h3>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    @click="showModal = false"
                                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                                >
                                    Fermer
                                </button>
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
import axios from "axios";
import moment from "moment";
import Sidebar from "../../assets/SidebarUser.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";
import { ref, onMounted } from "vue";

export default {
    name: "HistoryAIMenu",
    components: { Sidebar, Navbar, Footer },
    setup() {
        const isSidebarCollapsed = ref(false);

        const handleSidebarToggle = (collapsed) => {
            isSidebarCollapsed.value = collapsed;
            localStorage.setItem("sidebar-collapsed", collapsed);
        };

        onMounted(() => {
            const saved = localStorage.getItem("sidebar-collapsed");
            if (saved !== null) {
                isSidebarCollapsed.value = saved === "true";
            }
        });

        return { isSidebarCollapsed, handleSidebarToggle };
    },
    data() {
        return {
            historyItems: [],
            showModal: false,
            modalContent: { title: "", body: "" },
        };
    },
    mounted() {
        this.fetchHistory();
    },
    methods: {
        async fetchHistory() {
            try {
                const res = await axios.get("/api/historiques/aii");
                this.historyItems = res.data;
            } catch (err) {
                console.error("Erreur chargement historique :", err);
            }
        },
        formatDate(date) {
            return moment(date).format("DD/MM/YYYY à HH:mm");
        },
        openModal(history) {
            this.modalContent = {
                title: history.title,
                body: history.modalBody,
            };
            this.showModal = true;
        },
    },
};
</script>
