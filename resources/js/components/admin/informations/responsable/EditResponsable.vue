<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const responsable = ref({
    code: "",
    libelle: "",
    description: "",
});

const erreurs = ref({
    code: "",
    libelle: "",
    description: "",
});

onMounted(async () => {
    const id = route.params.id;
    axios
        .get(`/api/responsable/${id}`)
        .then((response) => {
            responsable.value = response.data;
        })
        .catch((error) => {
            toast.error("Erreur lors de la récupération du Responsable", error);
        });
});

const mettreAJourResponsable = async () => {
    const id = route.params.id;
    try {
        await axios.put(`/api/responsable/${id}`, responsable.value);
        router.push("/admin/informations/responsable");
        toast.success("Responsable mise à jour avec succès !");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            erreurs.value = error.response.data.errors;
        } else {
            toast.error("Erreur lors de la mise à jour du responsable", error);
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
                        Editer Responsable
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Editer le responsable pour pouvoir faire des
                        modifications sur le responsable sélectionné.
                    </p>
                </div>

                <!-- Formulaire d'edition du responsable -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <label
                            for="code"
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="code"
                                class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="responsable.code"
                            />
                            <p v-if="erreurs.code" class="text-red-500">
                                {{ erreurs.code }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="libelle"
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="libelle"
                                class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="responsable.libelle"
                            />
                            <p v-if="erreurs.libelle" class="text-red-500">
                                {{ erreurs.libelle }}
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
                                id="libelle"
                                class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="responsable.description"
                            />
                            <p v-if="erreurs.description" class="text-red-500">
                                {{ erreurs.description }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[63.6%] justify-center mt-5">
                        <router-link to="/admin/informations/responsable"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="mettreAJourResponsable"
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
