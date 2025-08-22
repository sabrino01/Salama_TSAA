<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();

const suivi = reactive({
    nom: "",
    description: "",
    email: "",
    mot_de_passe: "",
});
const erreurs = reactive({
    nom: "",
    description: "",
    email: "",
    mot_de_passe: "",
});

const enregistrerSuivi = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!suivi.nom) erreurs.nom = "Le champ Nom pour le Suivi est requis.";
    if (!suivi.description)
        erreurs.description = "Le champ Description pour le Suivi est requis.";
    if (!suivi.email)
        erreurs.email = "Le champ Email pour le suivi est requis.";
    if (!suivi.mot_de_passe)
        erreurs.mot_de_passe =
            "Le champ du mot du passe pour le suivi est requis.";

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const userData = {
            ...suivi,
        };

        await axios.post("/api/suivi", userData).then(() => {
            router.push("/admin/informations/suivi");
            toast.success("Suivi ajouté avec succès !");
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

onMounted(() => {
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
                            Ajout Suivi
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans cet espace, vous pourriez ajouter un
                                nouveau personne pour pouvoir faire le suivi
                                d'une action.
                            </p>
                        </div>

                        <!-- Formulaire d'ajout du suivi -->
                        <div class="w-full mt-5">
                            <div class="flex w-[60%] items-center">
                                <label
                                    for="nom"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Nom :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="nom"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.nom,
                                            'border-red-500': erreurs.nom,
                                        }"
                                        v-model="suivi.nom"
                                        @input="resetError('nom')"
                                    />
                                    <p
                                        v-if="erreurs.nom"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.nom }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="description"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Description :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="description"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.description,
                                            'border-red-500':
                                                erreurs.description,
                                        }"
                                        v-model="suivi.description"
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
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="email"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Email :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="email"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.email,
                                            'border-red-500': erreurs.email,
                                        }"
                                        v-model="suivi.email"
                                        @input="resetError('email')"
                                    />
                                    <p
                                        v-if="erreurs.email"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.email }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="mot_de_passe"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Mot de passe :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="password"
                                        id="mot_de_passe"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.mot_de_passe,
                                            'border-red-500':
                                                erreurs.mot_de_passe,
                                        }"
                                        v-model="suivi.mot_de_passe"
                                        @input="resetError('mot_de_passe')"
                                    />
                                    <p
                                        v-if="erreurs.mot_de_passe"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.mot_de_passe }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[41%] justify-end mt-5">
                                <router-link to="/admin/informations/suivi"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="enregistrerSuivi"
                                    class="w-[18%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                                >
                                    Enregistrer
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
