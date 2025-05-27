<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import {
    RefreshCcw,
    FileCheck,
    Check,
    X,
    CheckCircle,
    Search,
    AlertTriangle,
    Ban,
    Clock,
} from "lucide-vue-next";

import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const action = ref({});
const frequenceDetails = ref("");

// Fonction pour formater la date
const formatDate = (date) => {
    if (!date) return ""; // Si la date est vide, retourner une chaîne vide
    if (date.includes("/")) return date; // Si la date est déjà formatée, la retourner telle quelle
    const [year, month, day] = date.split("-");
    return `${day}/${month}/${year}`; // Reformater en dd/mm/yyyy
};

// Fonction pour l’icône
const getConstatIcon = (value) => {
    const icons = {
        "En Cours": RefreshCcw,
        Réalisé: Check,
        "Non Réalisé": X,
        "Ecart Réglé": CheckCircle,
        "Ecart Réglé à Suivre": Search,
        "Ecart non réglé": AlertTriangle,
        "Réalisé partiel": FileCheck,
        "Actions abandonnées": Ban,
        Retard: Clock,
    };
    return icons[value] || null;
};

// Fonction pour la couleur
const getConstatColor = (value) => {
    const colors = {
        "En Cours": "text-blue-500",
        Réalisé: "text-green-500",
        "Non Réalisé": "text-red-500",
        "Ecart Réglé": "text-blue-800",
        "Ecart Réglé à Suivre": "text-gray-500",
        "Ecart non réglé": "text-yellow-500",
        "Réalisé partiel": "text-green-600",
        "Actions abandonnées": "text-purple-500",
        Retard: "text-red-500",
    };
    return colors[value] || "";
};

// Variables réactives pour icône et couleur
const constatIcon = computed(() =>
    getConstatIcon(action.value.constat_libelle)
);
const constatColor = computed(() =>
    getConstatColor(action.value.constat_libelle)
);

const trimmedResponsables = computed(() => {
    return action.value.responsable_libelle
        ? action.value.responsable_libelle.split(",").map((e) => e.trim())
        : [];
});

const trimmedSuivis = computed(() => {
    return action.value.suivi_nom
        ? action.value.suivi_nom.split(",").map((e) => e.trim())
        : [];
});

// Fonction pour normaliser une chaîne JSON potentiellement imbriquée
const normalizeJsonString = (jsonString) => {
    if (!jsonString) return "";

    let normalizedValue = jsonString;
    let isNormalized = false;

    while (!isNormalized) {
        try {
            if (
                typeof normalizedValue === "string" &&
                (normalizedValue.startsWith('"') ||
                    normalizedValue.startsWith("{"))
            ) {
                const parsed = JSON.parse(normalizedValue);

                if (
                    typeof parsed === "string" &&
                    (parsed.startsWith('"') || parsed.startsWith("{"))
                ) {
                    normalizedValue = parsed;
                } else {
                    normalizedValue = parsed;
                    isNormalized = true;
                }
            } else {
                isNormalized = true;
            }
        } catch (e) {
            isNormalized = true;
        }
    }

    return normalizedValue;
};

// Fonction générique pour formater les données JSON en texte lisible
const formatJsonForTooltip = (jsonData) => {
    if (!jsonData || typeof jsonData !== "object") return "";

    const { type, ...rest } = jsonData;

    if (Object.keys(rest).length === 0) return "";

    const jsonString = JSON.stringify(rest, null, 2);

    return jsonString
        .replace(/",/g, '"')
        .replace(/,/g, "\n")
        .replace(/"/g, "")
        .replace(/\\/g, "")
        .replace(/\{/g, "")
        .replace(/\}/g, "")
        .replace(/\[/g, "")
        .replace(/\]/g, "")
        .replace(/\n\s*\n/g, "\n")
        .replace(/^\s+/gm, "")
        .replace(
            /\b(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})\b/g,
            (_, year, month, day, hour, minute) =>
                `${day}/${month}/${year} à ${hour}:${minute}`
        )
        .trim();
};

// Charger les données de l'action
onMounted(async () => {
    const id = route.params.id;
    try {
        const response = await axios.get(`/api/actions/${id}`);
        const fetchedAction = response.data;

        // Reformater la date
        fetchedAction.date = formatDate(fetchedAction.date);

        // Gérer la fréquence
        if (fetchedAction.frequence) {
            try {
                const normalizedFrequence = normalizeJsonString(
                    fetchedAction.frequence
                );

                const frequenceData =
                    typeof normalizedFrequence === "object" &&
                    normalizedFrequence !== null
                        ? normalizedFrequence
                        : typeof normalizedFrequence === "string" &&
                          normalizedFrequence.trim().startsWith("{")
                        ? JSON.parse(normalizedFrequence)
                        : null;

                if (frequenceData) {
                    fetchedAction.frequence =
                        frequenceData.type || "Non défini";
                    frequenceDetails.value =
                        formatJsonForTooltip(frequenceData);
                } else {
                    fetchedAction.frequence = normalizedFrequence;
                }
            } catch (e) {
                console.error("Erreur lors du parsing de la fréquence:", e);
                fetchedAction.frequence = fetchedAction.frequence;
            }
        }

        action.value = fetchedAction;
    } catch (error) {
        toast.error("Erreur lors du chargement des données", error);
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
                        Voir Audit Interne
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez voir et connaître plus
                        d'informations sur l'Audit Interne sélectionnée.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex justify-center">
                        <div
                            class="w-1/12 bg-white border-b-4 border-b-blue-700 flex items-center justify-center py-2"
                        >
                            <span class="text-lg font-semibold text-gray-500">
                                {{ action.num_actions }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-center mt-2">
                        <div class="flex items-center justify-center py-2">
                            <span class="text-2xl font-semibold text-gray-500">
                                Ajouter par : {{ action.nom_utilisateur }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center mt-6">
                        <span class="ml-4 text-xl font-semibold text-gray-800">
                            Date :
                        </span>
                        <span
                            class="ml-2 text-xl text-gray-400 font-semibold bg-white border-b border-b-gray-400 px-2"
                        >
                            {{ formatDate(action.date) }}
                        </span>
                    </div>

                    <div
                        class="flex flex-wrap justify-start gap-x-20 text-xl mt-6 ml-20"
                    >
                        <div class="flex items-center">
                            <span class="font-semibold text-gray-800">
                                Source :
                            </span>
                            <span
                                class="ml-2 text-gray-500 font-semibold bg-white border-b border-b-gray-400 px-2"
                            >
                                {{ action.source_libelle }}
                            </span>
                        </div>

                        <div class="flex items-center">
                            <span class="ml-6 font-semibold text-gray-800">
                                Type d'actions :
                            </span>
                            <span
                                class="ml-2 text-gray-500 font-semibold bg-white border-b border-b-gray-400 px-2"
                            >
                                {{ action.type_action_libelle }}
                            </span>
                        </div>

                        <div class="flex items-center">
                            <span class="ml-6 font-semibold text-gray-800">
                                Constat :
                            </span>
                            <span
                                class="ml-2 font-semibold text-2xl border-b border-b-gray-400 px-2 flex items-center gap-2"
                                :class="constatColor"
                            >
                                <component
                                    v-if="constatIcon"
                                    :is="constatIcon"
                                    class="w-6 h-6"
                                />
                                {{ action.constat_libelle }}
                            </span>
                        </div>
                    </div>

                    <div class="flex text-xl gap-20 justify-start mt-8 ml-20">
                        <!-- Responsable -->
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-800">
                                Responsable :
                            </span>
                            <div
                                class="ml-2 text-gray-500 font-semibold border-b border-b-gray-400 px-2"
                            >
                                <div
                                    v-for="(
                                        respo, index
                                    ) in trimmedResponsables"
                                    :key="'responsable-' + index"
                                    class="break-words"
                                >
                                    {{ respo }}
                                </div>
                            </div>
                        </div>

                        <!-- Suivi -->
                        <div class="flex items-start">
                            <span class="ml-6 font-semibold text-gray-800">
                                Suivi :
                            </span>
                            <div
                                class="ml-2 text-gray-500 font-semibold border-b border-b-gray-400 px-2"
                            >
                                <div
                                    v-for="(suivi, index) in trimmedSuivis"
                                    :key="'suivi-' + index"
                                    class="break-words"
                                >
                                    {{ suivi }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conteneur horizontal -->
                    <div class="flex justify-start text-xl gap-20 mt-6">
                        <!-- Fréquence -->
                        <div class="flex items-start ml-20">
                            <span class="font-semibold text-gray-800 mt-1">
                                Fréquence :
                            </span>

                            <!-- Colonne pour la valeur + détails -->
                            <div class="ml-2 flex flex-col">
                                <!-- Valeur principale -->
                                <div
                                    class="font-semibold bg-white border-b border-b-gray-400 px-2 text-gray-500"
                                >
                                    {{ action.frequence }}
                                </div>

                                <!-- Détails en-dessous -->
                                <div
                                    v-if="frequenceDetails"
                                    class="mt-1 text-gray-900 font-poppins whitespace-pre-wrap text-sm"
                                >
                                    {{ frequenceDetails }}
                                </div>
                            </div>
                        </div>

                        <!-- Livrable -->
                        <div class="flex items-start">
                            <span class="ml-6 font-semibold text-gray-800">
                                Livrable :
                            </span>
                            <div
                                class="ml-2 font-semibold bg-white border-b border-b-gray-400 px-2 text-gray-500"
                            >
                                {{ action.mesure }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap justify-start gap-12 mt-6 w-full text-xl ml-4"
                    >
                        <!-- Champ Action -->
                        <div class="flex w-[48%] items-start">
                            <span class="w-[10%] font-semibold text-gray-800">
                                Action :
                            </span>
                            <div
                                class="w-[90%] font-semibold bg-white border-b border-b-gray-400 px-2 py-2 text-gray-600 whitespace-pre-wrap break-words"
                                style="max-width: 100%"
                            >
                                {{ action.description }}
                            </div>
                        </div>

                        <!-- Champ Observation -->
                        <div class="flex w-[48%] items-start">
                            <span class="w-[18%] font-semibold text-gray-800">
                                Observation :
                            </span>
                            <div
                                class="w-[82%] font-semibold bg-white border-b border-b-gray-400 px-2 py-2 text-gray-600 whitespace-pre-wrap break-words"
                                style="max-width: 100%"
                            >
                                {{ action.observation }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full mt-5">
                        <div
                            class="w-full border rounded px-4 py-2 text-xl font-semibold text-center"
                            :class="{
                                'bg-green-400 text-white':
                                    action.statut === 'En cours',
                                'bg-red-400 text-white':
                                    action.statut === 'En retard',
                                'bg-purple-400 text-white':
                                    action.statut === 'Abandonné',
                                'bg-gray-400 text-black':
                                    action.statut === 'Clôturé',
                            }"
                        >
                            {{ action.statut }}
                        </div>
                    </div>

                    <div class="flex w-full text-lg justify-end mt-8">
                        <router-link to="/admin/actions/auditinterne"
                            ><button
                                class="w-auto transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
