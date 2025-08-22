<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
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

const constat = reactive({
    code: "",
    libelle: "",
    description: "", // Valeur par défaut
    types_audit: [],
});
const erreurs = reactive({
    code: "",
    libelle: "",
    description: "",
    types_audit: "",
});

// Options pour le select multiple
const typesAuditOptions = reactive({
    pta: "PTA",
    auditinterne: "Audit Interne",
    auditexterne: "Audit Externe",
    cac: "CAC",
    swot: "SWOT",
    enquete: "Enquête de Satisfaction",
});

// Fonction pour gérer la sélection/désélection des types d'audit
const toggleTypeAudit = (type) => {
    const index = constat.types_audit.indexOf(type);
    if (index > -1) {
        constat.types_audit.splice(index, 1);
    } else {
        constat.types_audit.push(type);
    }
    resetError("types_audit");
};

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
    if (constat.types_audit.length === 0)
        erreurs.types_audit = "Veuillez sélectionner au moins un type d'audit.";

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
                            Ajout Constat
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
                                nouveau constat.
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
                                            'border-gray-400':
                                                !erreurs.description,
                                            'border-red-500':
                                                erreurs.description,
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

                            <!-- Types d'audit -->
                            <div class="flex w-auto items-start mt-5">
                                <label
                                    class="w-auto ml-4 text-lg font-semibold text-gray-800 mt-2"
                                >
                                    Types d'audit :
                                </label>
                                <div class="w-[40%] ml-4">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div
                                            v-for="(
                                                label, key
                                            ) in typesAuditOptions"
                                            :key="key"
                                            class="flex items-center"
                                        >
                                            <input
                                                type="checkbox"
                                                :id="key"
                                                :value="key"
                                                :checked="
                                                    constat.types_audit.includes(
                                                        key
                                                    )
                                                "
                                                @change="toggleTypeAudit(key)"
                                                class="mr-2 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label
                                                :for="key"
                                                class="text-sm text-gray-700 cursor-pointer"
                                            >
                                                {{ label }}
                                            </label>
                                        </div>
                                    </div>
                                    <p
                                        v-if="erreurs.types_audit"
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.types_audit }}
                                    </p>
                                    <!-- Affichage des types sélectionnés -->
                                    <div
                                        v-if="constat.types_audit.length > 0"
                                        class="mt-2"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Sélectionnés :
                                        </p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span
                                                v-for="type in constat.types_audit"
                                                :key="type"
                                                class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
                                            >
                                                {{ typesAuditOptions[type] }}
                                            </span>
                                        </div>
                                    </div>
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
                </div>

                <!-- Footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>
