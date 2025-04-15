<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

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
                        Editer Suivi
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez faire des modifications
                        sur la personne sélectionnée pour un suivi.
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
                            <p v-if="erreurs.description" class="text-red-500">
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

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
