<script>
export const auditInterneData = ref([
    {
        sources: "Audit Interne",
        action: "Demande le statut",
        datesuivi: "14/04/2025",
    },
    {
        sources: "Audit Interne",
        action: "Exploiter les informations",
        datesuivi: "13/10/2025",
    },
    {
        sources: "Audit Interne",
        action: "Renforcer la coordination",
        datesuivi: "10/10/2024",
    },
]);
</script>

<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/TableActions.vue";
import * as XLSX from "xlsx";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const actionsAI = ref([]);
const sourcesAI = ref([]);
const typeActionsAI = ref([]);
const responsables = ref([]);
const suivis = ref([]);
const constats = ref([]);
const users = ref([]);
const totalActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(6); // Même valeur que dans votre backend
const lastPage = ref(1);
const tableRef = ref(null); // Référence vers le composant TableActions.vue

// Charger les types d'actions depuis l'API
const chargerActions = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/actions/auditinterne", {
            params: { page, search },
        });
        actionsAI.value = response.data.data;
        totalActions.value = response.data.total;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
    } catch (error) {
        toast.error(
            "Erreur lors du chargement de l'audit interne ou de l'action",
            error
        );
    }
};

const chargerSourcesAI = async () => {
    try {
        const response = await axios.get("/api/sourcesAI");
        sourcesAI.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des sources AI:", error);
    }
};

const chargerTypeActionsAI = async () => {
    try {
        const response = await axios.get("/api/typeactionsAI");
        typeActionsAI.value = response.data;
    } catch (error) {
        console.error(
            "Erreur lors du chargement des types d'actions AI:",
            error
        );
    }
};

const chargerResponsables = async () => {
    try {
        const response = await axios.get("/api/responsables");
        responsables.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des responsables:", error);
    }
};

const chargerSuivis = async () => {
    try {
        const response = await axios.get("/api/suivis");
        suivis.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des suivis:", error);
    }
};

const chargerConstats = async () => {
    try {
        const response = await axios.get("/api/constats");
        constats.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des constats:", error);
    }
};

const chargerUsers = async () => {
    try {
        const response = await axios.get("/api/users");
        users.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs:", error);
    }
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
        chargerActions(page, searchQuery.value);
    }
};

// Fonction pour gérer la recherche
const rechercherActions = () => {
    currentPage.value = 1; // Réinitialiser à la première page lors de la recherche
    chargerActions(currentPage.value, searchQuery.value);
};

// Fonction pour supprimer une action
const supprimerAction = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer cette action pour Audit Interne ?",
        async () => {
            try {
                await axios.delete(`/api/actions/${id}`);
                toast.success("Action Audit Interne supprimée avec succès");
                chargerActions(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression de l'action de l'Audit Interne",
                    error
                );
            }
        }
    );
};

// Données formatées pour afficher correctement la fréquence
const formattedActions = computed(() => {
    return actionsAI.value.map((action) => {
        // Créer une copie pour éviter de modifier les données originales
        const newAction = { ...action };

        // Reformater la date si elle existe
        if (newAction.date) {
            const [year, month, day] = newAction.date.split("-");
            newAction.date = `${day}/${month}/${year}`; // Reformater en jj-mm-yyyy
        }

        // Essayer de parser la fréquence JSON et extraire le type
        try {
            if (newAction.frequence) {
                const frequenceData =
                    typeof newAction.frequence === "string"
                        ? JSON.parse(newAction.frequence)
                        : newAction.frequence;

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
            }
        } catch (e) {
            // En cas d'erreur, garder la valeur originale
            console.error("Erreur lors du chargement de la fréquence:", e);
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

const voirAuditInterne = (id) => {
    router.push(`/admin/actions/auditinterne/voir/${id}`);
};

const editerAuditInterne = (id) => {
    router.push(`/admin/actions/auditinterne/editer/${id}`);
};
// Colonne pour la table
const columns = [
    { label: "N°", field: "num_actions" },
    { label: "Date", field: "date" },
    {
        label: "Action",
        field: "description",
        classes: "truncate", // Classes Tailwind à appliquer à cette colonne
        isExpandable: true, // Indique que cette colonne a un contenu extensible
        render: (row) => row.description, // Retourne directement la valeur
    },
    { label: "Constat", field: "constat_libelle" },
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
    { label: "Statut", field: "statut" },
    { label: "Ajouter par", field: "nom_utilisateur" },
];

// Actions pour la table
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirAuditInterne(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerAuditInterne(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerAction(row.id),
    },
];

const showExportMenu = ref(false); // État pour afficher ou masquer le menu exporter

// Fonction pour basculer l'affichage du menu
const toggleExportMenu = () => {
    showExportMenu.value = !showExportMenu.value;
};

// Fonction pour exporter en Excel
const exportToExcel = async () => {
    const selectedIds = tableRef.value?.selectedRows || [];

    // Filtrer les actions à exporter
    const selectedData = formattedActions.value.filter((action) =>
        selectedIds.includes(action.num_actions)
    );

    if (selectedData.length === 0) {
        toast.error("Veuillez sélectionner au moins une ligne.");
        return;
    }

    try {
        // Charger les données nécessaires depuis les API
        await Promise.all([
            chargerSourcesAI(),
            chargerTypeActionsAI(),
            chargerResponsables(),
            chargerSuivis(),
            chargerConstats(),
            chargerUsers(),
        ]);

        // Générer un fichier Excel avec les données chargées
        generateExcelFile(
            selectedData,
            sourcesAI.value,
            typeActionsAI.value,
            responsables.value,
            suivis.value,
            constats.value,
            users.value
        );

        showExportMenu.value = false;
    } catch (error) {
        console.error(
            "Erreur lors du chargement des données pour l'export:",
            error
        );
        toast.error("Une erreur s'est produite lors de l'exportation.");
    }
};

// Fonction pour exporter en PDF
const exportToPdf = () => {
    console.log("Exportation en PDF...");
    // Ajoutez ici la logique pour exporter en PDF
    showExportMenu.value = false; // Masquer le menu après l'action
};

// Fonction pour générer un fichier Excel à partir des données sélectionnées
const generateExcelFile = (
    rows,
    sourcesList = [],
    typeActionsList = [],
    responsablesList = [],
    suivisList = [],
    constatsList = [],
    usersList = []
) => {
    // Convertir en format compatible Excel
    const headers = [
        "N°",
        "Date",
        "Sources",
        "Type d'action",
        "Responsable",
        "Suivi",
        "Action",
        "Constat",
        "Frequence",
        "Statut",
        "Ajouté par",
    ];
    const data = rows.map((row) => [
        row.num_actions,
        row.date,
        row.source_libelle,
        row.type_action_libelle,
        row.responsable_libelle,
        row.suivi_nom,
        row.description,
        row.constat_libelle,
        row.frequenceWithDetails,
        row.statut,
        row.nom_utilisateur,
    ]);

    const worksheetData = [headers, ...data];

    const workbook = XLSX.utils.book_new();

    // Feuille principale
    const mainSheet = XLSX.utils.aoa_to_sheet(worksheetData);
    XLSX.utils.book_append_sheet(workbook, mainSheet, "Audit Interne");

    // Feuille "Sources"
    if (sourcesList.length) {
        const sourcesData = [["ID", "Libellé"]];
        sourcesList.forEach((src) => {
            sourcesData.push([src.id, src.libelle]);
        });
        const sourcesSheet = XLSX.utils.aoa_to_sheet(sourcesData);
        XLSX.utils.book_append_sheet(workbook, sourcesSheet, "Sources");
    }

    // Feuille "Types d'Actions"
    if (typeActionsList.length) {
        const typeActionsData = [["ID", "Libellé"]];
        typeActionsList.forEach((type) => {
            typeActionsData.push([type.id, type.libelle]);
        });
        const typeActionsSheet = XLSX.utils.aoa_to_sheet(typeActionsData);
        XLSX.utils.book_append_sheet(
            workbook,
            typeActionsSheet,
            "Types d'Actions"
        );
    }

    // Feuille "Responsables"
    if (responsablesList.length) {
        const responsablesData = [["ID", "Libellé"]];
        responsablesList.forEach((resp) => {
            responsablesData.push([resp.id, resp.libelle]);
        });
        const responsablesSheet = XLSX.utils.aoa_to_sheet(responsablesData);
        XLSX.utils.book_append_sheet(
            workbook,
            responsablesSheet,
            "Responsables"
        );
    }

    // Feuille "Suivis"
    if (suivisList.length) {
        const suivisData = [["ID", "Nom"]];
        suivisList.forEach((suivi) => {
            suivisData.push([suivi.id, suivi.nom]);
        });
        const suivisSheet = XLSX.utils.aoa_to_sheet(suivisData);
        XLSX.utils.book_append_sheet(workbook, suivisSheet, "Suivis");
    }

    // Feuille "Constats"
    if (constatsList.length) {
        const constatsData = [["ID", "Libellé"]];
        constatsList.forEach((constat) => {
            constatsData.push([constat.id, constat.libelle]);
        });
        const constatsSheet = XLSX.utils.aoa_to_sheet(constatsData);
        XLSX.utils.book_append_sheet(workbook, constatsSheet, "Constats");
    }

    // Feuille "Utilisateurs"
    if (usersList.length) {
        const usersData = [["ID", "Nom"]];
        usersList.forEach((user) => {
            usersData.push([user.id, user.nom_utilisateur]);
        });
        const usersSheet = XLSX.utils.aoa_to_sheet(usersData);
        XLSX.utils.book_append_sheet(workbook, usersSheet, "Utilisateurs");
    }

    // Générer le fichier Excel
    XLSX.writeFile(workbook, "Audit_Interne.xlsx");
};

// Fonction pour charger les actions au démarrage
onMounted(() => {
    chargerActions(currentPage.value, searchQuery.value);
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
                        Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace actions, vous pourriez voir et gérer les
                        informations sur les actions, que ce soit Audit Interne
                        ou PTA, d'y ajouter et encore plus.
                    </p>
                </div>

                <!-- Choix d'action à faire(A.I ou PTA) -->
                <div class="flex w-full mt-5 ml-4 justify-center space-x-4">
                    <!-- Lien Audit Interne -->
                    <router-link
                        to="/admin/actions/auditinterne"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/admin/actions/auditinterne',
                        }"
                    >
                        Audit Interne
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien PTA -->
                    <router-link
                        to="/admin/actions/pta"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/admin/actions/pta',
                        }"
                    >
                        PTA
                    </router-link>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Bouton d'ajout -->
                    <router-link to="/admin/actions/auditinterne/ajouter">
                        <button
                            class="flex items-center justify-center bg-[#0062ff] text-white px-4 py-2 rounded-md w-38"
                        >
                            <Plus class="w-5 h-5 mr-2" /> Ajouter
                        </button></router-link
                    >

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
                    <button
                        class="flex items-center justify-center border border-gray-400 ml-4 transparent text-black px-4 py-2 rounded-md w-38"
                    >
                        Importer
                    </button>
                    <div class="relative">
                        <button
                            @click="toggleExportMenu"
                            class="flex items-center justify-center border border-gray-400 ml-4 text-black px-4 py-2 rounded-md w-38"
                        >
                            Exporter
                        </button>

                        <div
                            v-if="showExportMenu"
                            class="absolute mt-2 right-0 bg-white border border-gray-300 rounded-md shadow-lg w-40"
                        >
                            <button
                                @click="exportToExcel"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-green-600"
                            >
                                Exporter en Excel
                            </button>
                            <button
                                @click="exportToPdf"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600"
                            >
                                Exporter en PDF
                            </button>
                        </div>
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
                        <strong>{{ (currentPage - 1) * perPage + 1 }}</strong>
                        <span>à</span>
                        <strong>
                            {{ Math.min(currentPage * perPage, totalActions) }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalActions }}</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-2">
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage === 1"
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
                            :disabled="currentPage >= lastPage"
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
