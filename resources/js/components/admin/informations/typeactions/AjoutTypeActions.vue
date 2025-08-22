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

const typeactions = reactive({
    code: "",
    libelle: "",
    typeactions_pour: "auditinterne", // Valeur par défaut
});
const erreurs = reactive({
    code: "",
    libelle: "",
    typeactions_pour: "",
});

const enregistrerTypeActions = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!typeactions.code)
        erreurs.code = "Le champ Code pour le type d'action est requis.";
    if (!typeactions.libelle)
        erreurs.libelle = "Le champ Libelle pour le type d'action est requis.";
    if (!typeactions.typeactions_pour)
        erreurs.typeactions_pour =
            "Le champ de choix pour le type d'action est requis.";

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const userData = {
            ...typeactions,
        };

        await axios.post("/api/typeactions", userData).then(() => {
            router.push("/admin/informations/typeactions");
            toast.success("Type d'actions ajouté avec succès !");
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
                            Ajout Types d'Actions
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Ajouter des types d'actions pour avoir des types
                                d'actions à faire.
                            </p>
                        </div>

                        <!-- Formulaire d'ajout de type d'actions -->
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
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.code,
                                            'border-red-500': erreurs.code,
                                        }"
                                        v-model="typeactions.code"
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
                                    class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
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
                                        v-model="typeactions.libelle"
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
                                    for="typeactions_pour"
                                    class="w-[20%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Type Action pour :
                                </label>
                                <div class="w-[50%]">
                                    <select
                                        id="typeactions_pour"
                                        v-model="typeactions.typeactions_pour"
                                        class="w-[32%] border border-gray-400 rounded-md px-4 py-2"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.typeactions_pour,
                                            'border-red-500':
                                                erreurs.typeactions_pour,
                                        }"
                                        @change="resetError('typeactions_pour')"
                                    >
                                        <option value="auditinterne">
                                            Audit Interne
                                        </option>
                                        <option value="pta">PTA</option>
                                        <option value="enquete">
                                            Enquête de satisfaction
                                        </option>
                                        <option value="swot">SWOT</option>
                                        <option value="cac">CAC</option>
                                        <option value="auditexterne">
                                            Audit Externe
                                        </option>
                                    </select>
                                    <p
                                        v-if="erreurs.typeactions_pour"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.typeactions_pour }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[64%] justify-center mt-5">
                                <router-link
                                    to="/admin/informations/typeactions"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="enregistrerTypeActions"
                                    class="w-[15%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
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
