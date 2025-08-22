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
const constat = ref({
    code: "",
    libelle: "",
    description: "",
    types_audit: [],
});

const erreurs = ref({
    code: "",
    libelle: "",
    description: "",
    types_audit: "",
});

// Options pour le select multiple
const typesAuditOptions = {
    pta: "PTA",
    auditinterne: "Audit Interne",
    auditexterne: "Audit Externe",
    cac: "CAC",
    swot: "SWOT",
    enquete: "Enquête de Satisfaction",
};

// Fonction pour gérer la sélection/désélection des types d'audit
const toggleTypeAudit = (type) => {
    const index = constat.value.types_audit.indexOf(type);
    if (index > -1) {
        constat.value.types_audit.splice(index, 1);
    } else {
        constat.value.types_audit.push(type);
    }
    resetError("types_audit");
};

// Fonction pour réinitialiser l'erreur d'un champ spécifique
const resetError = (field) => {
    erreurs.value[field] = "";
};

onMounted(async () => {
    const id = route.params.id;
    axios
        .get(`/api/constat/${id}`)
        .then((response) => {
            constat.value = response.data;
            // S'assurer que types_audit est un tableau
            if (!Array.isArray(constat.value.types_audit)) {
                constat.value.types_audit = constat.value.types_audit
                    ? constat.value.types_audit
                    : [];
            }
        })
        .catch((error) => {
            toast.error(
                "Erreur lors de la récupération du Constat ou Action",
                error
            );
        });
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});

const mettreAJourConstat = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs.value).forEach((key) => (erreurs.value[key] = ""));

    // Validation côté frontend
    if (!constat.value.code)
        erreurs.value.code =
            "Le champ Code pour le Constat ou l'action est requis.";
    if (!constat.value.libelle)
        erreurs.value.libelle =
            "Le champ Libelle pour le Constat ou l'action est requis.";
    if (!constat.value.description)
        erreurs.value.description =
            "Le champ Description pour le Constat ou l'action est requis.";
    if (constat.value.types_audit.length === 0)
        erreurs.value.types_audit =
            "Veuillez sélectionner au moins un type d'audit.";

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs.value).some((err) => err)) return;

    const id = route.params.id;
    try {
        await axios.put(`/api/constat/${id}`, constat.value);
        router.push("/user/informations/constat");
        toast.success("Constat ou Action mise à jour avec succès !");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            erreurs.value = error.response.data.errors;
        } else {
            toast.error(
                "Erreur lors de la mise à jour du constat ou action",
                error
            );
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
                            Editer Constat
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
                                modifications sur le constat sélectionné.
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
                                        v-model="constat.code"
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
                                        v-model="constat.libelle"
                                    />
                                    <p
                                        v-if="erreurs.libelle"
                                        class="text-red-500"
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
                                        v-model="constat.description"
                                    />
                                    <p
                                        v-if="erreurs.description"
                                        class="text-red-500"
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
                                                    constat.types_audit &&
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
                                        v-if="
                                            constat.types_audit &&
                                            constat.types_audit.length > 0
                                        "
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

                            <div class="flex w-[63.6%] justify-center mt-5">
                                <router-link to="/user/informations/constat"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="mettreAJourConstat"
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
