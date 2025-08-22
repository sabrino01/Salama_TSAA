<script setup>
import Sidebar from "../../../assets/SidebarResponsable.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/TableResponsable.vue";
import { Info, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();
const actionsResponsablesSWOT = ref([]);
const totalActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);
const tableRef = ref(null); // Référence vers le composant TableActions.vue

// Ajoutez cette nouvelle référence pour suivre l'état de la pagination
const paginationEnabled = ref(true);
const allActions = ref([]); // Pour stocker toutes les actions quand la pagination est désactivée

// Récupérer les données de l'utilisateur depuis le localStorage
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

const userId = computed(() => user.value?.responsables_id);

// Fonction pour charger les actions responsables AI
const chargerActionsResponsables = async (page = 1, search = "") => {
    try {
        const params = {
            page,
            search,
            per_page: paginationEnabled.value ? perPage.value : 1000000,
        };

        // Ajouter l'ID du responsable si l'utilisateur a le rôle responsable
        if (user.value?.role === "responsable" && userId.value) {
            params.responsable_id = userId.value;
        }

        if (paginationEnabled.value) {
            // Comportement original avec pagination
            const response = await axios.get("/api/actions-responsables-swot", {
                params,
            });

            actionsResponsablesSWOT.value = response.data.data;
            totalActions.value = Number(response.data.total) || 0;
            currentPage.value = Number(response.data.current_page) || 1;
            lastPage.value = Number(response.data.last_page) || 1;
            perPage.value = Number(response.data.per_page) || 10;
        } else {
            // Charger toutes les données sans pagination
            const response = await axios.get("/api/actions-responsables-swot", {
                params,
            });

            allActions.value = response.data.data || response.data;
            actionsResponsablesSWOT.value = allActions.value;
            totalActions.value = allActions.value.length;
            currentPage.value = 1;
            lastPage.value = 1;
        }
    } catch (error) {
        console.error(
            "Erreur lors du chargement des actions responsables SWOT:",
            error
        );
        toast.error("Erreur lors du chargement des actions responsables SWOT");
    }
};

// Fonction utilitaire pour convertir dd/mm/yyyy en yyyy-mm-dd
function convertDateToBackendFormat(str) {
    // Vérifie si la chaîne correspond à un format date français
    const match = str.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (match) {
        const [, day, month, year] = match;
        return `${year}-${month}-${day}`;
    }
    return str;
}

// Fonction pour gérer la recherche
const rechercherActions = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    let search = searchQuery.value.trim();
    // Si c'est une date au format dd/mm/yyyy, convertir pour le backend
    search = convertDateToBackendFormat(search);
    chargerActionsResponsables(currentPage.value, search);
};

// Ajoutez cette fonction pour basculer l'état de la pagination
const togglePagination = () => {
    paginationEnabled.value = !paginationEnabled.value;
    // Rechargez les données avec la nouvelle configuration
    chargerActionsResponsables(1, searchQuery.value);
};

// Fonction pour normaliser une chaîne JSON potentiellement imbriquée
const normalizeJsonString = (jsonString) => {
    if (!jsonString) return "";

    // Essayer de détecter et normaliser les JSON imbriqués
    let normalizedValue = jsonString;
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

    return normalizedValue;
};

// Données formatées pour afficher correctement la fréquence
const formattedActions = computed(() => {
    let actions = actionsResponsablesSWOT.value;

    return actions.map((action) => {
        // Créer une copie pour éviter de modifier les données originales
        const newAction = { ...action };
        // Reformater la date si elle existe
        if (newAction.date) {
            const [year, month, day] = newAction.date.split("-");
            newAction.date = `${day}/${month}/${year}`; // Reformater en jj/mm/yyyy
        }

        // Gérer la fréquence - Normaliser d'abord pour éviter les problèmes d'encodage multiples
        try {
            if (newAction.frequence) {
                // Normaliser la fréquence pour retirer les encodages multiples
                const normalizedFrequence = normalizeJsonString(
                    newAction.frequence
                );

                // Vérifier si la fréquence normalisée est un objet JSON
                const frequenceData =
                    typeof normalizedFrequence === "object" &&
                    normalizedFrequence !== null
                        ? normalizedFrequence
                        : typeof normalizedFrequence === "string" &&
                          normalizedFrequence.trim().startsWith("{")
                        ? JSON.parse(normalizedFrequence)
                        : null;

                if (frequenceData) {
                    // Conserver les données complètes pour le tooltip
                    newAction.frequenceComplete = frequenceData;

                    // Afficher uniquement le type s'il existe
                    const type = frequenceData.type || "Non défini";
                    const tooltip = formatJsonForTooltip(frequenceData);

                    newAction.frequence = type;

                    // Ajoute un champ combiné pour Excel
                    newAction.frequenceWithDetails = tooltip
                        ? `${type}\n${tooltip}`
                        : type;
                } else {
                    // Si ce n'est pas un JSON valide, conserver la valeur brute
                    newAction.frequenceComplete = null;
                    newAction.frequenceWithDetails = normalizedFrequence;
                    newAction.frequence = normalizedFrequence;
                }
            } else {
                // Si frequence est vide, conserver la valeur brute
                newAction.frequenceComplete = null;
                newAction.frequenceWithDetails = newAction.frequence;
            }
        } catch (e) {
            // En cas d'erreur, garder la valeur brute
            console.error("Erreur lors du parsing de la fréquence:", e);
            newAction.frequenceComplete = null;
            newAction.frequenceWithDetails = newAction.frequence;
        }

        return newAction;
    });
});

// Fonction générique pour formater les données JSON en texte lisible
const formatJsonForTooltip = (jsonData) => {
    if (!jsonData || typeof jsonData !== "object") return "";

    // Exclure le type car il est déjà affiché
    const { type, ...rest } = jsonData;

    // Si pas de données supplémentaires, pas de tooltip
    if (Object.keys(rest).length === 0) return "";

    // Convertir l'objet en chaîne JSON formatée
    const jsonString = JSON.stringify(rest, null, 2);

    // Formatter la chaîne pour l'affichage:
    // 1. Remplacer les virgules par des sauts de ligne
    // 2. Supprimer les guillemets autour des clés et valeurs
    // 3. Éliminer les backslashs d'échappement
    return jsonString
        .replace(/",/g, '"') // Supprimer les virgules après les guillemets
        .replace(/,/g, "\n") // Remplacer virgules par sauts de ligne
        .replace(/"/g, "") // Supprimer tous les guillemets
        .replace(/\\/g, "") // Supprimer les backslashs
        .replace(/\{/g, "") // Supprimer les accolades ouvrantes
        .replace(/\}/g, "") // Supprimer les accolades fermantes
        .replace(/\[/g, "") // Supprimer les crochets ouvrants
        .replace(/\]/g, "") // Supprimer les crochets fermants
        .replace(/\n\s*\n/g, "\n") // Supprimer les lignes vides
        .replace(/^\s+/gm, "") // Supprimer les espaces au début de chaque ligne
        .replace(
            /\b(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})\b/g, // Détecter le format datetime
            (_, year, month, day, hour, minute) =>
                `${day}/${month}/${year} à ${hour}:${minute}` // Reformater la date
        )
        .trim(); // Supprimer les espaces inutiles au début et à la fin
};

// Générer les numéros de page pour la pagination
const pages = computed(() => {
    // Limitons à 5 numéros de page maximum pour éviter trop de boutons
    const totalPages = lastPage.value;
    const current = currentPage.value;
    let pageNumbers = [];

    if (totalPages <= 5) {
        // Si moins de 5 pages, affichons-les toutes
        for (let i = 1; i <= totalPages; i++) {
            pageNumbers.push(i);
        }
    } else {
        // Sinon, affichons les 5 pages pertinentes
        // Toujours inclure la première et la dernière page
        if (current <= 3) {
            // Début: 1, 2, 3, 4, ..., n
            pageNumbers = [1, 2, 3, 4, totalPages];
        } else if (current >= totalPages - 2) {
            // Fin: 1, ..., n-3, n-2, n-1, n
            pageNumbers = [
                1,
                totalPages - 4,
                totalPages - 3,
                totalPages - 2,
                totalPages,
            ];
        } else {
            // Milieu: n-2, n-1, n, n+1, n+2
            pageNumbers = [
                current - 2,
                current - 1,
                current,
                current + 1,
                current + 2,
            ];
        }
    }
    return pageNumbers;
});

// Fonction pour changer de page
const changerPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        chargerActionsResponsables(page, searchQuery.value);
    }
};

const editerSWOT = (id) => {
    router.push(`/responsable/actions/swot/editer/${id}`);
};

// Colonnes pour la table
const columns = [
    { label: "N°", field: "num_actions" },
    { label: "Date", field: "date" },
    {
        label: "Description non conformité",
        field: "description",
        classes: "truncate", // Classes Tailwind à appliquer à cette colonne
        isExpandable: true, // Indique que cette colonne a un contenu extensible
        render: (row) => row.description, // Retourne directement la valeur
    },
    { label: "Constat", field: "constat_libelle" },
    { label: "Source", field: "source_libelle" },
    { label: "Type Action", field: "type_action_libelle" },
    {
        label: "Frequence",
        field: "frequence",
        // Ajouter une fonction de rendu personnalisée pour cette colonne
        render: (row) => {
            if (!row.frequenceComplete) return row.frequence;
            // Formatage générique du tooltip
            const tooltipContent = formatJsonForTooltip(row.frequenceComplete);
            // Si pas de contenu supplémentaire, pas de tooltip
            if (!tooltipContent) return row.frequence;
            // Retourner un objet qui indique qu'il faut un rendu personnalisé
            return {
                customRender: true,
                value: row.frequence,
                tooltipContent,
            };
        },
    },
    { label: "Ajouter par", field: "nom_utilisateur" },
];

// Actions pour la table
const actions = [
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerSWOT(row.id),
    },
];

// Fonction pour charger les actions au démarrage
onMounted(() => {
    chargerActionsResponsables(currentPage.value, searchQuery.value);
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});
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
            <div class="flex-1 p-5 bg-gray-50 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace actions pour le Responsable, vous pourriez
                        voir et éditer les informations sur les actions, que ce
                        soit Audit Interne, PTA, Audit Externe, CAC, SWOT,
                        Enquête de Satisfaction, Audit Interne Inspection.
                    </p>
                </div>

                <!-- Choix d'action à faire(A.I / PTA / A.E / CAC / SWOT / Enquête) -->
                <div class="flex w-full mt-5 ml-4 justify-center space-x-4">
                    <!-- Lien Audit Interne -->
                    <router-link
                        to="/responsable/actions/auditinterne"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path ===
                                '/responsable/actions/auditinterne',
                        }"
                    >
                        Audit Interne
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien PTA -->
                    <router-link
                        to="/responsable/actions/pta"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/responsable/actions/pta',
                        }"
                    >
                        PTA
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien AE -->
                    <router-link
                        to="/responsable/actions/ae"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/responsable/actions/ae',
                        }"
                    >
                        Audit Externe
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien CAC -->
                    <router-link
                        to="/responsable/actions/cac"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/responsable/actions/cac',
                        }"
                    >
                        CAC
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien Enquête de Satisfaction -->
                    <router-link
                        to="/responsable/actions/enquete"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/responsable/actions/enquete',
                        }"
                    >
                        ENQUETE
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien SWOT -->
                    <router-link
                        to="/responsable/actions/swot"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/responsable/actions/swot',
                        }"
                    >
                        SWOT
                    </router-link>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Barre de recherche -->
                    <div
                        class="flex items-center ml-6 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                    >
                        <Search class="w-5 h-5 mr-2 text-gray-500" />
                        <input
                            type="text"
                            placeholder="Rechercher...."
                            class="outline-none bg-transparent text-gray-800 placeholder-gray-500"
                            v-model="searchQuery"
                            @input="rechercherActions"
                        />
                    </div>

                    <!-- Toggle de pagination -->
                    <div class="relative ml-4">
                        <button
                            @click="togglePagination"
                            class="flex items-center justify-center border border-gray-400 text-black px-4 py-2 rounded-md"
                            :class="{
                                'bg-blue-100': !paginationEnabled,
                                'bg-white': paginationEnabled,
                            }"
                        >
                            {{
                                paginationEnabled
                                    ? "Désactiver Pagination"
                                    : "Activer Pagination"
                            }}
                        </button>
                    </div>
                </div>

                <!-- Tableau pour afficher les données d'audit interne -->
                <div class="mt-5 ml-4">
                    <Table
                        ref="tableRef"
                        :columns="columns"
                        :data="formattedActions"
                        :actions="actions"
                    />
                </div>

                <!-- Footer -->
                <div class="flex w-full mt-5 justify-between">
                    <!-- Résultat -->
                    <div
                        class="flex items-center text-gray-500 justify-start px-4 space-x-2"
                    >
                        <span>Résultat</span>
                        <strong>{{
                            totalActions === 0
                                ? 0
                                : paginationEnabled
                                ? (currentPage - 1) * perPage + 1
                                : 1
                        }}</strong>
                        <span>à</span>
                        <strong>
                            {{
                                totalActions === 0
                                    ? 0
                                    : paginationEnabled
                                    ? Math.min(
                                          currentPage * perPage,
                                          totalActions
                                      )
                                    : totalActions
                            }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalActions }}</strong>
                    </div>
                    <!-- Pagination - masquer quand elle est désactivée -->
                    <div
                        v-if="paginationEnabled"
                        class="flex items-center justify-end space-x-2"
                    >
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage <= 1 || totalActions === 0"
                            @click="changerPage(currentPage - 1)"
                        >
                            <ChevronLeft class="w-4 h-4" /> Préc.
                        </button>
                        <!-- Numéros de page -->
                        <div class="flex space-x-1">
                            <button
                                v-for="page in pages"
                                :key="page"
                                class="flex items-center justify-center w-8 h-8 rounded-md border"
                                :class="
                                    page === currentPage
                                        ? 'bg-[#0062ff] text-white'
                                        : 'bg-white text-black border-gray-300'
                                "
                                @click="changerPage(page)"
                            >
                                {{ page }}
                            </button>
                        </div>
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="
                                currentPage >= lastPage || totalActions === 0
                            "
                            @click="changerPage(currentPage + 1)"
                        >
                            Suiv. <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
