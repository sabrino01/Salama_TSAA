<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const source = ref({});

onMounted(async () => {
    const id = route.params.id;
    try {
        const response = await axios.get(`/api/sources/${id}`);
        source.value = response.data;
    } catch (error) {
        toast.error("Erreur lors du chargement de la source", error);
    }
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
                        Voir Sources
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Voir la source pour connaître plus d'informations sur la
                        source sélectionnée.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <span
                            class="w-[10%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </span>
                        <span class="w-[50%] px-4 text-lg font-semibold">{{
                            source.code
                        }}</span>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[10%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </span>
                        <span class="w-[50%] px-4 text-lg font-semibold">{{
                            source.libelle
                        }}</span>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[15%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Source pour :
                        </span>
                        <span
                            class="w-[45%] px-4 text-lg font-semibold uppercase"
                            >{{ source.sources_pour }}</span
                        >
                    </div>
                    <div class="flex w-[61.6%] justify-center mt-5">
                        <router-link to="/admin/informations/sources"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <router-link
                            :to="`/admin/informations/sources/editer/${source.id}`"
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

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
