<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { concat } from "lodash";

const router = useRouter();

const constat = reactive({
    code: "",
    libelle: "",
    description: "", // Valeur par défaut
});
const erreurs = reactive({
    code: "",
    libelle: "",
    description: "",
});

const enregistrerConstat = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!constat.code)
        erreurs.code = "Le champ Code pour le Constat ou l'action est requis.";
    if (!constat.libelle)
        erreurs.libelle =
            "Le champ Libelle pour le Constat ou l'action est requis.";
    if (!constat.description)
        erreurs.description =
            "Le champ Description pour le Constat ou l'action est requis.";

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const userData = {
            ...constat,
        };

        await axios.post("/api/constat", userData).then(() => {
            router.push("/user/informations/constat");
            toast.success("Constat ou Action ajouté avec succès !");
        });
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                erreurs[key] = error.response.data.errors[key][0];
            });
        }
    }
};

// Fonction pour réinitialiser l'erreur d'un champ spécifique
const resetError = (field) => {
    erreurs[field] = "";
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
                        Ajout Constat
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez ajouter un nouveau
                        constat.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
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
                                :class="{
                                    'border-gray-400': !erreurs.code,
                                    'border-red-500': erreurs.code,
                                }"
                                v-model="constat.code"
                                @input="resetError('code')"
                            />
                            <p
                                v-if="erreurs.code"
                                class="flex text-red-500 text-sm mt-1"
                            >
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
                                :class="{
                                    'border-gray-400': !erreurs.libelle,
                                    'border-red-500': erreurs.libelle,
                                }"
                                v-model="constat.libelle"
                                @input="resetError('libelle')"
                            />
                            <p
                                v-if="erreurs.libelle"
                                class="flex text-red-500 text-sm mt-1"
                            >
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
                                id="description"
                                class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.description,
                                    'border-red-500': erreurs.description,
                                }"
                                v-model="constat.description"
                                @input="resetError('description')"
                            />
                            <p
                                v-if="erreurs.description"
                                class="flex text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.description }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[62%] justify-center mt-5">
                        <router-link to="/user/informations/constat"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="enregistrerConstat"
                            class="w-[15%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                        >
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
