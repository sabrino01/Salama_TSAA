<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { ref, onMounted } from "vue";
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

                <!-- Vue d'Audit Interne -->
                <div class="w-full mt-5">
                    <div class="flex w-full items-center">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Date :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            formatDate(action.date)
                        }}</span>
                    </div>
                    <div class="flex w-full mt-5">
                        <span
                            class="w-[5%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action :
                        </span>
                        <span class="w-[95%] text-lg font-semibold">{{
                            action.description
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Source :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.source_libelle
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Type d'actions :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.type_action_libelle
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Responsable :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.responsable_libelle
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Suivi :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.suivi_nom
                        }}</span>
                    </div>
                    <div class="flex w-auto mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Fréquence :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.frequence
                        }}</span>
                    </div>
                    <div
                        v-if="frequenceDetails"
                        class="flex w-full items-start mt-2 ml-8 text-gray-600 whitespace-pre-wrap"
                    >
                        <span class="text-lg text-black">{{
                            frequenceDetails
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Constat :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.constat_libelle
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Mesure :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.mesure
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Obsérvation :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.observation
                        }}</span>
                    </div>
                    <div class="flex w-full items-center mt-5">
                        <span class="ml-4 text-lg font-semibold text-gray-800">
                            Statut :
                        </span>
                        <span class="ml-2 text-lg font-semibold">{{
                            action.statut
                        }}</span>
                    </div>
                    <div class="flex w-[61.6%] justify-center mt-5">
                        <router-link to="/user/actions/auditinterne"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <router-link
                            :to="`/user/actions/auditinterne/editer/${action.id}`"
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
