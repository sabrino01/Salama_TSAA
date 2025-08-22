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
import { useRouter } from "vue-router";
import { frequenceOptions } from "../../../../utils/frequenceOptions.js";
import axios from "axios";
import { Plus, Trash } from "lucide-vue-next";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

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
    responsables_id: [""], // tableau pour plusieurs responsables
    suivis_id: [""], // tableau pour plusieurs suivis
    constats_id: "",
    frequence: "",
    description: "",
    mesure: "",
    observation: "",
    action: "",
    users_id: user?.id || null,
});

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

// Options pour le champ fréquence
const options = frequenceOptions;
const selectedOption = ref(""); // Option sélectionnée
const showModal = ref(false); // Contrôle de l'affichage du modal

// Charger les données nécessaires pour les champs select et le numéro d'actions
onMounted(async () => {
    try {
        const response = await axios.get("/api/actions/createEnquete");
        num_actions.value = response.data.num_actions;
        sources.value = response.data.sources;
        typeActions.value = response.data.typeActions;
        responsables.value = response.data.responsables;
        suivis.value = response.data.suivis;
        constats.value = response.data.constats;
    } catch (error) {
        toast.error("Erreur lors du chargement des données", error);
    }
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});

const handleOptionChange = () => {
    if (selectedOption.value === "Tout l'année") {
        // Enregistrer directement la fréquence pour "Tout l'année"
        action.value.frequence = JSON.stringify({ type: "Tout l'année" });
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

        await axios.post("/api/actions", action.value);
        router.push("/admin/actions/enquete");
        toast.success("Action ajoutée avec succès");
    } catch (error) {
        toast.error("Erreur lors de l'ajout de l'action", error);
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
                            Ajout Enquête de Satisfaction
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans cet espace, vous pourriez ajouter de
                                nouvelle Enquête de Satisfaction avec ses
                                informations et encore plus .
                            </p>
                        </div>

                        <!-- Formulaire d'ajout d'audit interne -->
                        <div class="w-full mt-5">
                            <div class="flex w-[40%] justify-end">
                                <input
                                    type="text"
                                    id="num_actions"
                                    class="w-[14%] border rounded-md px-4 py-2 bg-gray-100"
                                    :value="num_actions"
                                    readonly
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
                                        <option value="">
                                            --- Options ---
                                        </option>
                                        <option
                                            v-for="source in sources"
                                            :key="source.id"
                                            :value="source.id"
                                        >
                                            {{ source.code }} -
                                            {{ source.libelle }}
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
                                        <option value="">
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
                                        <option value="">
                                            --- Options ---
                                        </option>
                                        <option
                                            v-for="constat in constats"
                                            :key="constat.id"
                                            :value="constat.id"
                                        >
                                            {{ constat.code }} -
                                            {{ constat.libelle }}
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
                                        <option value="">
                                            --- Options ---
                                        </option>
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
                                        <option value="">
                                            --- Options ---
                                        </option>
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
                            <div class="flex flex-wrap gap-x-8 gap-y-6 mt-5">
                                <!-- Fréquence -->
                                <div class="flex items-center ml-4">
                                    <label
                                        for="frequence"
                                        class="text-lg font-semibold text-gray-800 mr-4 w-30"
                                    >
                                        Fréquence :
                                    </label>
                                    <select
                                        v-model="selectedOption"
                                        @change="handleOptionChange"
                                        class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                    >
                                        <option value="">
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

                                <!-- Mesure -->
                                <div class="flex items-center">
                                    <label
                                        for="mesure"
                                        class="text-lg font-semibold text-gray-800 w-20"
                                    >
                                        Livrable :
                                    </label>
                                    <input
                                        type="text"
                                        id="mesure"
                                        v-model="action.mesure"
                                        class="border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                    />
                                </div>
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

                            <!-- Groupe Action & Observation -->
                            <div class="flex flex-wrap gap-x-6 gap-y-6 mt-6">
                                <!-- Action -->
                                <div
                                    class="flex items-start w-full md:w-[40%] ml-4"
                                >
                                    <label
                                        for="description"
                                        class="text-lg font-semibold text-gray-800 w-40"
                                    >
                                        Description de la conformité :
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

                            <!-- Groupe Action & Observation -->
                            <div class="flex flex-wrap gap-x-6 gap-y-6 mt-6">
                                <!-- Action -->
                                <div
                                    class="flex items-start w-full md:w-[40%] ml-4"
                                >
                                    <label
                                        for="description"
                                        class="text-lg font-semibold text-gray-800 w-20"
                                    >
                                        Action :
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="action.action"
                                        class="flex-1 border border-gray-400 rounded-md px-4 py-2 bg-transparent resize-none"
                                        rows="3"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex w-[82.5%] justify-end mt-5">
                                <router-link to="/admin/actions/enquete"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="enregistrerAction"
                                    class="w-[10%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
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
