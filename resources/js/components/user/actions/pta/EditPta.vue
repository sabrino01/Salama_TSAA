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
                        Editer PTA
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez éditer PTA pour pouvoir
                        faire des modifications sur le PTA sélectionnée.
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
                            value="14/10/2024"
                        />
                    </div>
                    <div class="flex w-[60%] mt-5">
                        <label
                            for="dns"
                            class="w-[30%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Description de la non conformité :
                        </label>
                        <textarea
                            name="dns"
                            id="dns"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        >
Demande le statut</textarea
                        >
                    </div>
                    <div class="w-full flex mt-5">
                        <div class="flex w-auto items-center">
                            <label
                                for="source"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Source :
                            </label>
                            <select
                                name="source"
                                id="source"
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>P25</option>
                                <option value="source1">P25</option>
                                <option value="source2">P24</option>
                                <option value="source3">P23</option>
                            </select>
                        </div>
                        <div class="border-r border-slate-500"></div>
                        <div class="flex w-auto items-center">
                            <label
                                for="typeactions"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Type d'actions :
                            </label>
                            <select
                                name="typeactions"
                                id="typeactions"
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>
                                    Action à planifier
                                </option>
                                <option value="typeactions1">
                                    Action à planifier
                                </option>
                                <option value="typeactions2">
                                    Action à améliorer
                                </option>
                            </select>
                        </div>
                        <div class="border-r border-slate-500"></div>
                        <div class="flex w-auto items-center">
                            <label
                                for="responsable"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Responsable :
                            </label>
                            <select
                                name="responsable"
                                id="responsable"
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>
                                    Directeur Générale
                                </option>
                                <option value="responsable1">
                                    Directeur Générale
                                </option>
                                <option value="responsable2">
                                    Directeur du Service Informatique
                                </option>
                                <option value="responsable3">
                                    Responsable du Service Informatique
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="w-full flex mt-5">
                        <div class="flex w-auto items-center">
                            <label
                                for="suivi"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Suivi :
                            </label>
                            <select
                                name="suivi"
                                id="suivi"
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>
                                    Dominique
                                </option>
                                <option value="suivi1">Dominique</option>
                                <option value="suivi2">Nasandratra</option>
                                <option value="suivi3">Malala</option>
                            </select>
                        </div>
                        <div class="border-r border-slate-500"></div>
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
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>
                                    Mensuel
                                </option>
                                <option value="frequence1">Mensuel</option>
                                <option value="frequence2">Annuel</option>
                            </select>
                        </div>
                        <div class="border-r border-slate-500"></div>
                        <div class="flex w-auto items-center">
                            <label
                                for="action"
                                class="ml-4 text-lg font-semibold text-gray-800"
                            >
                                Action :
                            </label>
                            <select
                                name="action"
                                id="action"
                                class="ml-3 mr-4 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            >
                                <option value="" selected disabled>
                                    Non réalisé
                                </option>
                                <option value="action1">Non réalisé</option>
                                <option value="action2">Réalisé</option>
                                <option value="action3">Abandonné</option>
                            </select>
                        </div>
                    </div>

                    <div class="w-full flex mt-5">
                        <span
                            class="text-lg ml-4 font-semibold text-gray-800 mr-4"
                            >Mois :</span
                        >
                        <div class="flex flex-wrap space-x-6">
                            <div
                                v-for="(month, index) in months"
                                :key="index"
                                class="flex flex-col items-center"
                            >
                                <!-- Checkbox pour chaque mois -->
                                <label class="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        v-model="selectedMonths[index]"
                                        class="form-checkbox h-5 w-5 text-blue-500"
                                    />
                                    <span class="text-gray-800 font-medium">{{
                                        month
                                    }}</span>
                                </label>

                                <!-- Input de type date affiché si le mois est sélectionné -->
                                <input
                                    v-if="selectedMonths[index]"
                                    type="date"
                                    :value="getDefaultDate(index)"
                                    @input="updateDate(index, $event)"
                                    class="mt-2 border border-gray-400 rounded-md px-2 py-1"
                                />
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
                            value="C'est un problème assez grave"
                        />
                    </div>

                    <div class="flex w-auto max-w-[100%] items-center mt-5">
                        <label
                            for="datesuivi"
                            class="ml-4 mr-3 text-lg font-semibold text-gray-800"
                        >
                            Date suivi :
                        </label>
                        <div class="flex items-center space-x-4">
                            <!-- Liste des inputs de type date -->
                            <div
                                v-for="(date, index) in dateInputs"
                                :key="index"
                                class="flex w-auto items-center space-x-2"
                            >
                                <input
                                    type="date"
                                    v-model="dateInputs[index]"
                                    class="w-auto border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                />
                                <!-- Bouton pour supprimer un input -->
                                <button
                                    v-if="index > 0"
                                    @click="removeDateInput(index)"
                                    class="text-red-500"
                                >
                                    <Minus class="w-7 h-7 border" />
                                </button>
                            </div>

                            <!-- Bouton pour ajouter un nouvel input -->
                            <button
                                @click="addDateInput"
                                class="text-green-500"
                            >
                                <Plus class="w-8 h-8 border rounded-2xl" />
                            </button>
                        </div>
                    </div>

                    <div class="flex w-[80%] justify-end mt-5">
                        <router-link to="/user/actions/pta"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
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

<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import { ref } from "vue";
import { Plus, Minus } from "lucide-vue-next";

// Liste des mois abrégés
const months = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
];

// État des cases à cocher pour chaque mois
const selectedMonths = ref(Array(12).fill(false)); // Initialisé à false pour tous les mois

// Liste des dates sélectionnées
const selectedDates = ref(Array(12).fill("")); // Initialisé avec des dates vides

// Fonction pour obtenir la date par défaut avec le mois verrouillé
const getDefaultDate = (index) => {
    const year = new Date().getFullYear(); // Année actuelle
    const month = String(index + 1).padStart(2, "0"); // Mois (1-indexé) formaté sur 2 chiffres
    const day = "01"; // Par défaut, le jour est le 1er
    return `${year}-${month}-${day}`; // Format YYYY-MM-DD
};

// Fonction pour mettre à jour la date tout en verrouillant le mois
const updateDate = (index, event) => {
    const year = event.target.value.split("-")[0]; // Récupère l'année
    const day = event.target.value.split("-")[2]; // Récupère le jour
    const month = String(index + 1).padStart(2, "0"); // Mois verrouillé
    selectedDates.value[index] = `${year}-${month}-${day}`; // Met à jour la date
};

// Liste des inputs de type date
const dateInputs = ref([""]); // Initialise avec un seul champ de type date

// Fonction pour ajouter un nouvel input
const addDateInput = () => {
    dateInputs.value.push(""); // Ajoute un nouvel input vide
};

// Fonction pour supprimer un input
const removeDateInput = (index) => {
    dateInputs.value.splice(index, 1); // Supprime l'input à l'index donné
};
</script>
