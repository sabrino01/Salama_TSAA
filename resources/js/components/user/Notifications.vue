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
                            Notifications
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans l'espace Notifications, vous pouvez voir et
                                gérer les notifications, que ce soit une alerte
                                par email ou une alerte dans l'application et de
                                faire plus.
                            </p>
                        </div>

                        <div class="flex justify-end mt-8">
                            <div
                                class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4"
                            >
                                <h3 class="text-lg font-bold text-blue-800">
                                    Paramètres de Notifications
                                </h3>

                                <!-- Toggle Email -->
                                <div
                                    class="flex items-center rounded-lg space-x-2"
                                >
                                    <label
                                        for="emailNotification"
                                        class="text-gray-700 font-medium"
                                    >
                                        Notification par Email
                                    </label>
                                    <input
                                        id="emailNotification"
                                        type="checkbox"
                                        v-model="emailNotification"
                                        @change="handleEmailToggle"
                                        class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <!-- Toggle App Alert -->
                                <div
                                    class="flex items-center space-x-2 rounded-lg"
                                >
                                    <label
                                        for="appAlert"
                                        class="text-gray-700 font-medium"
                                    >
                                        Alerte dans l'application
                                    </label>
                                    <input
                                        id="appAlert"
                                        type="checkbox"
                                        v-model="appAlert"
                                        @change="onAlertToggle"
                                        class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="w-full text-gray-900 mt-5">
                            <div class="indent-4 text-2xl font-bold underline">
                                En cours
                            </div>
                        </div>

                        <!-- Tableau notifications par récente-->
                        <div class="mt-5 ml-4 flex w-full">
                            <!-- Colonne gauche -->
                            <div class="w-1/6 pr-4">
                                <label
                                    for="filter"
                                    class="block text-gray-700 font-medium mb-2"
                                >
                                    Filtrer par
                                </label>
                                <select
                                    id="filter-en-cours"
                                    v-model="selectedFilterEnCours"
                                    @change="filterChanged"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="all">
                                        Audit Interne / PTA...
                                    </option>
                                    <option value="AI">Audit Interne</option>
                                    <option value="PTA">PTA</option>
                                    <option value="CAC">CAC</option>
                                    <option value="SWOT">SWOT</option>
                                    <option value="AE">Audit Externe</option>
                                    <option value="ES">
                                        Enquête de Satisfaction
                                    </option>
                                </select>
                            </div>

                            <!-- Colonne droite -->
                            <div class="w-4/5">
                                <!-- Ligne avec Résultats à gauche et Pagination à droite -->
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <div class="text-lg font-bold">
                                        Résultats : {{ totalEnCours }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <label
                                            for="paginationToggle"
                                            class="text-gray-700 font-medium"
                                        >
                                            Pagination
                                        </label>
                                        <input
                                            id="paginationToggle"
                                            type="checkbox"
                                            v-model="paginationEnabled"
                                            @change="onPaginationToggle"
                                            class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                <table class="table-fixed w-full">
                                    <thead class="bg-gray-200 text-lg">
                                        <tr>
                                            <th class="text-start px-4 py-2">
                                                Source
                                            </th>
                                            <th class="text-start px-4 py-2">
                                                Description
                                            </th>
                                            <th class="text-start px-4 py-2">
                                                Fréquence
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                item, index
                                            ) in displayedDataEnCours"
                                            :key="index"
                                            @click="navigateToDetails(item.id)"
                                            :class="[
                                                'cursor-pointer border-b-4 bg-white text-black hover:bg-gray-100 shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2',
                                                getFrequenceBorderClass(
                                                    item.frequence
                                                ) || 'border-green-500',
                                            ]"
                                        >
                                            <td class="px-4 py-2">
                                                {{ item.source_libelle }}
                                            </td>
                                            <td class="truncate px-4 py-2">
                                                {{ item.description }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <FrequenceInformations
                                                    :frequence="item.frequence"
                                                    ref="frequenceInfo"
                                                />
                                            </td>
                                        </tr>

                                        <tr
                                            v-if="
                                                displayedDataEnCours.length ===
                                                0
                                            "
                                        >
                                            <td
                                                colspan="3"
                                                class="px-4 py-2 text-center"
                                            >
                                                Aucune Action en cours
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div
                                    v-if="paginationEnabled"
                                    class="flex justify-between items-center mt-4"
                                >
                                    <button
                                        @click="prevPageEnCours"
                                        :disabled="currentPageEnCours === 1"
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                currentPageEnCours === 1,
                                        }"
                                    >
                                        <ChevronLeft class="w-5 h-5" />
                                    </button>
                                    <div>
                                        Page {{ currentPageEnCours }} sur
                                        {{ totalPagesEnCours }}
                                    </div>
                                    <button
                                        @click="nextPageEnCours"
                                        :disabled="
                                            currentPageEnCours ===
                                            totalPagesEnCours
                                        "
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                currentPageEnCours ===
                                                totalPagesEnCours,
                                        }"
                                    >
                                        <ChevronRight class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border bg-gray-400 mt-6"></div>

                        <div class="w-full text-gray-900 mt-5">
                            <div class="indent-4 text-2xl font-bold underline">
                                En retard
                            </div>
                        </div>

                        <!-- Tableau notifications par récente-->
                        <div class="mt-5 ml-4 flex w-full">
                            <!-- Colonne gauche -->
                            <div class="w-1/6 pr-4">
                                <label
                                    for="filter-retard"
                                    class="block text-gray-700 font-medium mb-2"
                                >
                                    Filtrer par
                                </label>
                                <select
                                    id="filter-en-retard"
                                    v-model="selectedFilterEnRetard"
                                    @change="filterChanged"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="all">
                                        Audit Interne / PTA...
                                    </option>
                                    <option value="AI">Audit Interne</option>
                                    <option value="PTA">PTA</option>
                                    <option value="CAC">CAC</option>
                                    <option value="SWOT">SWOT</option>
                                    <option value="AE">Audit Externe</option>
                                    <option value="ES">
                                        Enquête de Satisfaction
                                    </option>
                                </select>
                            </div>

                            <!-- Colonne droite -->
                            <div class="w-4/5">
                                <!-- Ligne avec Résultats à gauche et Pagination à droite -->
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <div class="text-lg font-bold">
                                        Résultats : {{ totalEnRetard }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <label
                                            for="paginationToggle"
                                            class="text-gray-700 font-medium"
                                        >
                                            Pagination
                                        </label>
                                        <input
                                            id="paginationToggle"
                                            type="checkbox"
                                            v-model="paginationEnabled"
                                            @change="onPaginationToggle"
                                            class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                <table class="table-fixed w-full">
                                    <thead class="bg-gray-200 text-lg">
                                        <tr>
                                            <th class="text-start px-4 py-2">
                                                Source
                                            </th>
                                            <th class="text-start px-4 py-2">
                                                Description
                                            </th>
                                            <th class="text-start px-4 py-2">
                                                Fréquence
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                item, index
                                            ) in displayedDataEnRetard"
                                            :key="index"
                                            @click="navigateToDetails(item.id)"
                                            :class="[
                                                'cursor-pointer border-b-4 bg-white text-black hover:bg-gray-100 shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2',
                                                getFrequenceBorderClass(
                                                    item.frequence
                                                ) || 'border-red-500',
                                            ]"
                                        >
                                            <td class="px-4 py-2">
                                                {{ item.source_libelle }}
                                            </td>
                                            <td class="truncate px-4 py-2">
                                                {{ item.description }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <FrequenceInformations
                                                    :frequence="item.frequence"
                                                    ref="frequenceInfoRetard"
                                                />
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="
                                                displayedDataEnRetard.length ===
                                                0
                                            "
                                        >
                                            <td
                                                colspan="3"
                                                class="px-4 py-2 text-center"
                                            >
                                                Aucune Actions en retard
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div
                                    v-if="paginationEnabled"
                                    class="flex justify-between items-center mt-4"
                                >
                                    <button
                                        @click="prevPageEnRetard"
                                        :disabled="currentPageEnRetard === 1"
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                currentPageEnRetard === 1,
                                        }"
                                    >
                                        <ChevronLeft class="w-5 h-5" />
                                    </button>
                                    <div>
                                        Page {{ currentPageEnRetard }} sur
                                        {{ totalPagesEnRetard }}
                                    </div>
                                    <button
                                        @click="nextPageEnRetard"
                                        :disabled="
                                            currentPageEnRetard ===
                                            totalPagesEnRetard
                                        "
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                        :class="{
                                            'opacity-50 cursor-not-allowed':
                                                currentPageEnRetard ===
                                                totalPagesEnRetard,
                                        }"
                                    >
                                        <ChevronRight class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal pour l'alerte début -->
                <div
                    v-if="showDebutAlertModal"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
                    >
                        <div
                            class="fixed inset-0 transition-opacity"
                            aria-hidden="true"
                        >
                            <div
                                class="absolute inset-0 bg-gray-500 opacity-75"
                            ></div>
                        </div>

                        <span
                            class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                            >&#8203;</span
                        >

                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                        >
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-yellow-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"
                                    >
                                        <h3
                                            class="text-lg leading-6 font-medium text-gray-900"
                                        >
                                            Alerte d'action
                                        </h3>
                                        <div class="mt-2">
                                            <p
                                                v-if="
                                                    currentAlert?.type ===
                                                        'recurrent' ||
                                                    currentAlert?.type ===
                                                        'progressive'
                                                "
                                                class="text-sm text-gray-500 whitespace-normal break-words"
                                            >
                                                {{ currentAlert.message }}
                                                <br />
                                                <small class="text-gray-400">{{
                                                    currentAlert.details
                                                }}</small>
                                            </p>
                                            <p
                                                v-else
                                                class="text-sm text-gray-500 whitespace-normal break-words"
                                            >
                                                L'action sur "{{
                                                    currentItem?.description
                                                }}"
                                                <span
                                                    v-if="
                                                        joursRestantsDebut > 0
                                                    "
                                                    >aura lieu dans
                                                    {{ joursRestantsDebut }}
                                                    jours</span
                                                >
                                                <span
                                                    v-else-if="
                                                        joursRestantsDebut === 0
                                                    "
                                                    >aura lieu aujourd'hui</span
                                                >
                                                <span v-else
                                                    >est en retard de
                                                    {{
                                                        Math.abs(
                                                            joursRestantsDebut
                                                        )
                                                    }}
                                                    jours</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-x-2"
                            >
                                <button
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    @click="voirAction"
                                >
                                    Voir
                                </button>
                                <button
                                    type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="fermerModalDebut"
                                >
                                    OK
                                </button>
                                <button
                                    type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-red-100 text-base font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="fermerToutesAlertes"
                                >
                                    Tout fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal pour l'alerte suivis -->
                <div
                    v-if="showSuivisAlertModal"
                    class="fixed inset-0 z-50 overflow-y-auto"
                >
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
                    >
                        <div
                            class="fixed inset-0 transition-opacity"
                            aria-hidden="true"
                        >
                            <div
                                class="absolute inset-0 bg-gray-500 opacity-75"
                            ></div>
                        </div>

                        <span
                            class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true"
                            >&#8203;</span
                        >

                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                        >
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-orange-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"
                                    >
                                        <h3
                                            class="text-lg leading-6 font-medium text-gray-900"
                                        >
                                            Alerte de suivi
                                        </h3>
                                        <div class="mt-2">
                                            <p
                                                class="text-sm text-gray-500 whitespace-normal break-words"
                                            >
                                                Un suivi de l'action "{{
                                                    currentItem?.description
                                                }}"
                                                <span
                                                    v-if="
                                                        joursRestantsSuivis > 0
                                                    "
                                                    >aura lieu dans
                                                    {{ joursRestantsSuivis }}
                                                    jours</span
                                                >
                                                <span
                                                    v-else-if="
                                                        joursRestantsSuivis ===
                                                        0
                                                    "
                                                    >aura lieu aujourd'hui</span
                                                >
                                                <span v-else
                                                    >est en retard de
                                                    {{
                                                        Math.abs(
                                                            joursRestantsSuivis
                                                        )
                                                    }}
                                                    jours</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-x-2"
                            >
                                <button
                                    type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    @click="voirAction"
                                >
                                    Voir
                                </button>
                                <button
                                    type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="fermerModalSuivis"
                                >
                                    OK
                                </button>
                                <button
                                    type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-red-100 text-base font-medium text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="fermerToutesAlertes"
                                >
                                    Tout fermer
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

<script setup>
import Sidebar from "../assets/SidebarUser.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
import FrequenceInformations from "../assets/FrequenceNotifications.vue";
import {
    ChevronLeft,
    ChevronRight,
    Info,
    Settings,
    Eye,
    EyeOff,
} from "lucide-vue-next";
import { ref, onMounted, computed, watch, reactive } from "vue";
import { useRouter } from "vue-router";
import emailService from "../../utils/emailService.js";

// Existing refs and computed properties (unchanged)
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();
const emailNotification = ref(
    localStorage.getItem("emailNotification") === "true"
);
const appAlert = ref(localStorage.getItem("appAlert") === "true");
const paginationEnabled = ref(true);
const displayedDataEnCours = ref([]);
const displayedDataEnRetard = ref([]);
const allDataEnCours = ref([]);
const allDataEnRetard = ref([]);
const currentPageEnCours = ref(1);
const currentPageEnRetard = ref(1);
const totalPagesEnCours = ref(1);
const totalPagesEnRetard = ref(1);
const totalEnCours = ref(0);
const totalEnRetard = ref(0);
const selectedFilterEnCours = ref("all");
const selectedFilterEnRetard = ref("all");
const showDebutAlertModal = ref(false);
const showSuivisAlertModal = ref(false);
const joursRestantsDebut = ref(0);
const joursRestantsSuivis = ref(0);
const currentItem = ref(null);
const alertQueue = ref([]);
const currentAlert = ref(null);
const isInitialLoad = ref(true);

const joursMap = {
    Lundi: 1,
    Mardi: 2,
    Mercredi: 3,
    Jeudi: 4,
    Vendredi: 5,
    Samedi: 6,
    Dimanche: 0,
};

const user = computed(() => {
    try {
        return JSON.parse(localStorage.getItem("user")) || {};
    } catch (e) {
        toast.error(
            "Erreur lors de la récupération des données utilisateur:",
            e
        );
        return {};
    }
});
const userId = computed(() => user.value?.id);

// ==============================================
// METHODES INTEGREES DE FREQUENCEINFORMATIONS
// ==============================================

// Convertit une date ISO en objet Date
const parseDateTime = (dateTimeStr) => {
    if (!dateTimeStr) return null;
    return new Date(dateTimeStr);
};

// Calcule la différence en jours entre une date et aujourd'hui
const calculerJoursRestants = (dateTimeStr) => {
    if (!dateTimeStr) return 0;

    const inputDate = parseDateTime(dateTimeStr);
    if (!inputDate) return 0;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const inputDateOnly = new Date(inputDate);
    inputDateOnly.setHours(0, 0, 0, 0);

    const diffTime = inputDateOnly - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return diffDays;
};

// Formate une date/heure pour l'affichage
const formatDateHeure = (dateTimeStr) => {
    if (!dateTimeStr) return "";
    const date = parseDateTime(dateTimeStr);
    if (!date) return dateTimeStr;

    return date.toLocaleString("fr-FR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Retourne les seuils de notification selon le type
const getNotificationThresholds = (typeFrequence, typeEvent) => {
    const defaults = { commencement: 7, retard: 7 };

    if (typeEvent === "suivi") {
        return { commencement: 4, retard: 4 };
    }

    switch (typeFrequence) {
        case "ponctuel":
        case "hebdomadaire":
        case "annuel":
        case "mensuel":
        case "bimestriel":
        case "trimestriel":
        case "quadrimestriel":
        case "semestriel":
            return defaults;
        default:
            return defaults;
    }
};

// Retourne la classe CSS selon le statut de notification
const getNotificationClass = (typeFrequence, typeEvent, dateTimeStr) => {
    const jours = calculerJoursRestants(dateTimeStr);
    const seuils = getNotificationThresholds(typeFrequence, typeEvent);

    if (jours === 0) return "border-blue-400";

    // Commencement (avant la date)
    if (jours > 0) {
        if (jours <= seuils.commencement) {
            return "border-yellow-400"; // Notification de commencement
        }
        return "border-green-400"; // Pas encore temps
    }

    // Retard (après la date)
    if (jours < 0) {
        if (Math.abs(jours) <= seuils.retard) {
            return "border-orange-400"; // Notification de retard
        }
        return "border-red-400"; // Très en retard
    }

    return "border-gray-400";
};

// Formate le statut de notification
const formatNotificationStatus = (typeFrequence, typeEvent, dateTimeStr) => {
    const jours = calculerJoursRestants(dateTimeStr);

    if (jours === 0) return "(Aujourd'hui)";
    if (jours > 0) return `(dans ${jours} jour${jours > 1 ? "s" : ""})`;
    return `(il y a ${Math.abs(jours)} jour${Math.abs(jours) > 1 ? "s" : ""})`;
};

// Calcule la prochaine date récurrente pour les événements périodiques
const getNextRecurrentDate = (startDate, intervalMonths) => {
    const now = new Date();
    let nextDate = new Date(startDate);

    // Trouver la prochaine occurrence après aujourd'hui
    while (nextDate <= now) {
        nextDate.setMonth(nextDate.getMonth() + intervalMonths);
    }

    return nextDate;
};

// Calcule la prochaine date anniversaire pour les événements annuels
const getNextAnnualDate = (startDate) => {
    const now = new Date();
    const nextDate = new Date(startDate);

    // Mettre à l'année courante
    nextDate.setFullYear(now.getFullYear());

    // Si la date est déjà passée cette année, prendre l'année suivante
    if (nextDate <= now) {
        nextDate.setFullYear(now.getFullYear() + 1);
    }

    return nextDate;
};

// Génère les notifications récurrentes avancées
const getAdvancedRecurrentNotifications = (frequenceObj, item) => {
    if (!frequenceObj || typeof frequenceObj === "string") {
        return [];
    }

    const notifications = [];

    // Pour les fréquences périodiques (Mensuel, Bimestriel, etc.)
    if (
        [
            "Mensuel",
            "Bimestriel",
            "Trimestriel",
            "Quadrimestriel",
            "Semestriel",
        ].includes(frequenceObj.type)
    ) {
        const planification = frequenceObj.planification;
        if (planification) {
            const debut = parseDateTime(planification.dateHeureDebut);
            const fin = parseDateTime(
                planification.dateHeureFin || planification.dateHeureFinale
            );

            if (debut && fin) {
                const intervalles = {
                    Mensuel: 1,
                    Bimestriel: 2,
                    Trimestriel: 3,
                    Quadrimestriel: 4,
                    Semestriel: 6,
                };

                const intervalMois = intervalles[frequenceObj.type];
                const prochaineDeclenchement = getNextRecurrentDate(
                    debut,
                    intervalMois
                );

                const joursRestants = calculerJoursRestants(
                    prochaineDeclenchement.toISOString()
                );

                // Seulement les notifications dans la plage de 7 jours
                if (Math.abs(joursRestants) <= 7) {
                    notifications.push({
                        type: "recurrent",
                        item,
                        joursRestants,
                        message: `Prochaine occurrence : ${formatDateHeure(
                            prochaineDeclenchement.toISOString()
                        )}`,
                        details: `Se répète tous les ${intervalMois} mois jusqu'au ${formatDateHeure(
                            fin.toISOString()
                        )}`,
                        class: getNotificationClass(
                            frequenceObj.type.toLowerCase(),
                            "debut",
                            prochaineDeclenchement.toISOString()
                        ),
                    });
                }
            }
        }
    }

    // Pour les fréquences annuelles
    if (frequenceObj.type === "Annuel" && frequenceObj.dateHeure) {
        const debut = parseDateTime(frequenceObj.dateHeure.debut);
        if (debut) {
            const prochaineAnnee = getNextAnnualDate(debut);
            const joursRestants = calculerJoursRestants(
                prochaineAnnee.toISOString()
            );

            if (Math.abs(joursRestants) <= 7) {
                notifications.push({
                    type: "recurrent",
                    item,
                    joursRestants,
                    message: `Prochaine occurrence : ${formatDateHeure(
                        prochaineAnnee.toISOString()
                    )}`,
                    details: "Se répète chaque année",
                    class: getNotificationClass(
                        "annuel",
                        "debut",
                        prochaineAnnee.toISOString()
                    ),
                });
            }
        }
    }

    return notifications;
};

// Génère les notifications progressives (J-7 à J+7) pour un item
const getProgressiveNotifications = (frequenceObj, item) => {
    if (!frequenceObj || typeof frequenceObj === "string") {
        return [];
    }

    const notifications = [];
    const dates = getAllImportantDatesFromFrequence(frequenceObj);

    dates.forEach((dateInfo) => {
        const targetDate = parseDateTime(dateInfo.dateTime);
        if (!targetDate) return;

        const seuils = getNotificationThresholds(
            dateInfo.frequenceType,
            dateInfo.eventType
        );

        // Notifications de commencement (J-7 à J-1)
        for (let i = seuils.commencement; i >= 1; i--) {
            const notifDate = new Date(targetDate);
            notifDate.setDate(notifDate.getDate() - i);

            const joursRestants = calculerJoursRestants(
                notifDate.toISOString()
            );

            // Seulement afficher les notifications des 7 prochains jours
            if (joursRestants >= 0 && joursRestants <= 7) {
                notifications.push({
                    type: "progressive",
                    item,
                    joursRestants: i, // Jours avant l'événement
                    message: `Dans ${i} jour${i > 1 ? "s" : ""} : ${
                        dateInfo.description
                    }`,
                    details: `Date cible : ${formatDateHeure(
                        dateInfo.dateTime
                    )}`,
                    class: "border-yellow-400",
                    priority: i,
                    targetDate: targetDate,
                    eventInfo: dateInfo,
                });
            }
        }

        // Notification Jour J
        const joursRestantsJ = calculerJoursRestants(dateInfo.dateTime);
        if (joursRestantsJ === 0) {
            notifications.push({
                type: "progressive",
                item,
                joursRestants: 0,
                message: `AUJOURD'HUI : ${dateInfo.description}`,
                details: `Maintenant : ${formatDateHeure(dateInfo.dateTime)}`,
                class: "border-blue-400",
                priority: 0,
                targetDate: targetDate,
                eventInfo: dateInfo,
            });
        }

        // Notifications de retard (J+1 à J+7)
        for (let i = 1; i <= seuils.retard; i++) {
            const notifDate = new Date(targetDate);
            notifDate.setDate(notifDate.getDate() + i);

            const joursRestants = calculerJoursRestants(
                notifDate.toISOString()
            );

            // Seulement afficher les notifications des 7 derniers jours
            if (joursRestants >= -7 && joursRestants < 0) {
                const severity =
                    i <= 3 ? "border-orange-400" : "border-red-400";
                notifications.push({
                    type: "progressive",
                    item,
                    joursRestants: -i, // Jours de retard (négatif)
                    message: `Retard de ${i} jour${i > 1 ? "s" : ""} : ${
                        dateInfo.description
                    }`,
                    details: `Date cible dépassée : ${formatDateHeure(
                        dateInfo.dateTime
                    )}`,
                    class: severity,
                    priority: 10 + i,
                    targetDate: targetDate,
                    eventInfo: dateInfo,
                });
            }
        }
    });

    return notifications;
};

// Extrait toutes les dates importantes d'une fréquence
const getAllImportantDatesFromFrequence = (frequenceObj, item = null) => {
    const dates = [];

    let itemDesc = "";

    const found = [...allDataEnCours.value, ...allDataEnRetard.value].find(
        (item) =>
            JSON.stringify(parseFrequence(item.frequence)) ===
            JSON.stringify(frequenceObj)
    );
    itemDesc = found?.description || "";

    if (!frequenceObj || typeof frequenceObj === "string") {
        return dates;
    }

    // Ponctuel
    if (frequenceObj.type === "Ponctuel" && frequenceObj.creneaux) {
        frequenceObj.creneaux.forEach((creneau, index) => {
            if (creneau.debut) {
                dates.push({
                    dateTime: creneau.debut,
                    frequenceType: "ponctuel",
                    eventType: "debut",
                    label: "Ponctuel - Début",
                    description: `${itemDesc} - Début du date et heure ${
                        index + 1
                    }`,
                });
            }
            if (creneau.fin) {
                dates.push({
                    dateTime: creneau.fin,
                    frequenceType: "ponctuel",
                    eventType: "fin",
                    label: "Ponctuel - Fin",
                    description: `${itemDesc} - Fin du date et heure ${
                        index + 1
                    }`,
                });
            }
            if (creneau.suivis) {
                creneau.suivis.forEach((suivi, suiviIndex) => {
                    dates.push({
                        dateTime: suivi,
                        frequenceType: "ponctuel",
                        eventType: "suivi",
                        label: "Ponctuel - Suivi",
                        description: `${itemDesc} - Suivi ${
                            suiviIndex + 1
                        } du date ${index + 1}`,
                    });
                });
            }
        });
    }

    // Hebdomadaire
    if (frequenceObj.type === "Hebdomadaire" && frequenceObj.dateHeure) {
        if (frequenceObj.dateHeure.blocs) {
            frequenceObj.dateHeure.blocs.forEach((bloc, index) => {
                if (bloc.debut) {
                    dates.push({
                        dateTime: bloc.debut,
                        frequenceType: "hebdomadaire",
                        eventType: "debut",
                        label: "Hebdomadaire - Début",
                        description: `${itemDesc} - Début du date et heure ${
                            index + 1
                        }`,
                    });
                }
                if (bloc.fin) {
                    dates.push({
                        dateTime: bloc.fin,
                        frequenceType: "hebdomadaire",
                        eventType: "fin",
                        label: "Hebdomadaire - Fin",
                        description: `${itemDesc} - Fin du date et heure ${
                            index + 1
                        }`,
                    });
                }
            });
        }

        if (frequenceObj.dateHeure.suivis) {
            frequenceObj.dateHeure.suivis.forEach((suivi, suiviIndex) => {
                dates.push({
                    dateTime: suivi,
                    frequenceType: "hebdomadaire",
                    eventType: "suivi",
                    label: "Hebdomadaire - Suivi",
                    description: `${itemDesc} - Suivi ${suiviIndex + 1}`,
                });
            });
        }
    }

    // Annuel
    if (frequenceObj.type === "Annuel" && frequenceObj.dateHeure) {
        if (frequenceObj.dateHeure.debut) {
            dates.push({
                dateTime: frequenceObj.dateHeure.debut,
                frequenceType: "annuel",
                eventType: "debut",
                label: "Annuel - Début",
                description: `${itemDesc} - Début de l'événement annuel`,
            });
        }
        if (frequenceObj.dateHeure.fin) {
            dates.push({
                dateTime: frequenceObj.dateHeure.fin,
                frequenceType: "annuel",
                eventType: "fin",
                label: "Annuel - Fin",
                description: `${itemDesc} - Fin de l'événement annuel`,
            });
        }
        if (frequenceObj.dateHeure.suivis) {
            frequenceObj.dateHeure.suivis.forEach((suivi, suiviIndex) => {
                dates.push({
                    dateTime: suivi,
                    frequenceType: "annuel",
                    eventType: "suivi",
                    label: "Annuel - Suivi",
                    description: `${itemDesc} - Suivi annuel ${suiviIndex + 1}`,
                });
            });
        }
    }

    // Fréquences périodiques (Mensuel, Bimestriel, etc.)
    if (
        [
            "Mensuel",
            "Bimestriel",
            "Trimestriel",
            "Quadrimestriel",
            "Semestriel",
        ].includes(frequenceObj.type) &&
        frequenceObj.planification
    ) {
        const planification = frequenceObj.planification;

        if (planification.dateHeureDebut) {
            dates.push({
                dateTime: planification.dateHeureDebut,
                frequenceType: frequenceObj.type.toLowerCase(),
                eventType: "debut",
                label: `${frequenceObj.type} - Début`,
                description: `${itemDesc} - Début de l'événement ${frequenceObj.type.toLowerCase()}`,
            });
        }

        if (planification.dateHeureFin) {
            dates.push({
                dateTime: planification.dateHeureFin,
                frequenceType: frequenceObj.type.toLowerCase(),
                eventType: "fin",
                label: `${frequenceObj.type} - Fin`,
                description: `${itemDesc} - Fin de l'événement ${frequenceObj.type.toLowerCase()}`,
            });
        }

        if (planification.dateHeureFinale) {
            dates.push({
                dateTime: planification.dateHeureFinale,
                frequenceType: frequenceObj.type.toLowerCase(),
                eventType: "finale",
                label: `${frequenceObj.type} - Fin finale`,
                description: `${itemDesc} - Fin finale de l'événement ${frequenceObj.type.toLowerCase()}`,
            });
        }

        if (planification.suivis) {
            planification.suivis.forEach((suivi, suiviIndex) => {
                dates.push({
                    dateTime: suivi.dateHeure,
                    frequenceType: frequenceObj.type.toLowerCase(),
                    eventType: "suivi",
                    label: `${frequenceObj.type} - Suivi`,
                    description: `${itemDesc} - Suivi ${
                        suiviIndex + 1
                    } de l'événement ${frequenceObj.type.toLowerCase()}`,
                });
            });
        }
    }

    return dates;
};

// Version améliorée de getRecurrentNotifications qui intègre les fonctionnalités avancées
const getRecurrentNotifications = (frequenceObj, item) => {
    const recurrentNotifs = getAdvancedRecurrentNotifications(
        frequenceObj,
        item
    );
    const progressiveNotifs = getProgressiveNotifications(frequenceObj, item);

    // Combiner et trier les notifications
    const allNotifs = [...recurrentNotifs, ...progressiveNotifs];

    return allNotifs.sort((a, b) => {
        if (a.joursRestants === 0) return -1;
        if (b.joursRestants === 0) return 1;
        const absA = Math.abs(a.joursRestants);
        const absB = Math.abs(b.joursRestants);
        if (absA === absB) {
            return a.joursRestants > b.joursRestants ? -1 : 1;
        }
        return absA - absB;
    });
};

// ==============================================
// METHODES EXISTANTES (modifiées si nécessaire)
// ==============================================

const parseFrequence = (frequence) => {
    if (typeof frequence === "string") {
        try {
            return JSON.parse(frequence);
        } catch (e) {
            return frequence;
        }
    }
    return frequence;
};

const verifierToutesAlertes = () => {
    const alerts = [];

    [...allDataEnCours.value, ...allDataEnRetard.value].forEach((item) => {
        if (!item.frequence) return;

        try {
            const frequenceObj = parseFrequence(item.frequence);
            const infosDebut = verifierAlerteDebut(frequenceObj);
            const infosSuivis = verifierAlerteSuivis(frequenceObj);
            const recurrentNotifications = getRecurrentNotifications(
                frequenceObj,
                item
            );

            if (infosDebut.doitAlerter) {
                alerts.push({
                    type: "debut",
                    item,
                    joursRestants: infosDebut.joursRestants,
                });
            }

            if (infosSuivis.doitAlerter) {
                alerts.push({
                    type: "suivi",
                    item,
                    joursRestants: infosSuivis.joursRestants,
                });
            }

            // Add all types of notifications
            recurrentNotifications.forEach((notification) => {
                alerts.push({
                    type: notification.type,
                    item: notification.item,
                    joursRestants: notification.joursRestants,
                    message: notification.message,
                    details: notification.details,
                    class: notification.class,
                });
            });
        } catch (error) {
            console.error("Erreur lors de la vérification des alertes:", error);
        }
    });

    alerts.sort((a, b) => {
        if (a.joursRestants === 0) return -1;
        if (b.joursRestants === 0) return 1;
        const absA = Math.abs(a.joursRestants);
        const absB = Math.abs(b.joursRestants);
        if (absA === absB) {
            return a.joursRestants > b.joursRestants ? -1 : 1;
        }
        return absA - absB;
    });

    alertQueue.value = alerts.filter((alert) => {
        const jours = Math.abs(alert.joursRestants);
        return jours <= 7;
    });

    if (appAlert.value) {
        afficherProchaineAlerte();
    }
};

const verifierToutesAlertesForEmail = () => {
    const alerts = [];

    [...allDataEnCours.value, ...allDataEnRetard.value].forEach((item) => {
        if (!item.frequence) return;

        try {
            const frequenceObj = parseFrequence(item.frequence);
            const infosDebut = verifierAlerteDebut(frequenceObj);
            const infosSuivis = verifierAlerteSuivis(frequenceObj);
            const recurrentNotifications = getRecurrentNotifications(
                frequenceObj,
                item
            );

            if (infosDebut.doitAlerter) {
                alerts.push({
                    type: "debut",
                    item,
                    joursRestants: infosDebut.joursRestants,
                });
            }

            if (infosSuivis.doitAlerter) {
                alerts.push({
                    type: "suivi",
                    item,
                    joursRestants: infosSuivis.joursRestants,
                });
            }

            // Add all types of notifications
            recurrentNotifications.forEach((notification) => {
                alerts.push({
                    type: notification.type,
                    item: notification.item,
                    joursRestants: notification.joursRestants,
                    message: notification.message,
                    details: notification.details,
                    class: notification.class,
                });
            });
        } catch (error) {
            console.error("Erreur lors de la vérification des alertes:", error);
        }
    });

    alerts.sort((a, b) => {
        if (a.joursRestants === 0) return -1;
        if (b.joursRestants === 0) return 1;
        const absA = Math.abs(a.joursRestants);
        const absB = Math.abs(b.joursRestants);
        if (absA === absB) {
            return a.joursRestants > b.joursRestants ? -1 : 1;
        }
        return absA - absB;
    });

    alertQueue.value = alerts.filter((alert) => {
        const jours = Math.abs(alert.joursRestants);
        return jours <= 7;
    });
};

const formatAlertMessage = (alert) => {
    if (alert.type === "recurrent" || alert.type === "progressive") {
        return `${alert.message}\n${alert.details}`;
    }
    const joursRestants = alert.joursRestants;
    return `${alert.type === "debut" ? "L'action" : "Le suivi de l'action"} "${
        alert.item.description
    }"
            ${
                joursRestants > 0
                    ? `aura lieu dans ${joursRestants} jours`
                    : joursRestants === 0
                    ? "aura lieu aujourd'hui"
                    : `est en retard de ${Math.abs(joursRestants)} jours`
            }`;
};

const afficherProchaineAlerte = () => {
    if (!appAlert.value) {
        return;
    }

    if (alertQueue.value.length > 0) {
        const nextAlert = alertQueue.value[0];
        currentAlert.value = nextAlert;

        if (nextAlert.type === "debut") {
            currentItem.value = nextAlert.item;
            joursRestantsDebut.value = nextAlert.joursRestants;
            showDebutAlertModal.value = true;
            showSuivisAlertModal.value = false;
        } else if (nextAlert.type === "suivi") {
            currentItem.value = nextAlert.item;
            joursRestantsSuivis.value = nextAlert.joursRestants;
            showSuivisAlertModal.value = true;
            showDebutAlertModal.value = false;
        } else if (
            nextAlert.type === "recurrent" ||
            nextAlert.type === "progressive"
        ) {
            currentItem.value = nextAlert.item;
            joursRestantsDebut.value = nextAlert.joursRestants; // Use debut modal for these types
            showDebutAlertModal.value = true;
            showSuivisAlertModal.value = false;
        }
    }
};

const handleEmailToggle = async () => {
    try {
        if (emailNotification.value) {
            const result = await emailService.toggleNotifications(
                true,
                userId.value
            );
            if (!result.success) {
                throw new Error(result.message);
            }
            await handlePendingAlerts();
        } else {
            const result = await emailService.toggleNotifications(
                false,
                userId.value
            );
            if (!result.success) {
                throw new Error(result.message);
            }
            console.log("Notifications par email désactivées");
        }
        localStorage.setItem(
            "emailNotification",
            emailNotification.value.toString()
        );
    } catch (error) {
        console.error(`Erreur: ${error.message || "Erreur inconnue"}`);
        emailNotification.value = !emailNotification.value;
    }
};

const handlePendingAlerts = async () => {
    verifierToutesAlertesForEmail();
    if (alertQueue.value.length > 0) {
        let successCount = 0;
        let errorCount = 0;
        let totalResponsablesNotified = 0;

        for (const alert of alertQueue.value) {
            try {
                const alertResult = await emailService.sendAlert(
                    {
                        sujet:
                            alert.type === "recurrent" ||
                            alert.type === "progressive"
                                ? `Alerte ${alert.type} pour ${alert.item.description}`
                                : `Alerte ${
                                      alert.type === "debut" ? "début" : "suivi"
                                  } d'action`,
                        message: formatAlertMessage(alert),
                        type: alert.type,
                        item: alert.item,
                    },
                    userId.value
                );
                if (alertResult.success) {
                    successCount++;
                    if (alertResult.details?.responsables) {
                        totalResponsablesNotified +=
                            alertResult.details.responsables.success_count;
                    }
                } else {
                    errorCount++;
                }
            } catch (alertError) {
                errorCount++;
            }
        }
        if (successCount > 0) {
            let message = `${successCount} alerte(s) envoyée(s) par email`;
            if (totalResponsablesNotified > 0) {
                message += ` + ${totalResponsablesNotified} responsable(s) notifié(s)`;
            }
            console.log(message);
        }
        if (errorCount > 0) {
            console.error(`${errorCount} alerte(s) n'ont pas pu être envoyées`);
        }
    } else {
        const testResult = await emailService.sendAlert(
            {
                sujet: "Notifications par email activées",
                message: "Vous recevrez désormais les alertes par email.",
                type: "test",
                item: { description: "Activation des notifications" },
            },
            userId.value
        );
        if (testResult.success) {
            console.log(
                "Notifications activées ! Un email de confirmation a été envoyé."
            );
        }
    }
};

// Other existing functions (unchanged)
const fetchAllData = async (checkAlerts = false) => {
    try {
        const withPagination = paginationEnabled.value ? "true" : "false";
        const response = await fetch(
            `/api/notifications?filter_en_cours=${selectedFilterEnCours.value}&filter_en_retard=${selectedFilterEnRetard.value}&per_page=10&page_en_cours=${currentPageEnCours.value}&page_en_retard=${currentPageEnRetard.value}&user_id=${userId.value}&with_pagination=${withPagination}`
        );
        const data = await response.json();
        displayedDataEnCours.value = data.en_cours.data;
        totalEnCours.value = data.en_cours.total;
        totalPagesEnCours.value = data.en_cours.last_page;
        currentPageEnCours.value = data.en_cours.current_page;
        displayedDataEnRetard.value = data.en_retard.data;
        totalEnRetard.value = data.en_retard.total;
        totalPagesEnRetard.value = data.en_retard.last_page;
        currentPageEnRetard.value = data.en_retard.current_page;
        if (!paginationEnabled.value) {
            allDataEnCours.value = data.en_cours.data;
            allDataEnRetard.value = data.en_retard.data;
        } else if (appAlert.value) {
            await fetchAllDataForAlerts();
        }
        if (checkAlerts && appAlert.value) {
            verifierToutesAlertes();
        }
    } catch (error) {
        console.error("Erreur lors de la récupération des données :", error);
    }
};

const fetchAllDataForAlerts = async () => {
    try {
        const response = await fetch(
            `/api/notifications?filter_en_cours=${selectedFilterEnCours.value}&filter_en_retard=${selectedFilterEnRetard.value}&user_id=${userId.value}&with_pagination=false`
        );
        const data = await response.json();
        allDataEnCours.value = data.en_cours.data;
        allDataEnRetard.value = data.en_retard.data;
    } catch (error) {
        console.error(
            "Erreur lors de la récupération des données complètes pour les alertes:",
            error
        );
    }
};

const onPaginationToggle = () => {
    currentPageEnCours.value = 1;
    currentPageEnRetard.value = 1;
    fetchAllData(false);
};

const onAlertToggle = () => {
    if (appAlert.value) {
        if (!paginationEnabled.value) {
            verifierToutesAlertes();
        } else {
            fetchAllDataForAlerts().then(() => {
                verifierToutesAlertes();
            });
        }
    } else {
        showDebutAlertModal.value = false;
        showSuivisAlertModal.value = false;
        alertQueue.value = [];
    }
};

const filterChanged = async () => {
    if (selectedFilterEnCours.value !== "all") {
        currentPageEnCours.value = 1;
    }
    if (selectedFilterEnRetard.value !== "all") {
        currentPageEnRetard.value = 1;
    }
    await fetchAllData(false);
};

const navigateToDetails = (id) => {
    const item =
        displayedDataEnCours.value.find((i) => i.id === id) ||
        displayedDataEnRetard.value.find((i) => i.id === id);
    if (item) {
        if (id === "specificId1") {
            router.push(`/admin/actions/auditinterne/voir/${id}`);
        } else if (id === "specificId2") {
            router.push(`/admin/actions/pta/voir/${id}`);
        } else {
            const isEnCours = displayedDataEnCours.value.some(
                (i) => i.id === id
            );
            const currentFilter = isEnCours
                ? selectedFilterEnCours.value
                : selectedFilterEnRetard.value;
            if (
                currentFilter === "AI" ||
                (currentFilter === "all" && item.num_actions?.startsWith("AI-"))
            ) {
                router.push(`/admin/actions/auditinterne/voir/${id}`);
            } else {
                router.push(`/admin/actions/pta/voir/${id}`);
            }
        }
    } else {
        console.error("Élément avec ID", id, "non trouvé dans les données");
    }
};

const prevPageEnCours = () => {
    if (currentPageEnCours.value > 1) {
        currentPageEnCours.value--;
        fetchAllData(false);
    }
};

const nextPageEnCours = () => {
    if (currentPageEnCours.value < totalPagesEnCours.value) {
        currentPageEnCours.value++;
        fetchAllData(false);
    }
};

const prevPageEnRetard = () => {
    if (currentPageEnRetard.value > 1) {
        currentPageEnRetard.value--;
        fetchAllData(false);
    }
};

const nextPageEnRetard = () => {
    if (currentPageEnRetard.value < totalPagesEnRetard.value) {
        currentPageEnRetard.value++;
        fetchAllData(false);
    }
};

const fermerModalDebut = () => {
    showDebutAlertModal.value = false;
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

const fermerModalSuivis = () => {
    showSuivisAlertModal.value = false;
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

const fermerToutesAlertes = () => {
    showDebutAlertModal.value = false;
    showSuivisAlertModal.value = false;
    alertQueue.value = [];
};

const voirAction = () => {
    if (currentAlert.value) {
        navigateToDetails(currentAlert.value.item.id);
    }
    showDebutAlertModal.value = false;
    showSuivisAlertModal.value = false;
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

const calculerJoursRestantsParJour = (
    jourString,
    intervalle = 4,
    type = null,
    periode = null
) => {
    const jourIndex = joursMap[jourString];
    if (jourIndex === undefined) return 0;
    const aujourdhui = new Date();
    const jourActuel = aujourdhui.getDay();
    let joursRestants = (jourIndex - jourActuel + 7) % 7;
    if (joursRestants === 0) {
        joursRestants = 7;
    }
    if (type) {
        if (periode === "semaineProchaine" && joursRestants < 7) {
            joursRestants += 7;
        } else if (periode === "moisProchain") {
            const premierJourMoisProchain = new Date(
                aujourdhui.getFullYear(),
                aujourdhui.getMonth() + 1,
                1
            );
            const jourPremierMois = premierJourMoisProchain.getDay();
            joursRestants = (jourIndex - jourPremierMois + 7) % 7;
            const dernierJourMoisCourant = new Date(
                aujourdhui.getFullYear(),
                aujourdhui.getMonth() + 1,
                0
            ).getDate();
            joursRestants += dernierJourMoisCourant - aujourdhui.getDate();
        }
        const moisSupplementaires = {
            Mensuel: 1,
            Bimestriel: 2,
            Trimestriel: 3,
            Quadrimestriel: 4,
            Semestriel: 6,
        };
        if (moisSupplementaires[type]) {
            joursRestants += moisSupplementaires[type] * 30;
        }
    }
    return joursRestants;
};

const getFrequenceBorderClass = (frequence) => {
    const frequenceObj = parseFrequence(frequence);
    if (
        !frequenceObj ||
        typeof frequenceObj === "string" ||
        frequenceObj.type === "Quotidien" ||
        frequenceObj.type === "TouteLAnnee"
    ) {
        return null;
    }
    const infoDebut = verifierAlerteDebut(frequenceObj);
    const infoSuivis = verifierAlerteSuivis(frequenceObj);
    const recurrentNotifications = getRecurrentNotifications(frequenceObj, {
        description: "",
    });
    if (
        infoDebut.doitAlerter ||
        infoSuivis.doitAlerter ||
        recurrentNotifications.length > 0
    ) {
        const joursRestants = infoDebut.doitAlerter
            ? infoDebut.joursRestants
            : infoSuivis.doitAlerter
            ? infoSuivis.joursRestants
            : recurrentNotifications[0]?.joursRestants || 0;
        const retardJours = Math.abs(joursRestants);
        if (joursRestants === 0) {
            return "border-blue-400";
        }
        if (joursRestants > 0) {
            return joursRestants <= 7
                ? "border-yellow-100"
                : "border-green-400";
        }
        return retardJours <= 7 ? "border-orange-400" : "border-red-400";
    }
    return null;
};

const getJoursRestantsLePlusProche = (jours, type = null) => {
    return jours.reduce((min, jour) => {
        const joursRestants = calculerJoursRestantsParJour(jour, 4, type);
        return Math.abs(joursRestants) < Math.abs(min) ? joursRestants : min;
    }, Number.MAX_SAFE_INTEGER);
};

const verifierAlerteDebut = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };
    let doitAlerter = false;
    let joursRestants = 0;
    switch (frequenceObj.type) {
        case "Ponctuel":
            if (!frequenceObj.debut) return { doitAlerter: false };
            joursRestants = calculerJoursRestants(frequenceObj.debut);
            doitAlerter = joursRestants >= -7 && joursRestants <= 7;
            break;
        case "Hebdomadaire":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.blocs
            ) {
                const joursList = frequenceObj.dateHeure.blocs.map(
                    (bloc) => bloc.debut
                );
                joursRestants = getJoursRestantsLePlusProche(joursList);
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.jours
            ) {
                joursRestants = getJoursRestantsLePlusProche(
                    frequenceObj.joursHeure.jours
                );
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            }
            break;
        case "Mensuel":
        case "Bimestriel":
        case "Trimestriel":
        case "Quadrimestriel":
        case "Semestriel":
        case "Annuel":
            const plagesAlerte = {
                Mensuel: 5,
                Bimestriel: 7,
                Trimestriel: 10,
                Quadrimestriel: 10,
                Semestriel: 14,
                Annuel: 21,
            };
            const plage = plagesAlerte[frequenceObj.type] || 7;
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.debut
            ) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.debut
                );
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.jours
            ) {
                joursRestants = getJoursRestantsLePlusProche(
                    frequenceObj.joursHeure.jours,
                    frequenceObj.type
                );
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            }
            break;
        default:
            doitAlerter = false;
            break;
    }
    return { doitAlerter, joursRestants };
};

const verifierAlerteSuivis = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };
    let doitAlerter = false;
    let joursRestants = 0;
    const plagesAlerte = {
        Ponctuel: 4,
        Hebdomadaire: 2,
        Mensuel: 3,
        Bimestriel: 5,
        Trimestriel: 7,
        Quadrimestriel: 7,
        Semestriel: 10,
        Annuel: 14,
    };
    const plage = plagesAlerte[frequenceObj.type] || 7;
    switch (frequenceObj.type) {
        case "Ponctuel":
            if (frequenceObj.suivis?.length > 0) {
                joursRestants = calculerJoursRestants(frequenceObj.suivis[0]);
                doitAlerter = Math.abs(joursRestants) <= plage;
            }
            break;
        case "Hebdomadaire":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.suivis?.length > 0
            ) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.suivis[0]
                );
                doitAlerter = Math.abs(joursRestants) <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const jours = frequenceObj.joursHeure.suivis[0]?.jours || [];
                if (jours.length > 0) {
                    joursRestants = getJoursRestantsLePlusProche(jours);
                    doitAlerter = Math.abs(joursRestants) <= plage;
                }
            }
            break;
        case "Mensuel":
        case "Bimestriel":
        case "Trimestriel":
        case "Quadrimestriel":
        case "Semestriel":
        case "Annuel":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.suivis?.length > 0
            ) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.suivis[0]
                );
                doitAlerter = Math.abs(joursRestants) <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const jours = frequenceObj.joursHeure.suivis[0]?.jours || [];
                if (jours.length > 0) {
                    joursRestants = getJoursRestantsLePlusProche(
                        jours,
                        frequenceObj.type
                    );
                    doitAlerter = Math.abs(joursRestants) <= plage;
                }
            }
            break;
    }
    return { doitAlerter, joursRestants };
};

// ==============================================
// METHODES UTILITAIRES SUPPLEMENTAIRES
// ==============================================

// Méthode pour obtenir un résumé des notifications pour un item
const getItemNotificationSummary = (item) => {
    if (!item.frequence) return null;

    try {
        const frequenceObj = parseFrequence(item.frequence);
        const notifications = getRecurrentNotifications(frequenceObj, item);

        if (notifications.length === 0) return null;

        const urgent = notifications.filter(
            (n) => Math.abs(n.joursRestants) <= 3
        );
        const upcoming = notifications.filter(
            (n) => n.joursRestants > 3 && n.joursRestants <= 7
        );
        const overdue = notifications.filter((n) => n.joursRestants < -3);

        return {
            total: notifications.length,
            urgent: urgent.length,
            upcoming: upcoming.length,
            overdue: overdue.length,
            nextNotification: notifications[0] || null,
        };
    } catch (error) {
        console.error(
            "Erreur lors du calcul du résumé des notifications:",
            error
        );
        return null;
    }
};

// Méthode pour formater l'affichage d'un résumé de notifications
const formatNotificationSummary = (summary) => {
    if (!summary) return "";

    const parts = [];
    if (summary.urgent > 0)
        parts.push(
            `${summary.urgent} urgent${summary.urgent > 1 ? "es" : "e"}`
        );
    if (summary.upcoming > 0)
        parts.push(
            `${summary.upcoming} prochaine${summary.upcoming > 1 ? "s" : ""}`
        );
    if (summary.overdue > 0) parts.push(`${summary.overdue} en retard`);

    return parts.join(", ");
};

// Méthode pour obtenir toutes les notifications actives du système
const getAllSystemNotifications = () => {
    const allNotifications = [];

    [...allDataEnCours.value, ...allDataEnRetard.value].forEach((item) => {
        if (!item.frequence) return;

        try {
            const frequenceObj = parseFrequence(item.frequence);
            const itemNotifications = getRecurrentNotifications(
                frequenceObj,
                item
            );
            allNotifications.push(...itemNotifications);
        } catch (error) {
            console.error(
                "Erreur lors de la récupération des notifications:",
                error
            );
        }
    });

    return allNotifications.sort((a, b) => {
        if (a.joursRestants === 0) return -1;
        if (b.joursRestants === 0) return 1;
        const absA = Math.abs(a.joursRestants);
        const absB = Math.abs(b.joursRestants);
        if (absA === absB) {
            return a.joursRestants > b.joursRestants ? -1 : 1;
        }
        return absA - absB;
    });
};

// Méthode pour obtenir les statistiques globales du système
const getSystemNotificationStats = () => {
    const notifications = getAllSystemNotifications();

    return {
        total: notifications.length,
        today: notifications.filter((n) => n.joursRestants === 0).length,
        upcoming: notifications.filter(
            (n) => n.joursRestants > 0 && n.joursRestants <= 7
        ).length,
        overdue: notifications.filter(
            (n) => n.joursRestants < 0 && Math.abs(n.joursRestants) <= 7
        ).length,
        progressive: notifications.filter((n) => n.type === "progressive")
            .length,
        recurrent: notifications.filter((n) => n.type === "recurrent").length,
    };
};

// ==============================================
// WATCHERS ET LIFECYCLE
// ==============================================

watch(appAlert, (newValue) => {
    localStorage.setItem("appAlert", newValue);
    if (newValue) {
        afficherProchaineAlerte();
    } else {
        showDebutAlertModal.value = false;
        showSuivisAlertModal.value = false;
    }
});

watch(emailNotification, (newValue) => {
    localStorage.setItem("emailNotification", newValue);
});

onMounted(async () => {
    if (userId.value) {
        isInitialLoad.value = true;
        await fetchAllData(true);
    } else {
        console.error("Utilisateur non connecté");
    }
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});

// ==============================================
// EXPORTS POUR UTILISATION DANS LE TEMPLATE
// ==============================================

// Exporter les méthodes intégrées pour utilisation dans le template
const exportedMethods = {
    // Méthodes de FrequenceInformations
    parseDateTime,
    calculerJoursRestants,
    formatDateHeure,
    getNotificationThresholds,
    getNotificationClass,
    formatNotificationStatus,
    getNextRecurrentDate,
    getNextAnnualDate,
    getRecurrentNotifications,
    getProgressiveNotifications,
    getAllImportantDatesFromFrequence,

    // Méthodes utilitaires
    getItemNotificationSummary,
    formatNotificationSummary,
    getAllSystemNotifications,
    getSystemNotificationStats,

    // Méthodes existantes
    parseFrequence,
    getFrequenceBorderClass,
    navigateToDetails,
};

Object.assign(window, exportedMethods);
</script>
