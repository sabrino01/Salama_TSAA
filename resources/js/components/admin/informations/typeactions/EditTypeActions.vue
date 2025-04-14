<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const typeActions = ref({
    code: "",
    libelle: "",
    typeactions_pour: "",
});

const erreurs = ref({
    code: "",
    libelle: "",
    typeactions_pour: "",
});

onMounted(async () => {
    const id = route.params.id;
    axios
        .get(`/api/typeactions/${id}`)
        .then((response) => {
            typeActions.value = response.data;
        })
        .catch((error) => {
            toast.error(
                "Erreur lors de la récupération du Type d'action",
                error
            );
        });
});

const mettreAJourTypeActions = async () => {
    const id = route.params.id;
    try {
        await axios.put(`/api/typeactions/${id}`, typeActions.value);
        router.push("/admin/informations/typeactions");
        toast.success("Type d'action mise à jour avec succès !");
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
                        Editer Type d'Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Editer le type d'action pour pouvoir faire des
                        modifications sur le type d'action sélectionnée.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <label
                            for="code"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="code"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="typeActions.code"
                            />
                            <p v-if="erreurs.code" class="text-red-500">
                                {{ erreurs.code }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="libelle"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="libelle"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="typeActions.libelle"
                            />
                            <p v-if="erreurs.libelle" class="text-red-500">
                                {{ erreurs.libelle }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="typeactions_pour"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action pour :
                        </label>
                        <div class="w-[50%]">
                            <select
                                type="text"
                                id="typeactions_pour"
                                class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                v-model="typeActions.typeactions_pour"
                            >
                                <option value="auditinterne">
                                    Audit Interne
                                </option>
                                <option value="pta">PTA</option>
                            </select>
                            <p
                                v-if="erreurs.typeactions_pour"
                                class="text-red-500"
                            >
                                {{ erreurs.typeactions_pour }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[66%] justify-center mt-5">
                        <router-link to="/admin/informations/typeactions"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="mettreAJourTypeActions"
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
