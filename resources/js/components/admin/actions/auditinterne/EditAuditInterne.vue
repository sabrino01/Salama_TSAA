<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import FrequencePonctuel from "../../../assets/FrequencePonctuel.vue";
import FrequenceAnnuel from "../../../assets/FrequenceAnnuel.vue";
import FrequenceQuotidien from "../../../assets/FrequenceQuotidien.vue";
import FrequenceHebdomadaire from "../../../assets/FrequenceHebdomadaire.vue";
import FrequenceMensuel from "../../../assets/FrequenceMensuel.vue";
import FrequenceBimestriel from "../../../assets/FrequenceBimestriel.vue";
import FrequenceTrimestriel from "../../../assets/FrequenceTrimestriel.vue";
import FrequenceQuadrimestriel from "../../../assets/FrequenceQuadrimestriel.vue";
import FrequenceSemestriel from "../../../assets/FrequenceSemestriel.vue";
import FrequenceToutAnnee from "../../../assets/FrequenceToutAnnee.vue";
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import { frequenceOptions } from "../../../../utils/frequenceOptions.js";
import { useStatusManager } from "../../../../utils/usesStatusManager.js";
import { Plus, Trash } from "lucide-vue-next";

// Router et route pour récupérer l'ID de l'action
const router = useRouter();
const route = useRoute();
const actionId = route.params.id; // ID de l'action sélectionnée
const nouvelleObservationSuivi = ref("");
const dateObservationSuivi = ref(""); // Nouvelle ref pour la date

// Données du formulaire
const action = ref({
    description: "",
    sources_id: "",
    type_actions_id: "",
    responsables_id: [""],
    suivis_id: [""],
    constats_id: "",
    observation: "",
    frequence: "",
    mesure: "",
    statut: "",
});

// Initialisez le composable
const { isCheckingStatus, statusMessage, checkSingleActionStatus } =
    useStatusManager();

// Fonction pour vérifier le statut manuellement
const verifierStatut = async () => {
    if (!actionId) {
        toast.error("ID d'action manquant");
        return;
    }

    const result = await checkSingleActionStatus(actionId);

    if (result.success) {
        if (result.updated) {
            // Le statut a été mis à jour
            action.value.statut = result.newStatus;
            toast.success(result.message);
        } else {
            toast.info(result.message);
        }
    } else {
        toast.error(result.message);
    }
};

// Fonction pour vérifier automatiquement au chargement
const verifierStatutAutomatique = async () => {
    if (!actionId) return;

    const result = await checkSingleActionStatus(actionId);

    if (result.success && result.updated) {
        action.value.statut = result.newStatus;
        console.log("Statut mis à jour automatiquement:", result.newStatus);
    }
};

const formatDateUpdate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Ajouter ou retirer un suivi
const addSuivi = () => {
    action.value.suivis_id.push("");
};
const removeSuivi = (index) => {
    action.value.suivis_id.splice(index, 1);
};

// Ajouter ou retirer un responsable
const addResponsable = () => {
    action.value.responsables_id.push("");
};
const removeResponsable = (index) => {
    action.value.responsables_id.splice(index, 1);
};

// Variable pour stocker la fréquence originale
const originalFrequence = ref("");
// Variable pour indiquer si la fréquence a été modifiée
const frequenceModified = ref(false);

// Options pour les champs select
const sources = ref([]);
const typeActions = ref([]);
const responsables = ref([]);
const suivis = ref([]);
const constats = ref([]);

// Options pour le champ fréquence
const options = frequenceOptions;
const selectedOption = ref(""); // Option sélectionnée
const showModal = ref(false); // Contrôle de l'affichage du modal

// Fonction pour normaliser la fréquence (gérer les chaînes JSON imbriquées)
const normalizeFrequence = (frequenceValue) => {
    if (!frequenceValue) return "";

    // Essayer de détecter et normaliser les JSON imbriqués
    let normalizedValue = frequenceValue;
    let isNormalized = false;

    // Boucle pour gérer les multiples niveaux d'encodage
    while (!isNormalized) {
        try {
            // Vérifier si c'est une chaîne JSON valide
            if (
                typeof normalizedValue === "string" &&
                (normalizedValue.startsWith('"') ||
                    normalizedValue.startsWith("{"))
            ) {
                const parsed = JSON.parse(normalizedValue);

                // Si le résultat est encore une chaîne qui ressemble à du JSON, continuer
                if (
                    typeof parsed === "string" &&
                    (parsed.startsWith('"') || parsed.startsWith("{"))
                ) {
                    normalizedValue = parsed;
                } else {
                    // Si c'est un objet ou une chaîne non-JSON, on a fini
                    normalizedValue = parsed;
                    isNormalized = true;
                }
            } else {
                // Si ce n'est pas une chaîne JSON, on a terminé
                isNormalized = true;
            }
        } catch (e) {
            // Si on ne peut pas parser, c'est probablement déjà normalisé
            isNormalized = true;
        }
    }

    // Convertir en chaîne JSON si c'est un objet
    if (typeof normalizedValue === "object" && normalizedValue !== null) {
        return JSON.stringify(normalizedValue);
    }

    return normalizedValue;
};

// Fonction pour obtenir la date actuelle au format YYYY-MM-DD
const getCurrentDate = () => {
    return new Date().toISOString().split("T")[0];
};

// Gérer le changement d'option de fréquence
const handleOptionChange = () => {
    frequenceModified.value = true; // Marquer que la fréquence a été modifiée

    if (selectedOption.value === "Tout l'année") {
        const frequenceObject = { type: "Tout l'année" };
        action.value.frequence = JSON.stringify(frequenceObject); // Première conversion
    } else if (
        selectedOption.value === "Ponctuel" ||
        selectedOption.value === "Annuel" ||
        selectedOption.value === "Quotidien" ||
        selectedOption.value === "Hebdomadaire" ||
        selectedOption.value === "Mensuel" ||
        selectedOption.value === "Bimestriel" ||
        selectedOption.value === "Trimestriel" ||
        selectedOption.value === "Quadrimestriel" ||
        selectedOption.value === "Semestriel"
    ) {
        showModal.value = true; // Ouvre le modal pour les options spécifiques
    } else {
        action.value.frequence = selectedOption.value;
    }
};

// Fonction pour enregistrer les modifications
const modifierAI = async () => {
    try {
        // Vérifier le statut avant la modification
        await verifierStatutAutomatique();

        // Préparation des données à envoyer
        const dataToSend = { ...action.value };

        // Ne modifie la fréquence que si elle a été changée explicitement
        if (!frequenceModified.value) {
            dataToSend.frequence = originalFrequence.value;
        } else {
            // Normaliser la fréquence avant l'envoi
            if (typeof dataToSend.frequence === "object") {
                dataToSend.frequence = JSON.stringify(dataToSend.frequence);
            } else if (typeof dataToSend.frequence === "string") {
                // S'assurer qu'il n'y a pas de double encodage
                dataToSend.frequence = normalizeFrequence(dataToSend.frequence);
            }
        }

        // Envoyer la mise à jour
        const response = await axios.put(
            `/api/actions/${actionId}`,
            dataToSend
        );

        // Vérifier si le backend a aussi mis à jour le statut
        if (response.data.status_check && response.data.status_check.updated) {
            action.value.statut = response.data.status_check.status;
        }

        router.push("/admin/actions/auditinterne");
        toast.success("Action Audit Interne modifiée avec succès !");
    } catch (error) {
        toast.error(
            "Erreur lors de la modification de l'action Audit Interne",
            error
        );
    }
};

// Ajouter une observation de suivi
const ajouterObservationSuivi = async () => {
    if (!nouvelleObservationSuivi.value.trim()) {
        return;
    }
    try {
        const response = await axios.put(`/api/actions/${route.params.id}`, {
            ...action.value,
            nouvelle_observation_suivi: nouvelleObservationSuivi.value,
            date_observation_suivi: dateObservationSuivi.value, // Ajouter la date sélectionnée
        });

        // Mettre à jour les données locales
        action.value = response.data.action;

        // Réinitialiser les champs
        nouvelleObservationSuivi.value = "";
        dateObservationSuivi.value = getCurrentDate(); // Réinitialiser avec la date actuelle

        // Notification de succès
        toast.success("Observation de suivi ajoutée avec succès!");
    } catch (error) {
        console.error("Erreur lors de l'ajout de l'observation:", error);
        if (error.response?.status === 400) {
            toast.error(error.response.data.message);
        } else {
            toast.error("Erreur lors de l'ajout de l'observation");
        }
    }
};

// Fonction pour supprimer une observation de suivi
const supprimerObservationSuivi = async (index) => {
    try {
        const response = await axios.put(`/api/actions/${route.params.id}`, {
            ...action.value,
            supprimer_observation_index: index,
        });

        // Mettre à jour les données locales
        action.value = response.data.action;

        // Notification de succès
        toast.success("Observation de suivi supprimée avec succès!");
    } catch (error) {
        console.error("Erreur lors de la suppression de l'observation:", error);
        if (error.response?.status === 400) {
            toast.error(error.response.data.message);
        } else {
            toast.error("Erreur lors de la suppression de l'observation");
        }
    }
};

const formatDateSuivi = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Charger les données de l'action sélectionnée et les options
onMounted(async () => {
    try {
        // Charger les options pour les champs select
        const optionsResponse = await axios.get("/api/actions/createAI");
        sources.value = optionsResponse.data.sources;
        typeActions.value = optionsResponse.data.typeActions;
        responsables.value = optionsResponse.data.responsables;
        suivis.value = optionsResponse.data.suivis;
        constats.value = optionsResponse.data.constats;

        // Charger les données de l'action sélectionnée
        const actionResponse = await axios.get(`/api/actions/${actionId}`);
        Object.assign(action.value, actionResponse.data);

        if (action.value.responsables_id) {
            action.value.responsables_id = action.value.responsables_id
                .split(",")
                .map(Number);
        }

        if (action.value.suivis_id) {
            action.value.suivis_id = action.value.suivis_id
                .split(",")
                .map(Number);
        }

        // Normaliser et stocker la valeur originale de la fréquence
        action.value.frequence = normalizeFrequence(action.value.frequence);
        originalFrequence.value = action.value.frequence;

        // Définir l'option sélectionnée en fonction de la fréquence existante
        if (action.value.frequence) {
            try {
                const parsedFrequence = JSON.parse(action.value.frequence);
                selectedOption.value =
                    parsedFrequence.type || action.value.frequence;
            } catch {
                // Si la valeur brute ne peut pas être parsée, utilisez-la directement
                selectedOption.value = action.value.frequence;
            }
        }
        // Vérifier automatiquement le statut après le chargement
        await verifierStatutAutomatique();

        // Initialiser la date avec la date actuelle
        dateObservationSuivi.value = getCurrentDate();
    } catch (error) {
        console.error("Erreur lors du chargement des données :", error);
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

                    <div class="flex w-[60%] items-center">
                        <label
                            for="date"
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Date :
                        </label>
                        <input
                            type="date"
                            id="date"
                            class="w-[16%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            disabled
                            v-model="action.date"
                        />
                    </div>

                    <div class="flex flex-wrap gap-x-8 gap-y-6 mt-5">
                        <!-- Source -->
                        <div class="flex items-center ml-4">
                            <label
                                for="source"
                                class="text-lg font-semibold text-gray-800 w-20"
                            >
                                Source :
                            </label>
                            <select
                                v-model="action.sources_id"
                                class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="">--- Options ---</option>
                                <option
                                    v-for="source in sources"
                                    :key="source.id"
                                    :value="source.id"
                                >
                                    {{ source.code }} - {{ source.libelle }}
                                </option>
                            </select>
                        </div>

                        <!-- Type d'actions -->
                        <div class="flex items-center">
                            <label
                                for="typeactions"
                                class="text-lg font-semibold text-gray-800 mr-2 w-30"
                            >
                                Type d'actions :
                            </label>
                            <select
                                v-model="action.type_actions_id"
                                class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="">--- Options ---</option>
                                <option
                                    v-for="type in typeActions"
                                    :key="type.id"
                                    :value="type.id"
                                >
                                    {{ type.code }} - {{ type.libelle }}
                                </option>
                            </select>
                        </div>

                        <!-- Constat -->
                        <div class="flex items-center">
                            <label
                                for="action"
                                class="text-lg font-semibold text-gray-800 mr-2 w-20"
                            >
                                Constat :
                            </label>
                            <select
                                v-model="action.constats_id"
                                class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="">--- Options ---</option>
                                <option
                                    v-for="constat in constats"
                                    :key="constat.id"
                                    :value="constat.id"
                                >
                                    {{ constat.code }} - {{ constat.libelle }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Suivis -->
                    <div class="flex flex-wrap w-full ml-4 mt-5">
                        <label
                            class="text-lg font-semibold text-gray-800 mt-2 w-20"
                            >Suivis :</label
                        >
                        <div
                            v-for="(suivi, index) in action.suivis_id"
                            :key="'suivi-' + index"
                            class="flex items-center gap-2"
                        >
                            <select
                                v-model="action.suivis_id[index]"
                                class="border border-gray-400 rounded-md ml-2 mt-2 px-4 py-2 bg-transparent"
                            >
                                <option value="">--- Options ---</option>
                                <option
                                    v-for="s in suivis"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.nom }}
                                </option>
                            </select>
                            <button
                                type="button"
                                @click="removeSuivi(index)"
                                class="text-red-600 mt-2 font-bold text-xl"
                            >
                                <Trash />
                            </button>
                        </div>
                        <button
                            type="button"
                            @click="addSuivi"
                            class="text-green-600 mt-2 font-bold"
                        >
                            <Plus />
                        </button>
                    </div>

                    <!-- Responsables -->
                    <div class="flex flex-wrap w-full ml-4 mt-5">
                        <label
                            class="text-lg font-semibold text-gray-800 mt-2 mr-4 w-30"
                            >Responsables :</label
                        >
                        <div
                            v-for="(
                                responsable, index
                            ) in action.responsables_id"
                            :key="'responsable-' + index"
                            class="flex items-center gap-2"
                        >
                            <select
                                v-model="action.responsables_id[index]"
                                class="border border-gray-400 rounded-md ml-2 mt-2 px-4 py-2 bg-transparent"
                            >
                                <option value="">--- Options ---</option>
                                <option
                                    v-for="r in responsables"
                                    :key="r.id"
                                    :value="r.id"
                                >
                                    {{ r.code }} - {{ r.libelle }}
                                </option>
                            </select>
                            <button
                                type="button"
                                @click="removeResponsable(index)"
                                class="text-red-600 mt-2 font-bold text-xl"
                            >
                                <trash />
                            </button>
                        </div>
                        <button
                            type="button"
                            @click="addResponsable"
                            class="text-green-600 mt-2 font-bold"
                        >
                            <Plus />
                        </button>
                    </div>

                    <!-- Groupe Fréquence et Mesure -->
                    <div class="flex flex-wrap gap-x-8 gap-y-6 mt-6">
                        <!-- Fréquence -->
                        <div class="flex items-start ml-4">
                            <label
                                for="frequence"
                                class="text-lg font-semibold text-gray-800 mr-4 w-30"
                            >
                                Fréquence :
                            </label>
                            <div class="ml-2 flex flex-col">
                                <select
                                    v-model="selectedOption"
                                    @change="handleOptionChange"
                                    class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                >
                                    <option value="">--- Options ---</option>
                                    <option
                                        v-for="option in options"
                                        :key="option"
                                        :value="option"
                                    >
                                        {{ option }}
                                    </option>
                                </select>

                                <div
                                    class="mt-1 text-gray-900 font-poppins whitespace-pre-wrap text-sm"
                                >
                                    <FrequencePonctuel
                                        v-if="selectedOption === 'Ponctuel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceAnnuel
                                        v-if="selectedOption === 'Annuel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceQuotidien
                                        v-if="selectedOption === 'Quotidien'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceToutAnnee
                                        v-if="
                                            selectedOption === 'Tout l\'année'
                                        "
                                        v-model="action.frequence"
                                    />
                                    <FrequenceHebdomadaire
                                        v-if="selectedOption === 'Hebdomadaire'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceMensuel
                                        v-if="selectedOption === 'Mensuel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceBimestriel
                                        v-if="selectedOption === 'Bimestriel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceTrimestriel
                                        v-if="selectedOption === 'Trimestriel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceQuadrimestriel
                                        v-if="
                                            selectedOption === 'Quadrimestriel'
                                        "
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                    <FrequenceSemestriel
                                        v-if="selectedOption === 'Semestriel'"
                                        v-model:showModal="showModal"
                                        v-model="action.frequence"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- livrable -->
                        <div class="flex items-start">
                            <label
                                for="livrable"
                                class="text-lg font-semibold text-gray-800 w-20"
                            >
                                Livrable :
                            </label>
                            <input
                                type="text"
                                id="livrable"
                                v-model="action.mesure"
                                class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            />
                        </div>
                    </div>

                    <!-- Composants dynamiques -->

                    <!-- Groupe Action & Observation -->
                    <div class="flex flex-wrap gap-x-6 gap-y-6 mt-6">
                        <!-- Action -->
                        <div class="flex items-start w-full md:w-[40%] ml-4">
                            <label
                                for="description"
                                class="text-lg font-semibold text-gray-800 w-20"
                            >
                                Action :
                            </label>
                            <textarea
                                id="description"
                                v-model="action.description"
                                class="flex-1 border border-gray-400 rounded-md px-4 py-2 bg-transparent resize-none"
                                rows="3"
                            ></textarea>
                        </div>

                        <!-- Observation -->
                        <div class="flex items-start w-full md:w-[40%]">
                            <label
                                for="observation"
                                class="text-lg font-semibold text-gray-800 mr-2 w-30"
                            >
                                Obsérvation :
                            </label>
                            <textarea
                                id="observation"
                                v-model="action.observation"
                                class="flex-1 border border-gray-400 rounded-md px-4 py-2 bg-transparent resize-none"
                                rows="3"
                            ></textarea>
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
                            v-model="action.statut"
                            id="statut"
                            class="mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            :class="{
                                'border-red-500 bg-red-50':
                                    action.statut === 'En retard',
                            }"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option value="En cours">En cours</option>
                            <option value="En retard">En retard</option>
                            <option value="Clôturé">Clôturé</option>
                            <option value="Abandonné">Abandonné</option>
                        </select>

                        <!-- Bouton de vérification du statut -->
                        <button
                            @click="verifierStatut"
                            :disabled="isCheckingStatus"
                            class="px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{
                                isCheckingStatus
                                    ? "Vérification..."
                                    : "Vérifier Statut"
                            }}
                        </button>
                    </div>
                    <!-- Indicateur visuel pour les actions en retard -->
                    <div
                        v-if="action.statut === 'En retard'"
                        class="flex items-center gap-2 mt-2 p-3 ml-4 bg-red-50 border border-red-200 rounded-md"
                    >
                        <svg
                            class="w-5 h-5 text-red-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            />
                        </svg>
                        <span class="text-red-800 font-medium"
                            >Cette action est en retard selon sa fréquence</span
                        >
                    </div>

                    <!-- Affichage du message de statut -->
                    <div
                        v-if="statusMessage"
                        class="mt-2 p-2 ml-4 bg-blue-50 border border-blue-200 rounded text-blue-800 text-sm"
                    >
                        {{ statusMessage }}
                    </div>

                    <!-- Section des mises à jour des responsables ET suivis -->
                    <div
                        class="bg-white shadow rounded-lg p-6 mt-8"
                        v-if="action.has_updates"
                    >
                        <h3 class="text-xl font-bold mb-4">
                            <i class="fas fa-eye mr-2"></i>
                            Mises à jour des Responsables et Suivis
                        </h3>

                        <div
                            class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4"
                        >
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Ces informations proviennent des responsables et
                                suivis et ne peuvent pas être modifiées ici.
                            </p>
                        </div>

                        <!-- Mises à jour des responsables -->
                        <div
                            v-if="action.has_responsables_updates"
                            class="mb-6"
                        >
                            <h4
                                class="text-lg font-semibold mb-3 text-blue-700"
                            >
                                <i class="fas fa-users mr-2"></i>
                                Mises à jour des Responsables
                            </h4>

                            <div
                                v-for="update in action.responsables_updates"
                                :key="'resp-' + update.responsables_id"
                                class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <h5
                                        class="font-semibold text-lg text-gray-700"
                                    >
                                        <i class="fas fa-user mr-2"></i>
                                        {{ update.responsable_nom }}
                                    </h5>
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{
                                            formatDateUpdate(update.date_update)
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div>
                                        <strong class="text-blue-600">
                                            <i class="fas fa-flag mr-1"></i>
                                            Statut:
                                        </strong>
                                        <div
                                            class="mt-1 p-3 bg-blue-100 rounded border-l-4 border-blue-500"
                                        >
                                            <p class="text-blue-800">
                                                {{ update.statut_resp }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <strong class="text-green-600">
                                            <i class="fas fa-comment mr-1"></i>
                                            Observation:
                                        </strong>
                                        <div
                                            class="mt-1 p-3 bg-green-100 rounded border-l-4 border-green-500"
                                        >
                                            <p class="text-green-800">
                                                {{ update.observation_resp }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mises à jour des suivis -->
                        <div v-if="action.has_suivis_updates" class="mb-6">
                            <h4
                                class="text-lg font-semibold mb-3 text-purple-700"
                            >
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Mises à jour des Suivis
                            </h4>

                            <div
                                v-for="update in action.suivis_updates"
                                :key="'suivi-' + update.suivis_id"
                                class="border border-purple-200 rounded-lg p-4 mb-4 bg-purple-50"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <h5
                                        class="font-semibold text-lg text-gray-700"
                                    >
                                        <i class="fas fa-clipboard mr-2"></i>
                                        {{ update.suivi_nom }}
                                    </h5>
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{
                                            formatDateUpdate(update.date_update)
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div>
                                        <strong class="text-purple-600">
                                            <i class="fas fa-flag mr-1"></i>
                                            Statut:
                                        </strong>
                                        <div
                                            class="mt-1 p-3 bg-purple-100 rounded border-l-4 border-purple-500"
                                        >
                                            <p class="text-purple-800">
                                                {{ update.statut_suivi }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <strong class="text-orange-600">
                                            <i class="fas fa-comment mr-1"></i>
                                            Observation:
                                        </strong>
                                        <div
                                            class="mt-1 p-3 bg-orange-100 rounded border-l-4 border-orange-500"
                                        >
                                            <p class="text-orange-800">
                                                {{ update.observation_suivi }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message si pas de mises à jour -->
                    <div v-else class="bg-white shadow rounded-lg mt-8">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4"></i>
                            <p>
                                Aucune mise à jour des responsables ou suivis
                                pour cette action.
                            </p>
                        </div>
                    </div>

                    <!-- Section Observation par Suivi -->
                    <div
                        v-if="action.has_updates"
                        class="bg-white shadow rounded-lg p-6 mb-6 mt-8"
                    >
                        <h3 class="text-xl font-bold mb-4">
                            <i
                                class="fas fa-clipboard-list mr-2 text-purple-600"
                            ></i>
                            Observation par Suivi
                        </h3>

                        <div
                            class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4"
                        >
                            <p class="text-sm text-purple-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Cette section permet d'ajouter des observations
                                de suivi puisque des responsables ont mis à jour
                                cette action.
                            </p>
                        </div>

                        <!-- Formulaire pour ajouter une nouvelle observation -->
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold mb-3">
                                <i class="fas fa-plus mr-2 text-green-600"></i>
                                Ajouter une nouvelle observation par suivi
                            </h4>

                            <div>
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        <i class="fas fa-calendar mr-1"></i>
                                        Date de suivi
                                    </label>
                                    <input
                                        type="date"
                                        v-model="dateObservationSuivi"
                                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :max="getCurrentDate()"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Si aucune date n'est sélectionnée, la
                                        date actuelle sera utilisée.
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        <i class="fas fa-edit mr-1"></i>
                                        Observation
                                    </label>
                                    <textarea
                                        v-model="nouvelleObservationSuivi"
                                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        rows="4"
                                        placeholder="Entrez votre observation..."
                                        required
                                    ></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="button"
                                        @click="ajouterObservationSuivi"
                                        :disabled="
                                            !nouvelleObservationSuivi.trim()
                                        "
                                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <Plus class="inline mr-1" />
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Affichage des observations existantes -->
                        <div v-if="action.has_observations_suivi">
                            <h4 class="font-semibold mb-3">
                                <i
                                    class="fas fa-history mr-2 text-blue-600"
                                ></i>
                                Historique des observations par suivi
                            </h4>

                            <div
                                v-for="(
                                    observation, index
                                ) in action.observations_suivi"
                                :key="index"
                                class="border border-blue-200 rounded-lg p-4 mb-3 bg-blue-50"
                            >
                                <div
                                    class="flex justify-between items-start mb-2"
                                >
                                    <span class="text-sm text-blue-600">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ formatDateSuivi(observation.date) }}
                                    </span>
                                    <button
                                        @click="
                                            supprimerObservationSuivi(index)
                                        "
                                        class="text-red-500 hover:text-red-700 hover:bg-red-100 p-1 rounded transition-colors"
                                        title="Supprimer cette observation"
                                    >
                                        <Trash />
                                    </button>
                                </div>
                                <div
                                    class="bg-white rounded p-3 border-l-4 border-blue-500"
                                >
                                    <p class="text-gray-800">
                                        {{ observation.observation }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-4 text-gray-500">
                            <i class="fas fa-clipboard text-2xl mb-2"></i>
                            <p>Aucune observation de suivi pour le moment.</p>
                        </div>
                    </div>

                    <div class="flex w-auto justify-end mt-6">
                        <router-link to="/admin/actions/auditinterne"
                            ><button
                                class="w-auto transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="modifierAI"
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
