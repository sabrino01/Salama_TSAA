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
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { frequenceOptions } from "../../../../utils/frequenceOptions.js";
import axios from "axios";

const router = useRouter();

// Récupérer les données de l'utilisateur depuis le localStorage
const user = JSON.parse(localStorage.getItem("user"));

// Numéro d'action
const num_actions = ref([]);

// Données pour les champs select
const sources = ref([]);
const typeActions = ref([]);
const responsables = ref([]);
const suivis = ref([]);
const constats = ref([]);

// Données du formulaire
const action = ref({
    num_actions: num_actions,
    sources_id: "",
    type_actions_id: "",
    responsables_id: "",
    suivis_id: "",
    constats_id: "",
    frequence: "",
    description: "",
    observation: "",
    users_id: user?.id || null,
});

// Options pour le champ fréquence
const options = frequenceOptions;
const selectedOption = ref(""); // Option sélectionnée
const showModal = ref(false); // Contrôle de l'affichage du modal

// Charger les données nécessaires pour les champs select et le numéro d'actions
onMounted(async () => {
    try {
        const response = await axios.get("/api/actions/createAI");
        num_actions.value = response.data.num_actions;
        sources.value = response.data.sources;
        typeActions.value = response.data.typeActions;
        responsables.value = response.data.responsables;
        suivis.value = response.data.suivis;
        constats.value = response.data.constats;
    } catch (error) {
        toast.error("Erreur lors du chargement des données", error);
    }
});

const handleOptionChange = () => {
    if (
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
        action.value.frequence = selectedOption.value; // Enregistre directement la fréquence pour les autres options
    }
};

const enregistrerAction = async () => {
    try {
        // Préparation des données de fréquence avant envoi
        if (
            typeof action.value.frequence === "object" &&
            action.value.frequence !== null
        ) {
            action.value.frequence = JSON.stringify(action.value.frequence);
        }

        console.log(action.value);
        await axios.post("/api/actions", action.value);
        router.push("/admin/actions/auditinterne");
        toast.success("Action ajoutée avec succès");
    } catch (error) {
        toast.error("Erreur lors de l'ajout de l'action", error);
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
                        Ajout Actions Audit Interne
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez ajouter de nouvelle Audit
                        Interne avec ses informations et encore plus .
                    </p>
                </div>

                <!-- Formulaire d'ajout d'audit interne -->
                <div class="w-full mt-5">
                    <div class="flex w-[40%] justify-center">
                        <input
                            type="text"
                            id="date"
                            class="w-[14%] border rounded-md px-4 py-2 bg-gray-100"
                            :value="num_actions"
                            readonly
                        />
                    </div>
                    <div class="flex w-[60%] mt-5">
                        <label
                            for="description"
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action :
                        </label>
                        <textarea
                            id="description"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
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
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
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
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
                            <option
                                v-for="type in typeActions"
                                :key="type.id"
                                :value="type.id"
                            >
                                {{ type.code }} - {{ type.libelle }}
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
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
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
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
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
                            for="action"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Constat :
                        </label>
                        <select
                            v-model="action.constats_id"
                            class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
                            <option value="" class="text-center">
                                --- Options ---
                            </option>
                            <option
                                v-for="constat in constats"
                                :key="constat.id"
                                :value="constat.id"
                            >
                                {{ constat.code }} - {{ constat.libelle }}
                            </option>
                        </select>
                    </div>

                    <!-- Ajout du champ Fréquence -->
                    <div class="flex w-auto items-center mt-5">
                        <label
                            for="frequence"
                            class="ml-4 text-lg font-semibold text-gray-800"
                        >
                            Fréquence :
                        </label>
                        <select
                            v-model="selectedOption"
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
                        <!-- Vous pouvez ajouter d'autres composants ici si nécessaire -->
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
                            class="w-[40%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            v-model="action.observation"
                        />
                    </div>

                    <div class="flex w-[50%] justify-end mt-5">
                        <router-link to="/admin/actions/auditinterne"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            @click="enregistrerAction"
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
