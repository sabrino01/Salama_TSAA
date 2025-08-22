<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
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
const router = useRouter();
const suivi = ref({
    nom: "",
    description: "",
});

const erreurs = ref({
    nom: "",
    description: "",
});

onMounted(async () => {
    const id = route.params.id;
    axios
        .get(`/api/suivi/${id}`)
        .then((response) => {
            suivi.value = response.data;
        })
        .catch((error) => {
            toast.error("Erreur lors de la récupération du Suivi", error);
        });
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});

const mettreAJourSuivi = async () => {
    const id = route.params.id;
    try {
        await axios.put(`/api/suivi/${id}`, suivi.value);
        router.push("/user/informations/suivi");
        toast.success("Suivi mise à jour avec succès !");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            erreurs.value = error.response.data.errors;
        } else {
            toast.error("Erreur lors de la mise à jour du suivi", error);
        }
    }
};
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
                            Editer Suivi
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans cet espace, vous pourriez faire des
                                modifications sur la personne sélectionnée pour
                                un suivi.
                            </p>
                        </div>

                        <!-- Formulaire d'édition du suivi -->
                        <div class="w-full mt-5">
                            <div class="flex w-[60%] items-center">
                                <label
                                    for="nom"
                                    class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Nom :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="nom"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        v-model="suivi.nom"
                                    />
                                    <p v-if="erreurs.nom" class="text-red-500">
                                        {{ erreurs.nom }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="description"
                                    class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Description :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="description"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        v-model="suivi.description"
                                    />
                                    <p
                                        v-if="erreurs.description"
                                        class="text-red-500"
                                    >
                                        {{ erreurs.description }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[63.6%] justify-center mt-5">
                                <router-link to="/user/informations/suivi"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="mettreAJourSuivi"
                                    class="w-[12%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                                >
                                    Modifier
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
