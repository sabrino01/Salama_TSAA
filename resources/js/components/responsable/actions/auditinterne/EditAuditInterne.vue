<script setup>
import Sidebar from "../../../assets/SidebarResponsable.vue";
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
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

// Router et route pour récupérer l'ID de l'action
const router = useRouter();
const route = useRoute();
const actionId = route.params.id; // ID de l'action sélectionnée
const frequenceDetails = ref("");

// Données de l'action responsable
const action = ref({
    statut_resp: "",
    observation_resp: "",
    // Autres données pour affichage
    num_actions: "",
    description: "",
    constat_libelle: "",
    source_libelle: "",
    type_action_libelle: "",
    nom_utilisateur: "",
    date: "",
    mesure: "",
    observation: "",
});

// Fonction pour formater la date
const formatDate = (date) => {
    if (!date) return ""; // Si la date est vide, retourner une chaîne vide
    if (date.includes("/")) return date; // Si la date est déjà formatée, la retourner telle quelle
    const [year, month, day] = date.split("-");
    return `${day}/${month}/${year}`; // Reformater en dd/mm/yyyy
};

// Récupérer les données de l'utilisateur connecté pour le filtrage
const user = JSON.parse(localStorage.getItem("user") || "{}");
const userId = user?.responsables_id;

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

// Charger les données de l'action sélectionnée
onMounted(async () => {
    try {
        // Charger les données de l'action responsable sélectionnée avec le filtrage par responsable
        const params = {};
        if (user?.role === "responsable" && userId) {
            params.responsable_id = userId;
        }

        const actionResponse = await axios.get(
            `/api/actions-responsables/${actionId}`,
            {
                params,
            }
        );
        const fetchedAction = actionResponse.data;
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

        // Assigner les données récupérées
        Object.assign(action.value, actionResponse.data);

        // S'assurer que observation_resp est une chaîne vide si null
        if (
            action.value.observation_resp === null ||
            action.value.observation_resp === undefined
        ) {
            action.value.observation_resp = "";
        }
    } catch (error) {
        console.error("Erreur lors du chargement des données :", error);
        toast.error("Erreur lors du chargement des données de l'action");
    }
});

// Fonction pour enregistrer les modifications
const modifierResponsablesAI = async () => {
    try {
        // Validation basique
        if (!action.value.statut_resp) {
            toast.error("Veuillez sélectionner un statut");
            return;
        }

        // Envoyer seulement les données modifiables
        const dataToUpdate = {
            statut_resp: action.value.statut_resp,
            observation_resp: action.value.observation_resp || null,
        };

        // Préparer les paramètres pour l'URL (query parameters)
        const params = {};
        if (user?.role === "responsable" && userId) {
            params.responsable_id = userId;
        }

        // Envoyer la requête PUT avec les paramètres
        await axios.put(`/api/actions-responsables/${actionId}`, dataToUpdate, {
            params: params,
        });

        router.push("/responsable/actions/auditinterne");
        toast.success("Action Audit Interne modifiée avec succès !");
    } catch (error) {
        console.error("Erreur lors de la modification :", error);

        // Afficher un message d'erreur plus détaillé
        if (error.response) {
            // Le serveur a répondu avec un code d'erreur
            toast.error(
                `Erreur ${error.response.status}: ${
                    error.response.data.message ||
                    "Erreur lors de la modification"
                }`
            );
        } else if (error.request) {
            // La requête a été envoyée mais aucune réponse reçue
            toast.error("Aucune réponse du serveur");
        } else {
            // Erreur lors de la configuration de la requête
            toast.error(
                "Erreur lors de la modification de l'action Audit Interne"
            );
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
                        Editer Audit Interne
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez éditer l'Audit Interne
                        pour pouvoir faire des modifications sur l'Audit Interne
                        sélectionnée.
                    </p>
                </div>

                <!-- Formulaire de modification d'Audit Interne -->
                <div class="w-full mt-5">
                    <div class="flex w-[40%] justify-end">
                        <input
                            type="text"
                            id="num_actions"
                            class="w-[14%] border rounded-md px-4 py-2 bg-gray-100"
                            v-model="action.num_actions"
                        />
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

                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="statut"
                            class="w-[7%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Statut :
                        </label>
                        <select
                            v-model="action.statut_resp"
                            id="suivi"
                            class="mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option value="En cours">En cours</option>
                            <option value="En retard">En retard</option>
                            <option value="Clôturé">Clôturé</option>
                            <option value="Abandonné">Abandonné</option>
                        </select>
                    </div>

                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="observation"
                            class="w-[12%] ml-4 text-lg items-start font-semibold text-gray-800"
                        >
                            Obsérvation :
                        </label>
                        <textarea
                            id="observation"
                            class="w-[56%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            v-model="action.observation_resp"
                        ></textarea>
                    </div>

                    <div class="flex w-full justify-end mt-6">
                        <router-link to="/responsable/actions/auditinterne"
                            ><button
                                class="w-auto transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="modifierResponsablesAI"
                            class="w-auto bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
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
