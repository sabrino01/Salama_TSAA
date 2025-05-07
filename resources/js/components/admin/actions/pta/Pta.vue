<script>
export const ptaData = ref([
    {
        sources: "PTA",
        dns: "Planifier les réunions",
        datesuivi: "15/11/2024",
    },
]);
</script>

<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/TableActions.vue";
import ExcelJS from "exceljs";
import { saveAs } from "file-saver";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";
import { frequenceOptions } from "../../../../utils/frequenceOptions";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import jsPDF from "jspdf";
import "jspdf-autotable"; // Pour ajouter des tableaux au PDF

const router = useRouter();
const actionsPTA = ref([]);
const sourcesPTA = ref([]);
const typeActionsPTA = ref([]);
const responsables = ref([]);
const suivis = ref([]);
const constats = ref([]);
const users = ref([]);
const totalActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);
const tableRef = ref(null); // Référence vers le composant TableActions.vue

// Charger les types d'actions depuis l'API
const chargerActions = async (page = 1, search = "") => {
    try {
        const response = await axios.get("/api/actions/pta", {
            params: { page, search },
        });
        actionsPTA.value = response.data.data;
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

const chargerSourcesPTA = async () => {
    try {
        const response = await axios.get("/api/sourcesPTA");
        sourcesPTA.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des sources PTA:", error);
    }
};

const chargerTypeActionsPTA = async () => {
    try {
        const response = await axios.get("/api/typeactionsPTA");
        typeActionsPTA.value = response.data;
    } catch (error) {
        console.error(
            "Erreur lors du chargement des types d'actions PTA:",
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
        "Êtes-vous sûr de vouloir supprimer cette action pour PTA ?",
        async () => {
            try {
                await axios.delete(`/api/actions/${id}`);
                toast.success("Action PTA supprimée avec succès");
                chargerActions(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression de l'action du PTA",
                    error
                );
            }
        }
    );
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
    return actionsPTA.value.map((action) => {
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

const voirPTA = (id) => {
    router.push(`/admin/actions/pta/voir/${id}`);
};

const voirPTAOther = (id) => {
    router.push(`/admin/actions/pta/voir/other/${id}`);
};

const editerPTA = (id) => {
    router.push(`/admin/actions/pta/editer/${id}`);
};
// Colonne pour la table
const columns = [
    { label: "N°", field: "num_actions" },
    { label: "Date", field: "date" },
    {
        label: "DNS",
        field: "description",
        classes: "truncate", // Classes Tailwind à appliquer à cette colonne
        isExpandable: true, // Indique que cette colonne a un contenu extensible
        render: (row) => row.description, // Retourne directement la valeur
    },
    { label: "Action", field: "constat_libelle" },
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
        handler: (row) => voirPTA(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerPTA(row.id),
    },
    {
        label: "Supprimer",
        class: "text-red-500",
        handler: (row) => supprimerAction(row.id),
    },
];

// Fonction pour filtrer les actions dynamiquement
const filterActions = (row, actions) => {
    // Récupérer les données de l'utilisateur depuis le localStorage
    const user = JSON.parse(localStorage.getItem("user"));
    const utilisateurConnecte = user ? user.nom_utilisateur : null;

    // Vérifier si l'utilisateur connecté correspond à celui qui a ajouté l'action
    if (row.nom_utilisateur === utilisateurConnecte) {
        // Afficher toutes les options si c'est l'utilisateur connecté
        return actions;
    } else {
        // Sinon, afficher uniquement l'option "Voir"
        return actions
            .filter((action) => action.label === "Voir")
            .map((action) => ({
                ...action,
                handler: () => voirPTAOther(row.id), // Remplacer le handler
            }));
    }
};

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
            chargerSourcesPTA(),
            chargerTypeActionsPTA(),
            chargerResponsables(),
            chargerSuivis(),
            chargerConstats(),
            chargerUsers(),
        ]);

        // Générer un fichier Excel avec les données chargées
        generateExcelFile(
            selectedData,
            sourcesPTA.value,
            typeActionsPTA.value,
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
        // Si vous avez besoin de données supplémentaires depuis l'API
        // const additionalData = await axios.post(
        //     "/api/actions/auditinterne/details",
        //     { ids: selectedIds }
        // );

        // Fusionner les données supplémentaires avec les données sélectionnées
        const dataToExport = selectedData.map((action) => {
            // const additionalInfo = additionalData.data.find(
            //     (item) => item.num_actions === action.num_actions
            // );
            return {
                ...action, //...additionalInfo
            };
        });

        // Initialiser le PDF
        const doc = new jsPDF();

        // Ajouter un titre
        doc.setFontSize(18);
        doc.text("Actions PTA", 14, 20);

        // Ajouter une table avec les données
        const tableData = dataToExport.map((action) => [
            action.num_actions,
            action.date,
            action.description,
            action.frequenceWithDetails || action.frequence,
            action.statut,
            action.nom_utilisateur,
        ]);

        doc.autoTable({
            head: [
                [
                    "N°",
                    "Date",
                    "Description",
                    "Fréquence",
                    "Statut",
                    "Ajouté par",
                ],
            ],
            body: tableData,
            startY: 30,
        });

        // Sauvegarder le PDF
        doc.save("PTA.pdf");

        toast.success("PDF exporté avec succès !");
        showExportMenu.value = false; // Masquer le menu après l'action
    } catch (error) {
        console.error("Erreur lors de l'exportation en PDF :", error);
        toast.error("Une erreur s'est produite lors de l'exportation.");
    }
};

// Fonction pour extraire le numéro d'un num_actions "PTA-0004" => 4
const extractNumber = (str) => {
    const match = str.match(/PTA-(\d+)/);
    return match ? parseInt(match[1], 10) : 0;
};

// Fonction pour générer un fichier Excel à partir des données sélectionnées
const generateExcelFile = async (
    rows,
    sourcesList = [],
    typeActionsList = [],
    responsablesList = [],
    suivisList = [],
    constatsList = [],
    usersList = []
) => {
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet("PTA");

    // Définir les colonnes
    sheet.columns = [
        { header: "N°", key: "num_actions", width: 15 },
        { header: "Date", key: "date", width: 15 },
        { header: "Sources", key: "source", width: 20 },
        { header: "Types d'action", key: "type_action", width: 20 },
        { header: "Responsables", key: "responsable", width: 20 },
        { header: "Suivis", key: "suivi", width: 15 },
        { header: "DNS", key: "description", width: 30 },
        { header: "Action", key: "constat_libelle", width: 20 },
        { header: "Fréquences", key: "frequenceWithDetails", width: 15 },
        { header: "Mesures", key: "mesure", width: 20 },
        { header: "Statuts", key: "statut", width: 15 },
        { header: "Ajouté par", key: "nom_utilisateur", width: 20 },
        { header: "Observations", key: "observation", width: 30 },
    ];

    // Ajouter les données existantes
    rows.forEach((row) => {
        sheet.addRow({
            num_actions: row.num_actions,
            date: row.date,
            source: row.source_libelle,
            type_action: row.type_action_libelle,
            responsable: row.responsable_libelle,
            suivi: row.suivi_nom,
            description: row.description,
            constat_libelle: row.constat_libelle,
            frequenceWithDetails: row.frequenceWithDetails,
            mesure: row.mesure,
            statut: row.statut,
            nom_utilisateur: row.nom_utilisateur,
            observation: row.observation,
        });
    });

    // Trouver le dernier numéro utilisé
    const lastNum = Math.max(
        ...rows.map((row) => extractNumber(row.num_actions))
    );
    let currentNum = lastNum;

    // Définir le début de la ligne pour les formules
    const startRow = rows.length + 2; // 1 = header, +1 pour première ligne libre

    // Définir le nombre de lignes supplémentaires à ajouter (ajustable)
    const additionalRows = 500; // Vous pouvez ajuster selon vos besoins

    // Ajouter des lignes vides avec des formules pour l'auto-incrémentation
    for (let i = 0; i < additionalRows; i++) {
        const row = sheet.addRow({});
        const rowIndex = startRow + i;

        // Ajouter la formule pour la colonne A (num_actions)
        if (i === 0) {
            // Première ligne vide - basée sur le dernier numéro connu
            sheet.getCell(`A${rowIndex}`).value = {
                formula: `IF(B${rowIndex}<>"", CONCATENATE("PTA-", TEXT(${
                    currentNum + 1
                }, "0000")), "")`,
            };
        } else {
            // Lignes suivantes - basées sur la ligne précédente
            sheet.getCell(`A${rowIndex}`).value = {
                formula: `IF(B${rowIndex}<>"", IF(B${
                    rowIndex - 1
                }<>"", CONCATENATE("PTA-", TEXT(VALUE(MID(A${
                    rowIndex - 1
                },4,4))+1, "0000")), CONCATENATE("PTA-", TEXT(${
                    currentNum + 1
                }, "0000"))), "")`,
            };
        }
    }

    // Définir les options pour la liste déroulante des statuts
    const statutOptions = ["En cours", "En retard", "Clôturé", "Abandonné"];

    // Appliquer un style aux cellules pour les rendre plus lisibles
    sheet.getRow(1).font = { bold: true };
    sheet.getRow(1).alignment = { vertical: "middle", horizontal: "center" };

    // Appliquer les listes déroulantes (data validations)
    const addDropdown = (col, list) => {
        // Déterminer la plage pour les validations (commencer après les données existantes)
        const validationStartRow = 2; // Première ligne après les en-têtes
        const validationEndRow = startRow + additionalRows - 1;

        // Appliquer la validation à la plage
        for (let i = validationStartRow; i <= validationEndRow; i++) {
            sheet.getCell(`${col}${i}`).dataValidation = {
                type: "list",
                allowBlank: true,
                formulae: [`"${list.join(",")}"`],
                showDropDown: true,
            };
        }
    };

    // Appliquer les listes déroulantes aux colonnes correspondantes
    if (sourcesList.length > 0) {
        addDropdown(
            "C",
            sourcesList.map((s) => s.libelle)
        ); // Sources
    }

    if (typeActionsList.length > 0) {
        addDropdown(
            "D",
            typeActionsList.map((t) => t.libelle)
        ); // Types d'action
    }

    if (responsablesList.length > 0) {
        addDropdown(
            "E",
            responsablesList.map((r) => r.libelle)
        ); // Responsables
    }

    if (suivisList.length > 0) {
        addDropdown(
            "F",
            suivisList.map((s) => s.nom)
        ); // Suivis
    }

    if (constatsList.length > 0) {
        addDropdown(
            "H",
            constatsList.map((c) => c.libelle)
        ); // Constats
    }

    if (usersList.length > 0) {
        addDropdown(
            "L",
            usersList.map((u) => u.nom_utilisateur)
        ); // Utilisateurs
    }

    // Appliquer la liste déroulante à la colonne "Fréquences" (colonne I)
    if (
        typeof frequenceOptions !== "undefined" &&
        frequenceOptions.length > 0
    ) {
        addDropdown("I", frequenceOptions);
    }

    // Appliquer la liste déroulante à la colonne "Statuts" (colonne K)
    addDropdown("K", statutOptions);

    // Activer les filtres pour faciliter la navigation
    sheet.autoFilter = {
        from: { row: 1, column: 1 },
        to: { row: 1, column: 13 },
    };

    // Générer le fichier
    const buffer = await workbook.xlsx.writeBuffer();
    saveAs(new Blob([buffer]), "PTA.xlsx");
};

// Fonction pour importer un fichier Excel
const importerFichier = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        toast.error("Veuillez sélectionner un fichier Excel.");
        return;
    }

    try {
        const workbook = new ExcelJS.Workbook();
        const reader = new FileReader();

        reader.onload = async (e) => {
            const buffer = e.target.result;
            await workbook.xlsx.load(buffer);

            const sheet = workbook.worksheets[0]; // Utiliser la première feuille
            const importedData = [];

            // Parcourir les lignes du fichier Excel
            sheet.eachRow((row, rowIndex) => {
                if (rowIndex === 1) return; // Ignorer la première ligne (en-têtes)

                // Extraire les valeurs des cellules principales
                const numActions = row.getCell(1).value; // Colonne A (N°)
                const date = row.getCell(2).value; // Colonne B (Date)
                const description = row.getCell(7).value; // Colonne G (Actions/Description)

                // Vérifier si la ligne contient des données réelles
                // Une ligne est considérée comme valide si elle a au moins une date ET soit un numActions, soit une description
                const hasDate = date != null && date !== "";
                const hasContent =
                    (numActions != null && numActions !== "") ||
                    (description != null && description !== "");

                if (!hasDate || !hasContent) {
                    // Ignorer les lignes sans données significatives
                    return;
                }

                // Reformater la date au format yyyy-mm-dd
                let formattedDate = null;
                if (typeof date === "string" && date.includes("/")) {
                    // Si le format est dd/mm/yyyy
                    const [day, month, year] = date.split("/");
                    formattedDate = `${year}-${month}-${day}`;
                } else if (date instanceof Date) {
                    // Si le format est un objet Date
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    formattedDate = `${year}-${month}-${day}`;
                }

                // Vérifier si la cellule contient une formule
                const numActionsValue =
                    typeof numActions === "object" && numActions.formula
                        ? numActions.result // Utiliser le résultat calculé
                        : numActions; // Sinon, utiliser la valeur brute

                // Extraire les autres valeurs
                const sourceLibelle = row.getCell(3).value;
                const typeActionLibelle = row.getCell(4).value;
                const responsableLibelle = row.getCell(5).value;
                const suiviNom = row.getCell(6).value;
                const constatLibelle = row.getCell(8).value;
                const frequence = row.getCell(9).value;
                const mesure = row.getCell(10).value;
                const statut = row.getCell(11).value;
                const nomUtilisateur = row.getCell(12).value;
                const observation = row.getCell(13).value;

                importedData.push({
                    num_actions: numActionsValue,
                    date: formattedDate,
                    source_libelle: sourceLibelle,
                    type_action_libelle: typeActionLibelle,
                    responsable_libelle: responsableLibelle,
                    suivi_nom: suiviNom,
                    description: description,
                    constat_libelle: constatLibelle,
                    frequence: frequence,
                    mesure: mesure,
                    statut: statut,
                    nom_utilisateur: nomUtilisateur,
                    observation: observation,
                });
            });

            // Vérifier si des données ont été importées
            console.log("Données importées:", importedData);
            if (importedData.length === 0) {
                toast.error(
                    "Aucune donnée valide n'a été trouvée dans le fichier."
                );
                return;
            }

            // Extraire les num_actions pour vérification
            const numActionsToCheck = importedData
                .filter((data) => data.num_actions) // Filtrer ceux qui ont un num_actions
                .map((data) => data.num_actions);

            if (numActionsToCheck.length === 0) {
                toast.error(
                    "Aucun numéro d'action valide n'a été trouvé dans le fichier."
                );
                return;
            }

            // Vérifier les num_actions existants dans la base de données
            const response = await axios.post(
                "/api/actions/check-num-actions",
                {
                    num_actions: numActionsToCheck,
                }
            );

            const existingNumActions = response.data;

            // Filtrer les données pour ne conserver que les nouvelles
            const newData = importedData.filter(
                (data) => !existingNumActions.includes(data.num_actions)
            );

            if (newData.length === 0) {
                toast.error(
                    "Toutes les données existent déjà dans la base de données."
                );
                return;
            }

            console.log("Nouvelles données à importer :", newData);

            // Envoyer les nouvelles données au backend
            await axios.post("/api/actions/import", {
                data: newData,
            });

            toast.success(
                `${newData.length} action(s) importée(s) avec succès !`
            );

            // Actualiser les données de la table
            await chargerActions(currentPage.value, searchQuery.value);
        };

        reader.readAsArrayBuffer(file);
    } catch (error) {
        console.error("Erreur lors de l'importation du fichier Excel:", error);
        toast.error("Une erreur s'est produite lors de l'importation.");
    }
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
                    <router-link to="/admin/actions/pta/ajouter">
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
                    <div class="relative">
                        <label
                            for="importer"
                            class="flex items-center justify-center border border-gray-400 ml-4 text-black px-4 py-2 rounded-md w-38 cursor-pointer"
                        >
                            Importer
                        </label>
                        <input
                            id="importer"
                            type="file"
                            accept=".xlsx, .xls"
                            @change="importerFichier"
                            class="hidden"
                        />
                    </div>
                    <div class="relative">
                        <!-- Bouton Exporter -->
                        <button
                            @click="toggleExportMenu"
                            class="flex items-center justify-center border border-gray-400 ml-4 text-black px-4 py-2 rounded-md w-38"
                        >
                            Exporter
                        </button>

                        <!-- Menu déroulant pour les options d'exportation -->
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

                <!-- Tableau PTA -->
                <div class="mt-5 ml-4">
                    <Table
                        ref="tableRef"
                        :columns="columns"
                        :data="formattedActions"
                        :actions="actions"
                        :filterActions="filterActions"
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
