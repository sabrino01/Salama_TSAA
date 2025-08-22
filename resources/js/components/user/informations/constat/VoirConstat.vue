<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const route = useRoute();
const constat = ref({});

onMounted(async () => {
    const id = route.params.id;
    try {
        const response = await axios.get(`/api/constat/${id}`);
        constat.value = response.data;
    } catch (error) {
        toast.error("Erreur lors du chargement du Constat ou Action", error);
    }
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
                            Voir Constat
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans cet espace, vous pourriez connaître plus
                                d'informations sur le constat sélectionné.
                            </p>
                        </div>

                        <!-- Formulaire d'ajout de membre -->
                        <div class="w-full mt-5">
                            <div class="flex w-[60%] items-center">
                                <span
                                    class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Code :
                                </span>
                                <span
                                    class="w-[50%] px-4 text-lg font-semibold"
                                    >{{ constat.code }}</span
                                >
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <span
                                    class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Libelle :
                                </span>
                                <span
                                    class="w-[50%] px-4 text-lg font-semibold"
                                    >{{ constat.libelle }}</span
                                >
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <span
                                    class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Description :
                                </span>
                                <span
                                    class="w-[50%] px-4 text-lg font-semibold"
                                    >{{ constat.description }}</span
                                >
                            </div>
                            <div class="flex w-[61.6%] justify-center mt-5">
                                <router-link to="/user/informations/constat"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <router-link
                                    :to="`/user/informations/constat/editer/${constat.id}`"
                                    class="w-[15%]"
                                >
                                    <button
                                        class="bg-green-500 text-white font-semibold rounded-md px-4 py-2"
                                    >
                                        Editer
                                    </button>
                                </router-link>
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
