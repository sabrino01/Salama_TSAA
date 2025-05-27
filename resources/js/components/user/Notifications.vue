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
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Notifications
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace Notifications, vous pouvez voir et gérer
                        les notifications, que ce soit une alerte par email ou
                        une alerte dans l'application et de faire plus.
                    </p>
                </div>

                <div class="border bg-gray-400 mt-6"></div>

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
                            <option value="all">Audit Interne / PTA</option>
                            <option value="AI">Audit Interne</option>
                            <option value="PTA">PTA</option>
                        </select>
                    </div>

                    <!-- Colonne centrale -->
                    <div class="w-3/4">
                        <div class="text-lg font-bold mb-4">
                            Résultats : {{ totalEnCours }}
                        </div>
                        <table class="table-fixed w-full">
                            <thead class="bg-gray-200 text-lg">
                                <tr>
                                    <th class="text-start px-4 py-2">Source</th>
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
                                    ) in paginatedDataEnCours"
                                    :key="index"
                                    @click="navigateToDetails(item.id)"
                                    class="cursor-pointer border-b-4 border-green-500 bg-white text-black hover:bg-gray-100 shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2"
                                >
                                    <td class="px-4 py-2">
                                        {{ item.source_libelle }}
                                    </td>
                                    <td class="truncate px-4 py-2">
                                        {{ item.description }}
                                    </td>
                                    <td
                                        :class="[
                                            'px-4 py-2',
                                            getFrequenceBorderClass(
                                                item.frequence
                                            ),
                                        ]"
                                    >
                                        <FrequenceInformations
                                            :frequence="item.frequence"
                                            ref="frequenceInfo"
                                        />
                                    </td>
                                </tr>
                                <tr v-if="paginatedDataEnCours.length === 0">
                                    <td
                                        colspan="3"
                                        class="px-4 py-2 text-center"
                                    >
                                        Aucune Action en cours
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-between items-center mt-4">
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
                                    currentPageEnCours === totalPagesEnCours
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

                    <!-- Colonne droite -->
                    <div class="w-1/6 pl-4 mr-2">
                        <div class="flex flex-col space-y-4">
                            <!-- Toggle pour l'envoi d'email -->
                            <div class="flex items-center space-x-2">
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

                            <!-- Toggle pour l'alerte dans l'application -->
                            <div class="flex items-center space-x-2">
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
                                    class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
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
                            <option value="all">Audit Interne / PTA</option>
                            <option value="AI">Audit Interne</option>
                            <option value="PTA">PTA</option>
                        </select>
                    </div>

                    <!-- Colonne centrale -->
                    <div class="w-2/3">
                        <div class="text-lg font-bold mb-4">
                            Résultats : {{ totalEnRetard }}
                        </div>
                        <table class="table-fixed w-full">
                            <thead class="bg-gray-200 text-lg">
                                <tr>
                                    <th class="text-start px-4 py-2">Source</th>
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
                                    ) in paginatedDataEnRetard"
                                    :key="index"
                                    @click="navigateToDetails(item.id)"
                                    class="cursor-pointer border-b-4 border-red-500 bg-white text-black hover:bg-gray-100 shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2"
                                >
                                    <td class="px-4 py-2">
                                        {{ item.source_libelle }}
                                    </td>
                                    <td class="truncate px-4 py-2">
                                        {{ item.description }}
                                    </td>
                                    <td
                                        :class="[
                                            'px-4 py-2',
                                            getFrequenceBorderClass(
                                                item.frequence
                                            ),
                                        ]"
                                    >
                                        <FrequenceInformations
                                            :frequence="item.frequence"
                                            ref="frequenceInfo"
                                        />
                                    </td>
                                </tr>
                                <tr v-if="paginatedDataEnRetard.length === 0">
                                    <td
                                        colspan="3"
                                        class="px-4 py-2 text-center"
                                    >
                                        Aucune Actions en retard
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-between items-center mt-4">
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
                                    currentPageEnRetard === totalPagesEnRetard
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
                                            class="text-sm text-gray-500 whitespace-normal break-words"
                                        >
                                            L'action sur "{{
                                                currentItem?.description
                                            }}"
                                            <span v-if="joursRestantsDebut > 0"
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
                                                    Math.abs(joursRestantsDebut)
                                                }}
                                                jours</span
                                            >
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
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
                                            <span v-if="joursRestantsSuivis > 0"
                                                >aura lieu dans
                                                {{ joursRestantsSuivis }}
                                                jours</span
                                            >
                                            <span
                                                v-else-if="
                                                    joursRestantsSuivis === 0
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
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
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
import Sidebar from "../assets/SidebarUser.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
import FrequenceInformations from "../assets/FrequenceNotifications.vue";
import { ChevronLeft, ChevronRight, Info } from "lucide-vue-next";
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import emailService from "../../utils/emailService.js"; // Importer le service email

const router = useRouter();
const emailNotification = ref(
    localStorage.getItem("emailNotification") === "true"
);
const appAlert = ref(localStorage.getItem("appAlert") === "true");
const currentPageEnCours = ref(1);
const currentPageEnRetard = ref(1);
const totalPagesEnCours = ref(1);
const totalPagesEnRetard = ref(1);
const totalEnCours = ref(0);
const totalEnRetard = ref(0);
const paginatedDataEnCours = ref([]);
const paginatedDataEnRetard = ref([]);
const selectedFilterEnCours = ref("all");
const selectedFilterEnRetard = ref("all");
const showDebutAlertModal = ref(false);
const showSuivisAlertModal = ref(false);
const joursRestantsDebut = ref(0);
const joursRestantsSuivis = ref(0);
const currentItem = ref(null);
const alertQueue = ref([]); // File d'attente des alertes
const currentAlert = ref(null); // Alerte actuellement affichée
const isInitialLoad = ref(true);
const allDataEnCours = ref([]);
const allDataEnRetard = ref([]);

// Données pour la gestion des fréquences
const joursMap = {
    Lundi: 1,
    Mardi: 2,
    Mercredi: 3,
    Jeudi: 4,
    Vendredi: 5,
    Samedi: 6,
    Dimanche: 0,
};

// Récupérer les données de l'utilisateur depuis le localStorage
const user = computed(() => {
    try {
        return JSON.parse(localStorage.getItem("user")) || {};
    } catch (e) {
        console.error(
            "Erreur lors de la récupération des données utilisateur:",
            e
        );
        return {};
    }
});
const userId = computed(() => user.value?.id);

const handleEmailToggle = async () => {
    try {
        if (emailNotification.value) {
            // Pas de vérification de configuration pour les users
            // Activer les notifications
            const result = await emailService.toggleNotifications(
                true,
                userId.value
            );

            if (!result.success) {
                throw new Error(result.message);
            }

            // Vérifier et envoyer les alertes
            verifierToutesAlertesForEmail();

            if (alertQueue.value.length > 0) {
                let successCount = 0;
                let errorCount = 0;

                for (const alert of alertQueue.value) {
                    try {
                        await emailService.sendAlert(
                            {
                                sujet: `Alerte ${
                                    alert.type === "debut" ? "début" : "suivi"
                                } d'action`,
                                message: formatAlertMessage(alert),
                                type:
                                    alert.type === "suivis"
                                        ? "suivi"
                                        : alert.type,
                                item: alert.item,
                            },
                            userId.value
                        );
                        successCount++;
                    } catch (alertError) {
                        console.error("Erreur envoi alerte:", alertError);
                        errorCount++;
                    }
                }

                if (successCount > 0) {
                    toast.success(
                        `${successCount} alerte(s) envoyée(s) par email`
                    );
                }
                if (errorCount > 0) {
                    toast.error(
                        `${errorCount} alerte(s) n'ont pas pu être envoyées`
                    );
                }
            } else {
                // Email de confirmation pour les users
                await emailService.sendAlert(
                    {
                        sujet: "Notifications par email activées",
                        message:
                            "Vous recevrez désormais les alertes par email.",
                        type: "test",
                        item: { description: "Activation des notifications" },
                    },
                    userId.value
                );
                toast.success(
                    "Notifications activées ! Un email de confirmation a été envoyé."
                );
            }
        } else {
            // Désactiver les notifications
            const result = await emailService.toggleNotifications(
                false,
                userId.value
            );

            if (!result.success) {
                throw new Error(result.message);
            }

            toast.success("Notifications par email désactivées");
        }

        localStorage.setItem(
            "emailNotification",
            emailNotification.value.toString()
        );
    } catch (error) {
        console.error("Erreur lors du toggle des notifications:", error);
        toast.error(`Erreur: ${error.message || "Erreur inconnue"}`);
        emailNotification.value = !emailNotification.value;
    }
};

// Nouvelle fonction pour vérifier les alertes uniquement pour l'email (sans afficher les modales)
const verifierToutesAlertesForEmail = () => {
    const alerts = [];

    [...allDataEnCours.value, ...allDataEnRetard.value].forEach((item) => {
        if (!item.frequence) return;

        try {
            const frequenceObj = parseFrequence(item.frequence);
            const infosDebut = verifierAlerteDebut(frequenceObj);
            const infosSuivis = verifierAlerteSuivis(frequenceObj);

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
        } catch (error) {
            console.error("Erreur lors de la vérification des alertes:", error);
        }
    });

    // Trier les alertes
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

    // Mettre à jour la file d'attente des alertes (filtrer seulement les alertes <= 7 jours)
    alertQueue.value = alerts.filter((alert) => {
        const jours = Math.abs(alert.joursRestants);
        return jours <= 7;
    });

    // Ne PAS appeler afficherProchaineAlerte() ici pour éviter l'ouverture des modales
};

// Formater le message d'alerte
const formatAlertMessage = (alert) => {
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

// Nouvelle fonction pour récupérer toutes les données
const fetchAllData = async (checkAlerts = false) => {
    try {
        const response = await fetch(
            `/api/notifications/all?filter_en_cours=${selectedFilterEnCours.value}&filter_en_retard=${selectedFilterEnRetard.value}&user_id=${userId.value}`
        );
        const data = await response.json();

        allDataEnCours.value = data.en_cours;
        allDataEnRetard.value = data.en_retard;

        // Ne vérifier les alertes que si checkAlerts est true
        if (checkAlerts) {
            verifierToutesAlertes();
        }
    } catch (error) {
        console.error(
            "Erreur lors de la récupération des données complètes:",
            error
        );
    }
};

// Fonction pour récupérer les données filtrées
const fetchFilteredData = async () => {
    try {
        const response = await fetch(
            `/api/notifications?filter_en_cours=${selectedFilterEnCours.value}&filter_en_retard=${selectedFilterEnRetard.value}&per_page=10&page_en_cours=${currentPageEnCours.value}&page_en_retard=${currentPageEnRetard.value}&user_id=${userId.value}`
        );
        const data = await response.json();

        // Données pour "En cours"
        paginatedDataEnCours.value = data.en_cours.data;
        totalEnCours.value = data.en_cours.total;
        totalPagesEnCours.value = data.en_cours.last_page;
        currentPageEnCours.value = data.en_cours.current_page;

        // Données pour "En retard"
        paginatedDataEnRetard.value = data.en_retard.data;
        totalEnRetard.value = data.en_retard.total;
        totalPagesEnRetard.value = data.en_retard.last_page;
        currentPageEnRetard.value = data.en_retard.current_page;

        // Vérifier les alertes uniquement au chargement initial
        if (isInitialLoad.value) {
            // Préparer les alertes même si le toggle est désactivé
            verifierToutesAlertes();
            // Mais ne les afficher que si le toggle est activé
            if (!appAlert.value) {
                showDebutAlertModal.value = false;
                showSuivisAlertModal.value = false;
            }
            isInitialLoad.value = false;
        }
    } catch (error) {
        console.error("Erreur lors de la récupération des données :", error);
    }
};

// Fonction pour vérifier toutes les alertes
const verifierToutesAlertes = () => {
    const alerts = [];

    // Utiliser toutes les données au lieu des données paginées
    [...allDataEnCours.value, ...allDataEnRetard.value].forEach((item) => {
        if (!item.frequence) return;

        try {
            const frequenceObj = parseFrequence(item.frequence);
            const infosDebut = verifierAlerteDebut(frequenceObj);
            const infosSuivis = verifierAlerteSuivis(frequenceObj);

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
        } catch (error) {
            console.error("Erreur lors de la vérification des alertes:", error);
        }
    });

    // Trier les alertes
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

    // Mettre à jour la file d'attente des alertes
    alertQueue.value = alerts.filter((alert) => {
        const jours = Math.abs(alert.joursRestants);
        return jours <= 7;
    });

    if (appAlert.value) {
        afficherProchaineAlerte();
    }
};

// Nouvelle fonction pour afficher l'alerte suivante
const afficherProchaineAlerte = () => {
    // Ne pas afficher d'alertes si le toggle est désactivé
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
        }
    }
};

// Fonctions de changement de filtre et navigation - sans vérification d'alertes
const filterChanged = async () => {
    if (selectedFilterEnCours.value !== "all") {
        currentPageEnCours.value = 1;
    }
    if (selectedFilterEnRetard.value !== "all") {
        currentPageEnRetard.value = 1;
    }

    // Recharger les données sans vérifier les alertes
    await fetchAllData(false);
    await fetchFilteredData();
};

// Fonction pour naviguer vers les détails d'une action
const navigateToDetails = (id) => {
    // Rechercher l'élément dans les deux listes de données
    const item =
        paginatedDataEnCours.value.find((i) => i.id === id) ||
        paginatedDataEnRetard.value.find((i) => i.id === id);

    if (item) {
        // Déterminer la destination en fonction de l'ID
        if (id === "specificId1") {
            router.push(`/user/actions/auditinterne/voir/${id}`);
        } else if (id === "specificId2") {
            router.push(`/user/actions/pta/voir/${id}`);
        } else {
            // Logique par défaut si l'ID ne correspond pas aux cas spécifiques
            // Déterminer le filtre à utiliser en fonction de la table d'origine
            const isEnCours = paginatedDataEnCours.value.some(
                (i) => i.id === id
            );
            const currentFilter = isEnCours
                ? selectedFilterEnCours.value
                : selectedFilterEnRetard.value;

            if (
                currentFilter === "AI" ||
                (currentFilter === "all" && item.num_actions?.startsWith("AI-"))
            ) {
                router.push(`/user/actions/auditinterne/voir/${id}`);
            } else {
                router.push(`/user/actions/pta/voir/${id}`);
            }
        }
    } else {
        console.error("Élément avec ID", id, "non trouvé dans les données");
    }
};

// Pagination pour "En cours"
const prevPageEnCours = () => {
    if (currentPageEnCours.value > 1) {
        currentPageEnCours.value--;
        fetchFilteredData(false); // Ne pas vérifier les alertes lors de la pagination
    }
};

const nextPageEnCours = () => {
    if (currentPageEnCours.value < totalPagesEnCours.value) {
        currentPageEnCours.value++;
        fetchFilteredData(false); // Ne pas vérifier les alertes lors de la pagination
    }
};

// Pagination pour "En retard"
const prevPageEnRetard = () => {
    if (currentPageEnRetard.value > 1) {
        currentPageEnRetard.value--;
        fetchFilteredData(false); // Ne pas vérifier les alertes lors de la pagination
    }
};

const nextPageEnRetard = () => {
    if (currentPageEnRetard.value < totalPagesEnRetard.value) {
        currentPageEnRetard.value++;
        fetchFilteredData(false); // Ne pas vérifier les alertes lors de la pagination
    }
};

// Fonctions pour les modales d'alertes
const fermerModalDebut = () => {
    showDebutAlertModal.value = false;
    // Passer à l'alerte suivante
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

const fermerModalSuivis = () => {
    showSuivisAlertModal.value = false;
    // Passer à l'alerte suivante
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

const voirAction = () => {
    if (currentAlert.value) {
        navigateToDetails(currentAlert.value.item.id);
    }
    // Fermer la modale actuelle et passer à la suivante
    showDebutAlertModal.value = false;
    showSuivisAlertModal.value = false;
    alertQueue.value.shift();
    afficherProchaineAlerte();
};

// Fonctions de calcul pour les fréquences
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

const calculerJoursRestants = (dateStr) => {
    if (!dateStr) return 0;

    const dateParts = dateStr.split("-");
    if (dateParts.length < 3) return 0;

    const inputDate = new Date(
        parseInt(dateParts[0]),
        parseInt(dateParts[1]) - 1,
        parseInt(dateParts[2])
    );

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const diffTime = inputDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return diffDays;
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

    // Si c'est aujourd'hui mais qu'on a déjà dépassé l'heure, on ajoute 7 jours
    if (joursRestants === 0) {
        joursRestants = 7;
    }

    // Ajustements pour les périodes spécifiques
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

        // Ajustement pour les fréquences mensuelles et plus
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

    // Vérifier d'abord le format et les types spéciaux
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

    // Vérifier s'il y a des alertes
    if (infoDebut.doitAlerter || infoSuivis.doitAlerter) {
        const joursRestants = infoDebut.doitAlerter
            ? infoDebut.joursRestants
            : infoSuivis.joursRestants;

        // Séquence de couleurs selon le nombre de jours restants :
        if (joursRestants === 0) {
            return "border-blue-400"; // Aujourd'hui
        }
        if (joursRestants > 0) {
            return joursRestants <= 7
                ? "border-yellow-400" // Dans moins de 7 jours
                : "border-green-400"; // Plus de 7 jours
        }
        // Pour les retards
        const retardJours = Math.abs(joursRestants);
        if (retardJours <= 7) {
            return "border-orange-400"; // Retard de moins de 7 jours
        }
        return "border-red-400"; // Retard de plus de 7 jours
    }

    return null;
};

const verifierAlerteDebut = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };

    let doitAlerter = false;
    let joursRestants = 0;

    switch (frequenceObj.type) {
        case "Ponctuel":
            joursRestants = calculerJoursRestants(frequenceObj.debut);
            doitAlerter = joursRestants >= -7 && joursRestants <= 7;
            break;

        case "Hebdomadaire":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.blocs
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.dateHeure.blocs.forEach((bloc) => {
                    const jours = calculerJoursRestants(bloc.debut);
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
                doitAlerter = joursRestants >= -7 && joursRestants <= 7;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.joursHeure.jours.forEach((jour) => {
                    const jours = calculerJoursRestantsParJour(jour);
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
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
                Mensuel: 5, // ±5 jours
                Bimestriel: 7, // ±7 jours
                Trimestriel: 10, // ±10 jours
                Quadrimestriel: 10, // ±10 jours
                Semestriel: 14, // ±14 jours
                Annuel: 21, // ±21 jours
            };

            if (frequenceObj.mode === "dateHeure" && frequenceObj.dateHeure) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.debut
                );
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.joursHeure.jours.forEach((jour) => {
                    const jours = calculerJoursRestantsParJour(
                        jour,
                        4,
                        frequenceObj.type
                    );
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            }
            break;
    }

    return { doitAlerter, joursRestants };
};

const verifierAlerteSuivis = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };

    let doitAlerter = false;
    let joursRestants = 0;

    switch (frequenceObj.type) {
        case "Ponctuel":
            if (frequenceObj.suivis?.length > 0) {
                joursRestants = calculerJoursRestants(frequenceObj.suivis[0]);
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
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
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const premierSuivi = frequenceObj.joursHeure.suivis[0];
                if (premierSuivi.jours?.length > 0) {
                    let minJours = Number.MAX_SAFE_INTEGER;
                    premierSuivi.jours.forEach((jour) => {
                        const jours = calculerJoursRestantsParJour(jour, 2);
                        if (Math.abs(jours) < Math.abs(minJours)) {
                            minJours = jours;
                        }
                    });
                    joursRestants = minJours;
                    doitAlerter = joursRestants >= -2 && joursRestants <= 2;
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
                // Plages d'alerte selon le type
                const plagesAlerte = {
                    Mensuel: 3, // ±3 jours
                    Bimestriel: 5, // ±5 jours
                    Trimestriel: 7, // ±7 jours
                    Quadrimestriel: 7, // ±7 jours
                    Semestriel: 10, // ±10 jours
                    Annuel: 14, // ±14 jours
                };
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const premierSuivi = frequenceObj.joursHeure.suivis[0];
                if (premierSuivi.jours?.length > 0) {
                    let minJours = Number.MAX_SAFE_INTEGER;
                    premierSuivi.jours.forEach((jour) => {
                        const jours = calculerJoursRestantsParJour(
                            jour,
                            2,
                            frequenceObj.type
                        );
                        if (Math.abs(jours) < Math.abs(minJours)) {
                            minJours = jours;
                        }
                    });
                    joursRestants = minJours;
                    // Même plages d'alerte que ci-dessus
                    const plagesAlerte = {
                        Mensuel: 3,
                        Bimestriel: 5,
                        Trimestriel: 7,
                        Quadrimestriel: 7,
                        Semestriel: 10,
                        Annuel: 14,
                    };
                    const plage = plagesAlerte[frequenceObj.type] || 7;
                    doitAlerter =
                        joursRestants >= -plage && joursRestants <= plage;
                }
            }
            break;
    }

    return { doitAlerter, joursRestants };
};

// Ajouter après les autres refs
watch(appAlert, (newValue) => {
    // Sauvegarder l'état dans le localStorage
    localStorage.setItem("appAlert", newValue);

    if (newValue) {
        // Si on active les alertes, on vérifie s'il y a des alertes en attente
        afficherProchaineAlerte();
    } else {
        // Si on désactive les alertes, on ferme toutes les modales
        showDebutAlertModal.value = false;
        showSuivisAlertModal.value = false;
    }
});

watch(emailNotification, (newValue) => {
    localStorage.setItem("emailNotification", newValue);
});

// Appeler les données au montage du composant
onMounted(async () => {
    if (userId.value) {
        isInitialLoad.value = true;
        // Charger les données et vérifier les alertes seulement au chargement initial
        await fetchAllData(true);
        await fetchFilteredData();
    } else {
        console.error("Utilisateur non connecté");
    }
});
</script>
