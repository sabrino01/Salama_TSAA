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

// Router et route pour récupérer l'ID de l'action
const router = useRouter();
const route = useRoute();
const actionId = route.params.id; // ID de l'action sélectionnée

// Données du formulaire
const action = ref({
    description: "",
    sources_id: "",
    type_actions_id: "",
    responsables_id: "",
    suivis_id: "",
    constats_id: "",
    observation: "",
    frequence: "",
    mesure: "",
    statut: "",
});

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
        const actionResponse = await axios.get(
            `/api/actions/auditinterne/${actionId}`
        );
        Object.assign(action.value, actionResponse.data);

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
    } catch (error) {
        console.error("Erreur lors du chargement des données :", error);
    }
});

// Gérer le changement d'option de fréquence
const handleOptionChange = () => {
    if (selectedOption.value === "Tout l'année") {
        const frequenceObject = { type: "Tout l'année" };
        action.value.frequence = JSON.stringify(frequenceObject); // Première conversion
        action.value.frequence = JSON.stringify(action.value.frequence); // Deuxième conversion pour échapper
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
        // Préparation des données de fréquence avant envoi
        if (typeof action.value.frequence === "object") {
            action.value.frequence = JSON.stringify(action.value.frequence);
            action.value.frequence = JSON.stringify(action.value.frequence); // Deuxième conversion pour échapper
        }
        await axios.put(`/api/actions/auditinterne/${actionId}`, action.value);
        router.push("/admin/actions/auditinterne");
        toast.success("Action Audit Interne modifiée avec succès !");
    } catch (error) {
        toast.error(
            "Erreur lors de la modification de l'action Audit Interne",
            error
        );
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
                    <div class="flex w-[80%] mt-5">
                        <label
                            for="description"
                            class="w-[6%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action :
                        </label>
                        <textarea
                            id="description"
                            class="w-[75%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            v-model="action.description"
                        ></textarea>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="source"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Source :
                        </label>
                        <select
                            v-model="action.sources_id"
                            id="source"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option
                                v-for="source in sources"
                                :key="source.id"
                                :value="source.id"
                            >
                                {{ source.code }} - {{ source.libelle }}
                            </option>
                        </select>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="typeactions"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Type d'actions :
                        </label>
                        <select
                            v-model="action.type_actions_id"
                            id="typeactions"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option
                                v-for="typeAction in typeActions"
                                :key="typeAction.id"
                                :value="typeAction.id"
                            >
                                {{ typeAction.code }} - {{ typeAction.libelle }}
                            </option>
                        </select>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="responsable"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Responsable :
                        </label>
                        <select
                            v-model="action.responsables_id"
                            id="responsable"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option
                                v-for="responsable in responsables"
                                :key="responsable.id"
                                :value="responsable.id"
                            >
                                {{ responsable.code }} -
                                {{ responsable.libelle }}
                            </option>
                        </select>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="suivi"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Suivi :
                        </label>
                        <select
                            v-model="action.suivis_id"
                            id="suivi"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option
                                v-for="suivi in suivis"
                                :key="suivi.id"
                                :value="suivi.id"
                            >
                                {{ suivi.nom }}
                            </option>
                        </select>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="constat"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Constat :
                        </label>
                        <select
                            v-model="action.constats_id"
                            id="constat"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" disabled>--- Options ---</option>
                            <option
                                v-for="constat in constats"
                                :key="constat.id"
                                :value="constat.id"
                            >
                                {{ constat.code }} - {{ constat.libelle }}
                            </option>
                        </select>
                    </div>
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="frequence"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Fréquence :
                        </label>
                        <select
                            v-model="selectedOption"
                            id="frequence"
                            @change="handleOptionChange"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
                            <option
                                v-for="option in options"
                                :key="option"
                                :value="option"
                            >
                                {{ option }}
                            </option>
                            <!-- Ajouter une option pour afficher la valeur brute si elle ne correspond pas -->
                            <option
                                v-if="
                                    !options.includes(selectedOption) &&
                                    selectedOption
                                "
                                :value="selectedOption"
                            >
                                {{ selectedOption }}
                            </option>
                        </select>
                    </div>

                    <!-- Composants dynamiques -->
                    <div class="mt-5">
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
                            v-if="selectedOption === 'Tout l\'année'"
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
                            v-if="selectedOption === 'Quadrimestriel'"
                            v-model:showModal="showModal"
                            v-model="action.frequence"
                        />
                        <FrequenceSemestriel
                            v-if="selectedOption === 'Semestriel'"
                            v-model:showModal="showModal"
                            v-model="action.frequence"
                        />
                    </div>

                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="observation"
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Obsérvation :
                        </label>
                        <input
                            type="text"
                            id="observation"
                            class="w-[48%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            v-model="action.observation"
                        />
                    </div>

                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="mesure"
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Mesure :
                        </label>
                        <input
                            type="text"
                            id="mesure"
                            class="w-[52%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            v-model="action.mesure"
                        />
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

                    <div class="flex w-[80%] justify-end mt-5">
                        <router-link to="/admin/actions/auditinterne"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="modifierAI"
                            class="w-[10%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
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
