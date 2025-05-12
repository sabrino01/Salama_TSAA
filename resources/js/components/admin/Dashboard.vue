<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-5 bg-gray-100 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl text-center font-bold text-gray-800"
                    >
                        Bienvenue au Tableau de bord
                        <h1>Salama_tsaa</h1>
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-12 font-poppins">
                        Salama_tsaa est une application qui permet de bien
                        suivre les actions ou faire des améliorations et encore
                        plus. Vous pourriez aussi voir et consulter les
                        statistiques ainsi que d'autres informations.
                    </p>
                </div>

                <!-- Titre statistique -->
                <div
                    class="w-full mt-5 text-center underline underline-offset-4"
                >
                    <h2 class="text-4xl font-medium text-gray-800">
                        Statistiques des actions
                    </h2>
                </div>

                <!-- Audit Interne | PTA -->
                <div class="flex w-full mt-8">
                    <!-- Audit Interne -->
                    <div class="w-1/2 pr-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Statistique et Tableau de Constat pour Audit Interne
                        </h3>

                        <!-- Recherche -->
                        <div class="flex w-full ml-2 mt-4">
                            <div class="w-1/2 text-end">
                                <p class="text-gray-600 mt-2">Recherche :</p>
                            </div>
                            <div class="w-1/2 ml-6">
                                <select
                                    class="rounded-lg w-32 border border-gray-200 p-1.5"
                                    name="rechercheTypeAI"
                                    id="rechercheTypeAI"
                                    v-model="rechercheAI.type"
                                    @change="resetSearchAI"
                                >
                                    <option value="1">Jours</option>
                                    <option value="2">Mois</option>
                                    <option value="3">Ans</option>
                                </select>
                            </div>
                        </div>

                        <!-- Résultat recherche -->
                        <div
                            class="flex w-full ml-2 mt-2"
                            v-if="rechercheAI.type"
                        >
                            <div class="w-2/3 text-center">
                                <!-- Recherche par jour -->
                                <input
                                    v-if="rechercheAI.type === '1'"
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5"
                                    v-model="rechercheAI.dateDebut"
                                />

                                <!-- Recherche par mois -->
                                <div
                                    v-if="rechercheAI.type === '2'"
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5 mr-2"
                                        v-model="rechercheAI.mois"
                                    >
                                        <option
                                            v-for="i in 12"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i.toString().padStart(2, "0") }}
                                        </option>
                                    </select>
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="rechercheAI.annee"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Recherche par an -->
                                <div
                                    v-if="rechercheAI.type === '3'"
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="rechercheAI.annee"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Affichage de la deuxième date quand toggle est activé -->
                                <input
                                    v-if="
                                        rechercheAI.type === '1' && isToggledAI
                                    "
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5 mt-2"
                                    v-model="rechercheAI.dateFin"
                                />

                                <div
                                    v-if="
                                        rechercheAI.type === '2' && isToggledAI
                                    "
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5 mr-2"
                                        v-model="rechercheAI.moisFin"
                                    >
                                        <option
                                            v-for="i in 12"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i.toString().padStart(2, "0") }}
                                        </option>
                                    </select>
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="rechercheAI.anneeFin"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <div
                                    v-if="
                                        rechercheAI.type === '3' && isToggledAI
                                    "
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="rechercheAI.anneeFin"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="w-1/3 justify-items-end">
                                <label
                                    class="relative inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        v-model="isToggledAI"
                                    />
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                    ></div>
                                    <span
                                        class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                    ></span>
                                </label>
                                <p class="text-gray-600">
                                    {{
                                        isToggledAI ? "Entre deux" : "Une seule"
                                    }}
                                </p>
                            </div>

                            <div
                                class="flex w-2/3 justify-center ml-2"
                                v-if="rechercheAI.type"
                            >
                                <div class="ml-1">
                                    <select
                                        class="rounded-lg w-48 border border-gray-200 p-1.5"
                                        v-model="rechercheAI.userId"
                                    >
                                        <option value="">
                                            Tous les utilisateurs
                                        </option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.nom_utilisateur }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton rechercher et export -->
                        <div class="flex w-full">
                            <div class="flex w-[50%] justify-end mt-4">
                                <button
                                    @click="rechercherAI"
                                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                    :disabled="isLoadingAI"
                                >
                                    <span v-if="isLoadingAI"
                                        >Chargement...</span
                                    >
                                    <span v-else>Rechercher</span>
                                </button>
                            </div>

                            <div class="flex w-[50%] justify-end mt-4">
                                <ExportPDF
                                    type="AI"
                                    container-selector="#statistiques-ai-container"
                                />
                            </div>
                        </div>

                        <!-- Message d'erreur -->
                        <div
                            v-if="errorMessageAI"
                            class="mt-4 text-center text-red-500"
                        >
                            {{ errorMessageAI }}
                        </div>

                        <!-- Statistique -->
                        <div
                            id="statistiques-ai-container"
                            class="mt-5"
                            v-if="resultatsRechercheAI.length > 0"
                        >
                            <BarChart
                                :chart-data="chartDataAI"
                                chart-title="Statistiques Audit Interne"
                                class="h-96"
                            />
                        </div>
                        <div
                            class="mt-5 text-center text-gray-500"
                            v-else-if="
                                rechercheAI.type && !resultatsRechercheAI.length
                            "
                        >
                            Aucun résultat trouvé pour votre recherche. Veuillez
                            modifier vos recherches.
                        </div>

                        <!-- Tableau de constat -->
                        <table
                            class="table-fixed bg-white border rounded-lg w-full mt-5"
                            v-if="resultatsRechercheAI.length > 0"
                        >
                            <thead class="bg-gray-200 text-lg h-10">
                                <tr>
                                    <th>Type de constat</th>
                                    <th>Nombres de constat</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr
                                    v-for="(
                                        resultat, index
                                    ) in resultatsRechercheAI"
                                    :key="index"
                                >
                                    <td>{{ resultat.code }}</td>
                                    <td>{{ resultat.nombre }}</td>
                                    <td>
                                        {{ resultat.pourcentage.toFixed(1) }}%
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot
                                class="bg-gray-200 h-10 text-center font-bold"
                            >
                                <tr>
                                    <td class="text-lg">Total</td>
                                    <td>{{ totalConstatsAI }}</td>
                                    <td>
                                        {{ totalPourcentageAI.toFixed(1) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Ligne verticale -->
                    <div class="border-l border-gray-300"></div>

                    <!-- PTA -->
                    <div class="w-1/2 pl-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Statistique et Tableau de Constat pour PTA
                        </h3>

                        <!-- Recherche -->
                        <div class="flex w-full ml-2 mt-4">
                            <div class="w-1/2 text-end">
                                <p class="text-gray-600 mt-2">Recherche :</p>
                            </div>
                            <div class="w-1/2 ml-6">
                                <select
                                    class="rounded-lg w-32 border border-gray-200 p-1.5"
                                    name="rechercheTypePTA"
                                    id="rechercheTypePTA"
                                    v-model="recherchePTA.type"
                                    @change="resetSearchPTA"
                                >
                                    <option value="1">Jours</option>
                                    <option value="2">Mois</option>
                                    <option value="3">Ans</option>
                                </select>
                            </div>
                        </div>

                        <!-- Résultat recherche -->
                        <div
                            class="flex w-full ml-2 mt-2"
                            v-if="recherchePTA.type"
                        >
                            <div class="w-2/3 text-center">
                                <!-- Recherche par jour -->
                                <input
                                    v-if="recherchePTA.type === '1'"
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5"
                                    v-model="recherchePTA.dateDebut"
                                />

                                <!-- Recherche par mois -->
                                <div
                                    v-if="recherchePTA.type === '2'"
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5 mr-2"
                                        v-model="recherchePTA.mois"
                                    >
                                        <option
                                            v-for="i in 12"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i.toString().padStart(2, "0") }}
                                        </option>
                                    </select>
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="recherchePTA.annee"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Recherche par an -->
                                <div
                                    v-if="recherchePTA.type === '3'"
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="recherchePTA.annee"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Affichage de la deuxième date quand toggle est activé -->
                                <input
                                    v-if="
                                        recherchePTA.type === '1' &&
                                        isToggledPTA
                                    "
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5 mt-2"
                                    v-model="recherchePTA.dateFin"
                                />

                                <div
                                    v-if="
                                        recherchePTA.type === '2' &&
                                        isToggledPTA
                                    "
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5 mr-2"
                                        v-model="recherchePTA.moisFin"
                                    >
                                        <option
                                            v-for="i in 12"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i.toString().padStart(2, "0") }}
                                        </option>
                                    </select>
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="recherchePTA.anneeFin"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>

                                <div
                                    v-if="
                                        recherchePTA.type === '3' &&
                                        isToggledPTA
                                    "
                                    class="inline-flex"
                                >
                                    <select
                                        class="rounded-lg w-24 border border-gray-200 p-1.5"
                                        v-model="recherchePTA.anneeFin"
                                    >
                                        <option
                                            v-for="i in anneesList"
                                            :key="i"
                                            :value="i"
                                        >
                                            {{ i }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="w-1/3 justify-items-end">
                                <label
                                    class="relative inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        v-model="isToggledPTA"
                                    />
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                    ></div>
                                    <span
                                        class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                    ></span>
                                </label>
                                <p class="text-gray-600">
                                    {{
                                        isToggledPTA
                                            ? "Entre deux"
                                            : "Une seule"
                                    }}
                                </p>
                            </div>

                            <div
                                class="flex w-2/3 justify-center ml-2"
                                v-if="recherchePTA.type"
                            >
                                <div class="ml-1">
                                    <select
                                        class="rounded-lg w-48 border border-gray-200 p-1.5"
                                        v-model="recherchePTA.userId"
                                    >
                                        <option value="">
                                            Tous les utilisateurs
                                        </option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.nom_utilisateur }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton rechercher et export -->
                        <div class="flex w-full">
                            <div class="flex w-[50%] justify-end mt-4">
                                <button
                                    @click="rechercherPTA"
                                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                    :disabled="isLoadingPTA"
                                >
                                    <span v-if="isLoadingPTA"
                                        >Chargement...</span
                                    >
                                    <span v-else>Rechercher</span>
                                </button>
                            </div>

                            <div class="flex w-[50%] justify-end mt-4">
                                <ExportPDF
                                    type="PTA"
                                    container-selector="#statistiques-pta-container"
                                />
                            </div>
                        </div>

                        <!-- Message d'erreur -->
                        <div
                            v-if="errorMessagePTA"
                            class="mt-4 text-center text-red-500"
                        >
                            {{ errorMessagePTA }}
                        </div>

                        <!-- Statistique -->
                        <div
                            id="statistiques-pta-container"
                            class="mt-5"
                            v-if="resultatsRecherchePTA.length > 0"
                        >
                            <BarChart
                                :chart-data="chartDataPTA"
                                chart-title="Statistiques PTA"
                                class="h-96"
                            />
                        </div>
                        <div
                            class="mt-5 text-center text-gray-500"
                            v-else-if="
                                recherchePTA.type &&
                                !resultatsRecherchePTA.length
                            "
                        >
                            Aucun résultat trouvé pour votre recherche. Veuillez
                            modifier vos recherches.
                        </div>

                        <!-- Tableau de constat -->
                        <table
                            class="table-fixed bg-white border rounded-lg w-full mt-5"
                            v-if="resultatsRecherchePTA.length > 0"
                        >
                            <thead class="bg-gray-200 text-lg h-10">
                                <tr>
                                    <th>Type de constat</th>
                                    <th>Nombres de constat</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr
                                    v-for="(
                                        resultat, index
                                    ) in resultatsRecherchePTA"
                                    :key="index"
                                >
                                    <td>{{ resultat.code }}</td>
                                    <td>{{ resultat.nombre }}</td>
                                    <td>
                                        {{ resultat.pourcentage.toFixed(1) }}%
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot
                                class="bg-gray-200 h-10 text-center font-bold"
                            >
                                <tr>
                                    <td class="text-lg">Total</td>
                                    <td>{{ totalConstatsPTA }}</td>
                                    <td>
                                        {{ totalPourcentagePTA.toFixed(1) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Titre actions -->
                <div
                    class="w-full mt-10 text-center underline underline-offset-4"
                >
                    <h2 class="text-4xl font-medium text-gray-800">Actions</h2>
                </div>

                <!-- Audit Interne | PTA -->
                <div class="flex w-full mt-6">
                    <!-- Audit Interne -->
                    <div class="w-1/2 pr-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Audit Interne
                        </h3>
                        <div
                            class="flex w-full mt-5 justify-between space-x-5 text-white"
                        >
                            <div
                                class="flex w-1/3 items-center bg-green-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">En cours</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    19
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-gray-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Clôturé</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    42
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-purple-600 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Abandonné</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    10
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne verticale -->
                    <div class="border-l border-gray-300"></div>

                    <!-- PTA -->
                    <div class="w-1/2 pl-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            PTA
                        </h3>
                        <div
                            class="flex w-full mt-5 justify-between space-x-5 text-white"
                        >
                            <div
                                class="flex w-1/3 items-center bg-green-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">En cours</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    23
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-gray-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Clôturé</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    37
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-purple-600 p-6 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Abandonné</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    8
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "../assets/Sidebar.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
import BarChart from "../charts/BarChart.vue";
import ExportPDF from "../charts/ExportBarChartPDF.vue";
import { ref, computed, onMounted } from "vue";
import { Info } from "lucide-vue-next";
import axios from "axios";

// Générer la liste des années (de l'année en cours à 5 ans en arrière)
const anneeActuelle = new Date().getFullYear();
const anneesList = Array.from({ length: 6 }, (_, i) => anneeActuelle - i);

// Utilisateurs pour le filtre
const users = ref([]);

// Options de recherche pour AI
const rechercheAI = ref({
    type: "1", // 1: Jours, 2: Mois, 3: Ans
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

// État toggle pour la sélection d'une plage (AI)
const isToggledAI = ref(false);

// Résultats de la recherche (AI)
const resultatsRechercheAI = ref([]);
// État de chargement (AI)
const isLoadingAI = ref(false);
// État d'erreur (AI)
const errorMessageAI = ref("");

// Calculer le total des constats (AI)
const totalConstatsAI = computed(() => {
    return resultatsRechercheAI.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

// Calculer le total en pourcentage (AI)
const totalPourcentageAI = computed(() => {
    return resultatsRechercheAI.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

// Données pour le graphique AI
const chartDataAI = computed(() => {
    if (resultatsRechercheAI.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats AI",
                    data: [],
                    backgroundColor: [
                        "#0062ff",
                        "#ff6384",
                        "#36a2eb",
                        "#ffcd56",
                        "#4bc0c0",
                        "#9966ff",
                        "#ff9f40",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheAI.value.map((r) => r.code),
        datasets: [
            {
                label: "Constats AI",
                data: resultatsRechercheAI.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#0062ff",
                    "#ff6384",
                    "#36a2eb",
                    "#ffcd56",
                    "#4bc0c0",
                    "#9966ff",
                    "#ff9f40",
                ],
            },
        ],
    };
});

// Options de recherche pour PTA
const recherchePTA = ref({
    type: "1", // 1: Jours, 2: Mois, 3: Ans
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

// État toggle pour la sélection d'une plage (PTA)
const isToggledPTA = ref(false);

// Résultats de la recherche (PTA)
const resultatsRecherchePTA = ref([]);
// État de chargement (PTA)
const isLoadingPTA = ref(false);
// État d'erreur (PTA)
const errorMessagePTA = ref("");

// Calculer le total des constats (PTA)
const totalConstatsPTA = computed(() => {
    return resultatsRecherchePTA.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

// Calculer le total en pourcentage (PTA)
const totalPourcentagePTA = computed(() => {
    return resultatsRecherchePTA.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

// Données pour le graphique PTA
const chartDataPTA = computed(() => {
    if (resultatsRecherchePTA.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats PTA",
                    data: [],
                    backgroundColor: [
                        "#34d399",
                        "#a78bfa",
                        "#fbbf24",
                        "#ec4899",
                        "#10b981",
                        "#8b5cf6",
                        "#f59e0b",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRecherchePTA.value.map((r) => r.code),
        datasets: [
            {
                label: "Constats PTA",
                data: resultatsRecherchePTA.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#34d399",
                    "#a78bfa",
                    "#fbbf24",
                    "#ec4899",
                    "#10b981",
                    "#8b5cf6",
                    "#f59e0b",
                ],
            },
        ],
    };
});

// Charger les utilisateurs au démarrage
onMounted(async () => {
    try {
        const response = await axios.get("/api/users");
        users.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs:", error);
    }
});

// Fonctions pour réinitialiser les critères de recherche lors du changement de type
const resetSearchAI = () => {
    resultatsRechercheAI.value = [];
    isToggledAI.value = false;
};

const resetSearchPTA = () => {
    resultatsRecherchePTA.value = [];
    isToggledPTA.value = false;
};

// Fonctions pour formater les paramètres selon le type de recherche (AI)
const getSearchParamsAI = () => {
    const params = { type: rechercheAI.value.type };

    if (rechercheAI.value.userId) {
        params.user_id = rechercheAI.value.userId;
    }

    if (rechercheAI.value.type === "1") {
        // Jours
        params.date_debut = rechercheAI.value.dateDebut;
        if (isToggledAI.value) {
            params.date_fin = rechercheAI.value.dateFin;
        }
    } else if (rechercheAI.value.type === "2") {
        // Mois
        params.mois = rechercheAI.value.mois;
        params.annee = rechercheAI.value.annee;
        if (isToggledAI.value) {
            params.mois_fin = rechercheAI.value.moisFin;
            params.annee_fin = rechercheAI.value.anneeFin;
        }
    } else if (rechercheAI.value.type === "3") {
        // Ans
        params.annee = rechercheAI.value.annee;
        if (isToggledAI.value) {
            params.annee_fin = rechercheAI.value.anneeFin;
        }
    }

    return params;
};

// Fonctions pour formater les paramètres selon le type de recherche (PTA)
const getSearchParamsPTA = () => {
    const params = { type: recherchePTA.value.type };

    if (recherchePTA.value.userId) {
        params.user_id = recherchePTA.value.userId;
    }

    if (recherchePTA.value.type === "1") {
        // Jours
        params.date_debut = recherchePTA.value.dateDebut;
        if (isToggledPTA.value) {
            params.date_fin = recherchePTA.value.dateFin;
        }
    } else if (recherchePTA.value.type === "2") {
        // Mois
        params.mois = recherchePTA.value.mois;
        params.annee = recherchePTA.value.annee;
        if (isToggledPTA.value) {
            params.mois_fin = recherchePTA.value.moisFin;
            params.annee_fin = recherchePTA.value.anneeFin;
        }
    } else if (recherchePTA.value.type === "3") {
        // Ans
        params.annee = recherchePTA.value.annee;
        if (isToggledPTA.value) {
            params.annee_fin = recherchePTA.value.anneeFin;
        }
    }

    return params;
};

// Fonction pour effectuer la recherche (AI)
const rechercherAI = async () => {
    try {
        isLoadingAI.value = true;
        errorMessageAI.value = "";

        const params = getSearchParamsAI();
        const response = await axios.get("/api/constats/statistiques/AI", {
            params,
        });

        // Vérifier si la réponse est un tableau
        if (Array.isArray(response.data)) {
            resultatsRechercheAI.value = response.data;
            if (response.data.length === 0) {
                errorMessageAI.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheAI.value = [];
            errorMessageAI.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche AI:", error);
        resultatsRechercheAI.value = [];
        errorMessageAI.value =
            "Une erreur est survenue lors de la recherche. Veuillez réessayer.";
    } finally {
        isLoadingAI.value = false;
    }
};

// Fonction pour effectuer la recherche (PTA)
const rechercherPTA = async () => {
    try {
        isLoadingPTA.value = true;
        errorMessagePTA.value = "";

        const params = getSearchParamsPTA();
        const response = await axios.get("/api/constats/statistiques/PTA", {
            params,
        });

        // Vérifier si la réponse est un tableau
        if (Array.isArray(response.data)) {
            resultatsRecherchePTA.value = response.data;
            if (response.data.length === 0) {
                errorMessagePTA.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRecherchePTA.value = [];
            errorMessagePTA.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche PTA:", error);
        resultatsRecherchePTA.value = [];
        errorMessagePTA.value =
            "Une erreur est survenue lors de la recherche. Veuillez réessayer.";
    } finally {
        isLoadingPTA.value = false;
    }
};
</script>
