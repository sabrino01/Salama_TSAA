<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const source = ref({
    code: "",
    libelle: "",
    sources_pour: "",
});

const erreurs = ref({
    code: "",
    libelle: "",
    sources_pour: "",
});

onMounted(async () => {
    const sourceId = route.params.id;
    axios
        .get(`/api/sources/${sourceId}`)
        .then((response) => {
            source.value = response.data;
        })
        .catch((error) => {
            toast.error("Erreur lors de la récupération de la source :", error);
        });
});

const mettreAJourSource = async () => {
    const sourceId = route.params.id;
    try {
        await axios.put(`/api/sources/${sourceId}`, source.value);
        router.push("/user/informations/sources");
        toast.success("Source mise à jour avec succès !");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            erreurs.value = error.response.data.errors;
        } else {
            toast.error("Erreur lors de la mise à jour de la source :", error);
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
                        Editer Sources
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Editer la source pour pouvoir faire des modifications
                        sur la source sélectionnée.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <label
                            for="code"
                            class="w-[10%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="code"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="source.code"
                            />
                            <p v-if="erreurs.code" class="text-red-500">
                                {{ erreurs.code }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="libelle"
                            class="w-[10%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="libelle"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="source.libelle"
                            />
                            <p v-if="erreurs.libelle" class="text-red-500">
                                {{ erreurs.libelle }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="sources_pour"
                            class="w-[15%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Sources pour :
                        </label>
                        <div class="w-[50%]">
                            <select
                                type="text"
                                id="sources_pour"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="source.sources_pour"
                            >
                                <option value="auditinterne">
                                    Audit Interne
                                </option>
                                <option value="pta">PTA</option>
                            </select>
                            <p v-if="erreurs.sources_pour" class="text-red-500">
                                {{ erreurs.sources_pour }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[61.6%] justify-center mt-5">
                        <router-link to="/user/informations/sources"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="mettreAJourSource"
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
