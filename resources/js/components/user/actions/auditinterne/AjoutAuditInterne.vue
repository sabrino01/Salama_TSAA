<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import { ref, onMounted } from "vue";
import { Plus, Minus, X } from "lucide-vue-next";
import { useRouter } from "vue-router";
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

const frequenceOptions = [
    "",
    "Ponctuel",
    "Annuel",
    "Tout l'année",
    "Quotidien",
    "Hebdomadaire",
    "Mensuel",
    "Bimestriel",
    "Trimestriel",
    "Quadrimestriel",
    "Semestriel",
];

// État pour les données dynamiques du card
const ponctuelData = ref([
    {
        debut: "",
        fin: "",
        suivis: [],
    },
]); // Contient les données des inputs pour "Ponctuel"
const isCardVisible = ref(false); // Contrôle la visibilité du card

// Ajouter un nouvel input "date et heure"
const addDateTimeInput = () => {
    ponctuelData.value.push({
        debut: "",
        fin: "",
        suivis: [],
    });
};

// Supprimer un input "date et heure"
const removeDateTimeInput = (index) => {
    ponctuelData.value.splice(index, 1);
};

// Ajouter un suivi
const addSuivi = (index) => {
    ponctuelData.value[index].suivis.push("");
};

// Supprimer un suivi
const removeSuivi = (index, suiviIndex) => {
    ponctuelData.value[index].suivis.splice(suiviIndex, 1);
};

// Enregistrer les données du card
const enregistrerPonctuel = () => {
    isCardVisible.value = false; // Masquer le card
};

// Annuler les modifications
const annulerPonctuel = () => {
    ponctuelData.value = []; // Réinitialiser les données
    isCardVisible.value = false; // Masquer le card
};

const formatDateTime = (datetime) => {
    if (!datetime) return "";

    const dateObj = new Date(datetime);
    const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    };

    // Formater la date et l'heure
    return dateObj.toLocaleDateString("fr-FR", options).replace(",", " à");
};

const enregistrerAction = async () => {
    try {
        action.value.frequence = JSON.stringify(ponctuelData.value);
        console.log(action.value);
        await axios.post("/api/actions", action.value);
        router.push("/user/actions/auditinterne");
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
                            <option
                                v-for="constat in constats"
                                :key="constat.id"
                                :value="constat.id"
                            >
                                {{ constat.code }} - {{ constat.libelle }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full flex mt-5">
                        <div class="flex w-auto items-center">
                            <label
                                for="frequence"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Fréquence :
                            </label>
                            <select
                                name="frequence"
                                id="frequence"
                                v-model="action.frequence"
                                @change="
                                    isCardVisible =
                                        action.frequence === 'Ponctuel'
                                "
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option
                                    v-for="option in frequenceOptions"
                                    :key="option"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <div class="border-r border-slate-500"></div>
                        <div
                            v-if="!isCardVisible && ponctuelData.length > 0"
                            class="flex-row ml-4"
                        >
                            <div
                                v-for="(data, index) in ponctuelData"
                                :key="index"
                                class="flex items-center space-x-2"
                            >
                                <span class="font-semibold"
                                    >Date et heure du début :</span
                                >
                                <span>{{ formatDateTime(data.debut) }}</span>
                                <span class="font-semibold"
                                    >Date et heure du fin :</span
                                >
                                <span>{{ formatDateTime(data.fin) }}</span>
                                <span class="font-semibold">Suivi :</span>
                                <span
                                    v-for="suivi in data.suivis"
                                    :key="suivi"
                                    >{{ formatDateTime(suivi) }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Card dynamique pour "Ponctuel" -->
                    <div class="w-full pl-8">
                        <div
                            v-if="isCardVisible"
                            class="w-[35%] border rounded-md shadow-md p-4 mt-4"
                        >
                            <div class="text-lg text-center font-semibold mb-4">
                                Ponctuel
                            </div>
                            <div
                                v-for="(data, index) in ponctuelData"
                                :key="index"
                                class="mb-4"
                            >
                                <!-- Date et heure début -->
                                <div class="flex items-center mb-2">
                                    <span class="mr-2">Date et heure :</span>
                                    <input
                                        type="datetime-local"
                                        v-model="data.debut"
                                        class="border rounded-md px-2 py-1"
                                    />
                                    <button
                                        @click="addDateTimeInput"
                                        class="ml-2 text-green-500"
                                    >
                                        <Plus
                                            class="w-6 h-6 border rounded-xl"
                                        />
                                    </button>
                                    <button
                                        v-if="ponctuelData.length > 1"
                                        @click="removeDateTimeInput(index)"
                                        class="ml-2 text-red-500"
                                    >
                                        <Minus
                                            class="w-6 h-6 border rounded-sm"
                                        />
                                    </button>
                                </div>

                                <!-- Date et heure fin -->
                                <div
                                    v-if="data.debut"
                                    class="flex items-center mb-2"
                                >
                                    <span class="mr-2"
                                        >Date et heure fin :</span
                                    >
                                    <input
                                        type="datetime-local"
                                        v-model="data.fin"
                                        class="border rounded-md px-2 py-1"
                                    />
                                    <button
                                        @click="data.fin = ''"
                                        class="ml-2 text-red-500"
                                    >
                                        <X class="w-6 h-6 border rounded-xl" />
                                    </button>
                                </div>

                                <!-- Suivis -->
                                <div
                                    v-if="data.fin"
                                    class="flex items-center mb-2"
                                >
                                    <span class="mr-2">Suivis :</span>
                                    <div class="flex items-center space-x-2">
                                        <div
                                            v-for="(
                                                suivi, suiviIndex
                                            ) in data.suivis"
                                            :key="suiviIndex"
                                            class="flex items-center"
                                        >
                                            <input
                                                type="datetime-local"
                                                v-model="
                                                    data.suivis[suiviIndex]
                                                "
                                                class="border rounded-md px-2 py-1"
                                            />
                                            <button
                                                @click="
                                                    removeSuivi(
                                                        index,
                                                        suiviIndex
                                                    )
                                                "
                                                class="ml-2 text-red-500"
                                            >
                                                <Minus
                                                    class="w-6 h-6 border rounded-sm"
                                                />
                                            </button>
                                        </div>
                                        <button
                                            @click="addSuivi(index)"
                                            class="text-green-500"
                                        >
                                            <Plus
                                                class="w-6 h-6 border rounded-xl"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer du card -->
                            <div class="flex justify-end mt-4">
                                <button
                                    @click="annulerPonctuel"
                                    class="bg-gray-300 px-4 py-2 rounded-md mr-2"
                                >
                                    Annuler
                                </button>
                                <button
                                    @click="enregistrerPonctuel"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </div>
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
                        <router-link to="/user/actions/auditinterne"
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
