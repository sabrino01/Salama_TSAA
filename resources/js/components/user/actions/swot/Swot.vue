<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
import Table from "../../../assets/TableActions.vue";
import ExcelJS from "exceljs";
import { saveAs } from "file-saver";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";
import { frequenceOptions } from "../../../../utils/frequenceOptions";
import { useStatusManager } from "../../../../utils/usesStatusManager";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import jsPDF from "jspdf";
import "jspdf-autotable"; // Pour ajouter des tableaux au PDF

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();
const actionsSWOT = ref([]);
const sourcesSWOT = ref([]);
const typeActionsSWOT = ref([]);
const responsables = ref([]);
const suivis = ref([]);
const constatsSWOT = ref([]);
const users = ref([]);
const totalActions = ref(0);
const currentPage = ref(1);
const searchQuery = ref("");
const perPage = ref(10); // Même valeur que dans votre backend
const lastPage = ref(1);
const tableRef = ref(null); // Référence vers le composant TableActions.vue
// Ajoutez cette nouvelle référence pour suivre l'état de la pagination
const paginationEnabled = ref(true);
const allActions = ref([]); // Pour stocker toutes les actions quand la pagination est désactivée

const { checkAllActionsStatus } = useStatusManager();

// Modifiez la fonction chargerActions pour gérer les deux cas
const chargerActions = async (page = 1, search = "") => {
    try {
        if (paginationEnabled.value) {
            // Comportement original avec pagination
            const response = await axios.get("/api/actions/swot", {
                params: { page, search },
            });
            actionsSWOT.value = response.data.data;
            totalActions.value = Number(response.data.total) || 0;
            currentPage.value = Number(response.data.current_page) || 1;
            lastPage.value = Number(response.data.last_page) || 1;
            perPage.value = Number(response.data.per_page) || 10;
        } else {
            // Charger toutes les données sans pagination
            const response = await axios.get("/api/actions/swot", {
                params: { page: 1, search, per_page: 1000000 }, // Charger toutes les données
            });

            allActions.value = response.data.data || response.data;
            actionsSWOT.value = allActions.value;
            totalActions.value = allActions.value.length;
            currentPage.value = 1;
            lastPage.value = 1;
        }
    } catch (error) {
        toast.error("Erreur lors du chargement du SWOT ou de l'action", error);
    }
};

const verifierTousLesStatuts = async () => {
    const result = await checkAllActionsStatus();

    // Stockage des résultats
    actionsSWOT.value.statut = result;

    if (result.success) {
        // toast.success(
        //     `${result.updated_count} actions mises à jour sur ${result.total_checked}`
        // );
        // Recharger la liste pour voir les changements
        await chargerActions(currentPage.value, searchQuery.value);
    } else {
        toast.error(result.message);
    }
};

// Ajoutez cette fonction pour basculer l'état de la pagination
const togglePagination = () => {
    paginationEnabled.value = !paginationEnabled.value;
    // Rechargez les données avec la nouvelle configuration
    chargerActions(1, searchQuery.value);
};

const chargerSourcesSWOT = async () => {
    try {
        const response = await axios.get("/api/sourcesSWOT");
        sourcesSWOT.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des sources SWOT:", error);
    }
};

const chargerTypeActionsSWOT = async () => {
    try {
        const response = await axios.get("/api/typeactionsSWOT");
        typeActionsSWOT.value = response.data;
    } catch (error) {
        console.error(
            "Erreur lors du chargement des types d'actions SWOT:",
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

const chargerConstatsSWOT = async () => {
    try {
        const response = await axios.get("/api/constatsSWOT");
        constatsSWOT.value = response.data;
    } catch (error) {
        console.error("Erreur lors du chargement des constats SWOT:", error);
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
    chargerActions(currentPage.value, search);
};

// Fonction pour supprimer une action
const supprimerAction = async (id) => {
    toast.confirm(
        "Êtes-vous sûr de vouloir supprimer cette action pour SWOT ?",
        async () => {
            try {
                await axios.delete(`/api/actions/${id}`);
                toast.success("Action SWOT supprimée avec succès");
                chargerActions(currentPage.value, searchQuery.value);
            } catch (error) {
                toast.error(
                    "Erreur lors de la suppression de l'action du SWOT",
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
    return actionsSWOT.value.map((action) => {
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

        // Décomposer observation_par_suivi (afficher les dates séparées par des virgules)
        try {
            if (newAction.observation_par_suivi) {
                const normalizedObs = normalizeJsonString(
                    newAction.observation_par_suivi
                );
                let obsArray = [];

                if (Array.isArray(normalizedObs)) {
                    obsArray = normalizedObs;
                } else if (
                    typeof normalizedObs === "string" &&
                    normalizedObs.trim().startsWith("[")
                ) {
                    obsArray = JSON.parse(normalizedObs);
                }

                // Extraire et reformater les dates + observations
                newAction.observationDates = obsArray
                    .map((obs) => {
                        if (obs.date) {
                            // Support "2025-05-26 12:01:16" ou "2025-05-26T12:01:16"
                            const [datePart] = obs.date.split(/[ T]/);
                            const [year, month, day] = datePart.split("-");
                            const dateStr = `${day}/${month}/${year}`;
                            // Ajoute l'observation à côté de la date
                            if (obs.observation) {
                                return `${dateStr}: ${obs.observation}`;
                            }
                            return dateStr;
                        }
                        return null;
                    })
                    .filter(Boolean)
                    .join("\n");
            } else {
                newAction.observationDates = "—";
            }
        } catch (e) {
            newAction.observationDates = "—";
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

const voirSWOT = (id) => {
    router.push(`/user/actions/swot/voir/${id}`);
};

const voirSWOTOther = (id) => {
    router.push(`/user/actions/swot/voir/other/${id}`);
};

const editerSWOT = (id) => {
    router.push(`/user/actions/swot/editer/${id}`);
};

// Colonne pour la table
const columns = [
    { label: "N°", field: "num_actions" },
    { label: "Date", field: "date" },
    {
        label: "Description non conformité",
        field: "description",
        cellClass: "custom-description-cell", // Classe personnalisée pour le style
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
    {
        label: "Responsables",
        field: "responsables_libelle",
        cellClass: "custom-description-cell", // Classe personnalisée pour le style
        render: (row) => row.responsables_libelle || "—",
    },
    {
        label: "Suivis",
        field: "suivis_noms",
        cellClass: "custom-description-cell", // Classe personnalisée pour le style
        render: (row) => row.suivis_noms || "—",
    },
    {
        label: "Date suivis",
        field: "observation_par_suivi",
        classes: "truncate", // Classes Tailwind à appliquer à cette colonne
        isExpandable: true, // Indique que cette colonne a un contenu extensible
        render: (row) => row.observationDates || "—",
    },
    { label: "Statut", field: "statut" },
    { label: "Ajouter par", field: "nom_utilisateur" },
];

// Actions pour la table
const actions = [
    {
        label: "Voir",
        class: "text-blue-500",
        handler: (row) => voirSWOT(row.id),
    },
    {
        label: "Editer",
        class: "text-green-500",
        handler: (row) => editerSWOT(row.id),
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
                handler: () => voirSWOTOther(row.id), // Remplacer le handler
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
            chargerSourcesSWOT(),
            chargerTypeActionsSWOT(),
            chargerResponsables(),
            chargerSuivis(),
            chargerConstatsSWOT(),
            chargerUsers(),
        ]);

        // Générer un fichier Excel avec les données chargées
        generateExcelFile(
            selectedData,
            sourcesSWOT.value,
            typeActionsSWOT.value,
            responsables.value,
            suivis.value,
            constatsSWOT.value,
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
// Fonction pour convertir une image en base64
const getImageAsBase64 = (imagePath) => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            canvas.width = this.naturalWidth;
            canvas.height = this.naturalHeight;
            ctx.drawImage(this, 0, 0);
            resolve(canvas.toDataURL("image/png"));
        };
        img.onerror = reject;
        img.src = imagePath;
    });
};

// Définir la hauteur exacte des images header/footer
const HEADER_HEIGHT = 30; // hauteur de l'image header
const HEADER_Y = 10; // position Y du header
const FOOTER_HEIGHT = 20; // hauteur de l'image footer
const FOOTER_Y_OFFSET = 30; // distance du bas de page pour le footer

// Fonction pour ajouter le header avec l'image
const addHeaderImages = async (doc, pageWidth) => {
    try {
        const salamaHeaderImg = await getImageAsBase64("/image/salama.png");
        doc.addImage(salamaHeaderImg, "PNG", 10, HEADER_Y, 40, HEADER_HEIGHT);
    } catch (error) {
        console.warn("Erreur lors du chargement de l'image du header:", error);
    }
};

// Fonction pour ajouter le footer avec les images
const addFooterImages = async (doc, pageWidth, pageHeight) => {
    try {
        const salamaFooter01 = await getImageAsBase64(
            "/image/capture salama 01.png"
        );
        const salamaFooter02 = await getImageAsBase64(
            "/image/capture salama 02.png"
        );
        const footerY = pageHeight - FOOTER_Y_OFFSET;
        doc.addImage(salamaFooter01, "PNG", 10, footerY, 50, FOOTER_HEIGHT);
        doc.addImage(
            salamaFooter02,
            "PNG",
            pageWidth - 60,
            footerY,
            50,
            FOOTER_HEIGHT
        );
    } catch (error) {
        console.warn("Erreur lors du chargement des images du footer:", error);
    }
};

// Fonction utilitaire pour découper le texte selon la largeur disponible
const splitTextToFitWidth = (doc, text, maxWidth) => {
    if (!text) return [];

    // Découpe d'abord sur les retours à la ligne
    const paragraphs = text.split("\n");
    const lines = [];

    for (let para of paragraphs) {
        const words = para.split(" ");
        let currentLine = "";

        for (let word of words) {
            const testLine = currentLine ? currentLine + " " + word : word;
            const testWidth =
                (doc.getStringUnitWidth(testLine) *
                    doc.internal.getFontSize()) /
                doc.internal.scaleFactor;

            if (testWidth <= maxWidth) {
                currentLine = testLine;
            } else {
                if (currentLine) {
                    lines.push(currentLine);
                    currentLine = word;
                } else {
                    // Si un seul mot est trop long, on le force quand même
                    lines.push(word);
                }
            }
        }
        if (currentLine) {
            lines.push(currentLine);
        }
    }

    return lines;
};

// Fonction améliorée pour ajouter des données avec label sur une ligne et valeur sur la ligne suivante
const addDataWithSeparateLines = (
    doc,
    label,
    value,
    currentY,
    leftMargin,
    availableWidth,
    indentValue = 0
) => {
    // Afficher le label en gras
    doc.setFont(undefined, "bold");
    doc.text(`${label}`, leftMargin, currentY);
    currentY += 6; // Passer à la ligne suivante

    // Afficher la valeur en normal avec indentation optionnelle
    doc.setFont(undefined, "normal");

    // Découper le texte de la valeur si nécessaire
    const valueLines = splitTextToFitWidth(
        doc,
        value || "N/A",
        availableWidth - indentValue
    );

    // Afficher toutes les lignes de la valeur avec indentation
    valueLines.forEach((line) => {
        doc.text(line, leftMargin + indentValue, currentY);
        currentY += 5; // Espacement entre les lignes de valeur
    });

    currentY += 2; // Espace après la valeur avant le prochain élément

    return currentY;
};

// Fonction pour vérifier si le contenu peut tenir dans l'espace restant
const checkSpaceAndAddPage = async (
    doc,
    currentY,
    requiredSpace,
    pageWidth,
    pageHeight,
    contentEndY
) => {
    // CORRECTION: Vérifier que currentY + requiredSpace ne dépasse pas contentEndY
    // et laisser un espace de sécurité de 5mm avant le footer
    if (currentY + requiredSpace > contentEndY - 5) {
        doc.addPage();
        await addHeaderImages(doc, pageWidth);
        await addFooterImages(doc, pageWidth, pageHeight);
        // Retourner la position Y de début de contenu (juste sous le header)
        return HEADER_Y + HEADER_HEIGHT + 10;
    }
    return currentY;
};

// Fonction pour calculer l'espace de contenu disponible
const getContentBounds = (pageHeight) => {
    const contentStartY = HEADER_Y + HEADER_HEIGHT + 10; // 10mm d'espace après le header
    const contentEndY = pageHeight - FOOTER_Y_OFFSET - FOOTER_HEIGHT - 5; // 5mm d'espace avant le footer
    return { contentStartY, contentEndY };
};

// Fonction pour créer le contenu de chaque action
const createActionContent = async (
    doc,
    action,
    startY,
    pageWidth,
    pageHeight,
    contentEndY
) => {
    let currentY = startY;
    const leftMargin = 20;
    const rightMargin = pageWidth - 20;
    const contentWidth = rightMargin - leftMargin;

    // Titre de l'action avec "Ajouté par" à droite
    doc.setFontSize(16);
    doc.setFont(undefined, "bold");
    doc.text(`N° ${action.num_actions}`, leftMargin, currentY);

    // "Ajouté par" à droite du numéro d'action - EN UPPERCASE
    doc.setFontSize(11);
    doc.setFont(undefined, "normal");
    const ajouteParText = `Ajouté par: ${(
        action.nom_utilisateur || "N/A"
    ).toUpperCase()}`;
    const ajouteParWidth =
        (doc.getStringUnitWidth(ajouteParText) * doc.internal.getFontSize()) /
        doc.internal.scaleFactor;
    doc.text(ajouteParText, rightMargin - ajouteParWidth, currentY);

    currentY += 8;

    // Ligne de séparation
    doc.setDrawColor(0, 0, 0);
    doc.line(leftMargin, currentY, rightMargin, currentY);
    currentY += 10;

    doc.setFontSize(11);
    doc.setFont(undefined, "normal");

    // Vérifier l'espace avant d'ajouter les informations de base
    currentY = await checkSpaceAndAddPage(
        doc,
        currentY,
        60,
        pageWidth,
        pageHeight,
        contentEndY
    );

    // Informations de base avec labels et valeurs sur des lignes séparées
    currentY = addDataWithSeparateLines(
        doc,
        "Date:",
        action.date,
        currentY,
        leftMargin,
        contentWidth
    );

    // Vérifier l'espace après chaque section importante
    currentY = await checkSpaceAndAddPage(
        doc,
        currentY,
        15,
        pageWidth,
        pageHeight,
        contentEndY
    );

    currentY = addDataWithSeparateLines(
        doc,
        "Type d'action:",
        action.type_action_libelle,
        currentY,
        leftMargin,
        contentWidth
    );

    currentY = await checkSpaceAndAddPage(
        doc,
        currentY,
        15,
        pageWidth,
        pageHeight,
        contentEndY
    );

    currentY = addDataWithSeparateLines(
        doc,
        "Statut:",
        action.statut,
        currentY,
        leftMargin,
        contentWidth
    );

    currentY = await checkSpaceAndAddPage(
        doc,
        currentY,
        15,
        pageWidth,
        pageHeight,
        contentEndY
    );

    currentY = addDataWithSeparateLines(
        doc,
        "Responsables:",
        action.responsables_libelle,
        currentY,
        leftMargin,
        contentWidth
    );

    // Description avec gestion améliorée
    if (action.description) {
        // Calculer l'espace nécessaire pour la description
        const descriptionLines = splitTextToFitWidth(
            doc,
            action.description,
            contentWidth - 5
        );
        const requiredSpace = 8 + descriptionLines.length * 5 + 5; // label + lignes + espacement

        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            requiredSpace,
            pageWidth,
            pageHeight,
            contentEndY
        );

        currentY = addDataWithSeparateLines(
            doc,
            "Description de la non conformité:",
            action.description,
            currentY,
            leftMargin,
            contentWidth,
            5
        );
    }

    // Description avec gestion améliorée
    if (action.action) {
        // Calculer l'espace nécessaire pour la description
        const descriptionLines = splitTextToFitWidth(
            doc,
            action.description,
            contentWidth - 5
        );
        const requiredSpace = 8 + descriptionLines.length * 5 + 5; // label + lignes + espacement

        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            requiredSpace,
            pageWidth,
            pageHeight,
            contentEndY
        );

        currentY = addDataWithSeparateLines(
            doc,
            "Actions:",
            action.action,
            currentY,
            leftMargin,
            contentWidth,
            5
        );
    }

    // Constat avec gestion améliorée
    if (action.constat_libelle) {
        const constatLines = splitTextToFitWidth(
            doc,
            action.constat_libelle,
            contentWidth - 5
        );
        const requiredSpace = 8 + constatLines.length * 5 + 5;

        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            requiredSpace,
            pageWidth,
            pageHeight,
            contentEndY
        );

        currentY = addDataWithSeparateLines(
            doc,
            "Constat:",
            action.constat_libelle,
            currentY,
            leftMargin,
            contentWidth,
            5
        );
    }

    // Autres informations avec vérification d'espace pour chacune
    if (action.source_libelle) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            20,
            pageWidth,
            pageHeight,
            contentEndY
        );
        currentY = addDataWithSeparateLines(
            doc,
            "Source:",
            action.source_libelle,
            currentY,
            leftMargin,
            contentWidth
        );
    }

    if (action.frequenceWithDetails || action.frequence) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            20,
            pageWidth,
            pageHeight,
            contentEndY
        );
        currentY = addDataWithSeparateLines(
            doc,
            "Fréquence:",
            action.frequenceWithDetails || action.frequence,
            currentY,
            leftMargin,
            contentWidth
        );
    }

    if (action.mesure) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            20,
            pageWidth,
            pageHeight,
            contentEndY
        );
        currentY = addDataWithSeparateLines(
            doc,
            "Livrable:",
            action.mesure,
            currentY,
            leftMargin,
            contentWidth
        );
    }

    if (action.suivis_noms) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            20,
            pageWidth,
            pageHeight,
            contentEndY
        );
        currentY = addDataWithSeparateLines(
            doc,
            "Suivis:",
            action.suivis_noms,
            currentY,
            leftMargin,
            contentWidth
        );
    }

    if (action.observation) {
        const observationLines = splitTextToFitWidth(
            doc,
            action.observation,
            contentWidth
        );
        const requiredSpace = 8 + observationLines.length * 5 + 5;

        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            requiredSpace,
            pageWidth,
            pageHeight,
            contentEndY
        );

        currentY = addDataWithSeparateLines(
            doc,
            "Observation:",
            action.observation,
            currentY,
            leftMargin,
            contentWidth
        );
    }

    // Mises à jour des responsables
    if (action.responsables_updates && action.responsables_updates.length > 0) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            30,
            pageWidth,
            pageHeight,
            contentEndY
        );

        doc.setFont(undefined, "bold");
        doc.text("Mises à jour des responsables:", leftMargin, currentY);
        currentY += 8;

        for (const update of action.responsables_updates) {
            currentY = await checkSpaceAndAddPage(
                doc,
                currentY,
                25,
                pageWidth,
                pageHeight,
                contentEndY
            );

            doc.setFont(undefined, "bold");
            doc.text(`${update.responsable_nom}:`, leftMargin + 5, currentY);
            currentY += 6;

            doc.setFont(undefined, "normal");
            if (update.statut_resp) {
                currentY = addDataWithSeparateLines(
                    doc,
                    "Statut:",
                    update.statut_resp,
                    currentY,
                    leftMargin + 10,
                    contentWidth - 20
                );
            }
            if (update.observation_resp) {
                currentY = addDataWithSeparateLines(
                    doc,
                    "Observation:",
                    update.observation_resp,
                    currentY,
                    leftMargin + 10,
                    contentWidth - 20
                );
            }
            currentY += 5;
        }
    }

    // Mises à jour des suivis
    if (action.suivis_updates && action.suivis_updates.length > 0) {
        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            30,
            pageWidth,
            pageHeight,
            contentEndY
        );

        doc.setFont(undefined, "bold");
        doc.text("Mises à jour des suivis:", leftMargin, currentY);
        currentY += 8;

        for (const update of action.suivis_updates) {
            currentY = await checkSpaceAndAddPage(
                doc,
                currentY,
                25,
                pageWidth,
                pageHeight,
                contentEndY
            );

            doc.setFont(undefined, "bold");
            doc.text(`${update.suivi_nom}:`, leftMargin + 5, currentY);
            currentY += 6;

            doc.setFont(undefined, "normal");
            if (update.statut_suivi) {
                currentY = addDataWithSeparateLines(
                    doc,
                    "Statut:",
                    update.statut_suivi,
                    currentY,
                    leftMargin + 10,
                    contentWidth - 20
                );
            }
            if (update.observation_suivi) {
                currentY = addDataWithSeparateLines(
                    doc,
                    "Observation:",
                    update.observation_suivi,
                    currentY,
                    leftMargin + 10,
                    contentWidth - 20
                );
            }
            currentY += 5;
        }
    }

    // Observation par suivis
    if (action.observationDates) {
        const observationDatesLines = splitTextToFitWidth(
            doc,
            action.observationDates,
            contentWidth - 5
        );
        const requiredSpace = 8 + observationDatesLines.length * 5 + 5;

        currentY = await checkSpaceAndAddPage(
            doc,
            currentY,
            requiredSpace,
            pageWidth,
            pageHeight,
            contentEndY
        );

        currentY = addDataWithSeparateLines(
            doc,
            "Observation par suivis:",
            action.observationDates,
            currentY,
            leftMargin,
            contentWidth,
            5
        );
    }

    return currentY;
};

// Fonction principale d'exportation en PDF
const exportToPdf = async () => {
    const selectedIds = tableRef.value?.selectedRows || [];
    const selectedData = formattedActions.value.filter((action) =>
        selectedIds.includes(action.num_actions)
    );

    if (selectedData.length === 0) {
        toast.error("Veuillez sélectionner au moins une ligne.");
        return;
    }

    try {
        const doc = new jsPDF({
            orientation: "portrait",
            unit: "mm",
            format: "a4",
        });

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // Calculer les limites de contenu avec la fonction dédiée
        const { contentStartY, contentEndY } = getContentBounds(pageHeight);

        let pageNumber = 1;
        const totalPages = selectedData.length;

        for (let i = 0; i < selectedData.length; i++) {
            const action = selectedData[i];

            if (i > 0) {
                doc.addPage();
                pageNumber++;
            }

            await addHeaderImages(doc, pageWidth);
            await addFooterImages(doc, pageWidth, pageHeight);

            await createActionContent(
                doc,
                action,
                contentStartY,
                pageWidth,
                pageHeight,
                contentEndY
            );

            // Numéro de page dans la zone du footer (mais pas sur les images)
            doc.setFontSize(9);
            doc.setFont(undefined, "normal");
            const pageText = `Page ${pageNumber} / ${totalPages}`;
            const pageTextWidth =
                (doc.getStringUnitWidth(pageText) * 9) /
                doc.internal.scaleFactor;

            // Centrer le numéro de page entre les deux images du footer
            const centerX = pageWidth / 2 - pageTextWidth / 2;
            doc.text(pageText, centerX, pageHeight - 15); // Position au milieu des images footer
        }

        const fileName = `SWOT_${new Date().toISOString().split("T")[0]}.pdf`;
        doc.save(fileName);

        toast.success("PDF exporté avec succès !");
        showExportMenu.value = false;
    } catch (error) {
        console.error("Erreur lors de l'exportation en PDF :", error);
        toast.error("Une erreur s'est produite lors de l'exportation.");
    }
};

// Fonction pour extraire le numéro d'un num_actions "SWOT-0004" => 4
const extractNumber = (str) => {
    const match = str.match(/SWOT-(\d+)/);
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
    const sheet = workbook.addWorksheet("SWOT");

    // Définir les colonnes
    sheet.columns = [
        { header: "N°", key: "num_actions", width: 15 },
        { header: "Date", key: "date", width: 15 },
        { header: "Sources", key: "source", width: 20 },
        { header: "Types d'action", key: "type_action", width: 20 },
        { header: "Responsables", key: "responsable", width: 20 },
        { header: "Suivis", key: "suivi", width: 15 },
        {
            header: "Description de la non conformité",
            key: "description",
            width: 30,
        },
        { header: "Constats", key: "constat_libelle", width: 20 },
        { header: "Actions", key: "action", width: 30 },
        { header: "Fréquences", key: "frequenceWithDetails", width: 15 },
        { header: "Livrables", key: "mesure", width: 20 },
        { header: "Statuts", key: "statut", width: 15 },
        { header: "Ajouté par", key: "nom_utilisateur", width: 20 },
        { header: "Observations", key: "observation", width: 30 },
    ];

    // Créer des feuilles séparées pour les listes de référence (pour éviter les limites Excel)
    const createReferenceSheet = (name, data, keyField) => {
        if (!data || data.length === 0) return null;

        const refSheet = workbook.addWorksheet(name);
        refSheet.columns = [{ header: name, key: keyField, width: 30 }];

        data.forEach((item) => {
            refSheet.addRow({ [keyField]: item[keyField] });
        });

        // Masquer la feuille de référence
        refSheet.state = "hidden";

        return refSheet;
    };

    // Créer les feuilles de référence pour les grandes listes
    const sourcesRefSheet = createReferenceSheet(
        "Sources_Ref",
        sourcesList,
        "libelle"
    );
    const typeActionsRefSheet = createReferenceSheet(
        "TypeActions_Ref",
        typeActionsList,
        "libelle"
    );
    const responsablesRefSheet = createReferenceSheet(
        "Responsables_Ref",
        responsablesList,
        "libelle"
    );
    const suivisRefSheet = createReferenceSheet(
        "Suivis_Ref",
        suivisList,
        "nom"
    );
    const constatsRefSheet = createReferenceSheet(
        "Constats_Ref",
        constatsList,
        "libelle"
    );
    const usersRefSheet = createReferenceSheet(
        "Users_Ref",
        usersList,
        "nom_utilisateur"
    );

    // Ajouter les données existantes
    rows.forEach((row) => {
        sheet.addRow({
            num_actions: row.num_actions,
            date: row.date,
            source: row.source_libelle,
            type_action: row.type_action_libelle,
            responsable: row.responsables_libelle,
            suivi: row.suivis_noms,
            description: row.description,
            constat_libelle: row.constat_libelle,
            action: row.action,
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

    // Réduire le nombre de lignes supplémentaires pour éviter les problèmes de performance
    const additionalRows = 200; // Réduit de 500 à 200

    // Ajouter des lignes vides avec des formules pour l'auto-incrémentation
    for (let i = 0; i < additionalRows; i++) {
        const row = sheet.addRow({});
        const rowIndex = startRow + i;

        // Ajouter la formule pour la colonne A (num_actions)
        if (i === 0) {
            // Première ligne vide - basée sur le dernier numéro connu
            sheet.getCell(`A${rowIndex}`).value = {
                formula: `IF(B${rowIndex}<>"", CONCATENATE("SWOT-", TEXT(${
                    currentNum + 1
                }, "0000")), "")`,
            };
        } else {
            // Lignes suivantes - basées sur la ligne précédente
            sheet.getCell(`A${rowIndex}`).value = {
                formula: `IF(B${rowIndex}<>"", IF(B${
                    rowIndex - 1
                }<>"", CONCATENATE("SWOT-", TEXT(VALUE(MID(A${
                    rowIndex - 1
                },4,4))+1, "0000")), CONCATENATE("SWOT-", TEXT(${
                    currentNum + 1
                }, "0000"))), "")`,
            };
        }
    }

    // Définir les options pour la liste déroulante des statuts
    const statutOptions = [
        "En cours",
        "En retard",
        "Clôturé",
        "Abandonné",
        "Non Réalisé",
        "A Reporter",
    ];

    // Appliquer un style aux cellules pour les rendre plus lisibles
    sheet.getRow(1).font = { bold: true };
    sheet.getRow(1).alignment = { vertical: "middle", horizontal: "center" };

    // Fonction optimisée pour ajouter les listes déroulantes
    const addDropdown = (
        col,
        list,
        useReference = false,
        refSheetName = null
    ) => {
        const validationStartRow = 2;
        const validationEndRow = startRow + additionalRows - 1;

        // Si la liste est trop grande (>50 éléments) ou si on force l'utilisation de référence
        if (useReference && refSheetName) {
            // Utiliser une référence vers une autre feuille
            const formula = `${refSheetName}!$A$2:$A$${list.length + 1}`;

            for (let i = validationStartRow; i <= validationEndRow; i++) {
                try {
                    sheet.getCell(`${col}${i}`).dataValidation = {
                        type: "list",
                        allowBlank: true,
                        formulae: [formula],
                        showDropDown: true,
                    };
                } catch (error) {
                    console.warn(
                        `Erreur lors de l'ajout de la validation pour ${col}${i}:`,
                        error
                    );
                }
            }
        } else {
            // Utiliser la méthode traditionnelle pour les petites listes
            const listString = list.join(",");

            // Vérifier la longueur de la chaîne (Excel a une limite de ~255 caractères)
            if (listString.length > 255) {
                console.warn(
                    `Liste trop longue pour la colonne ${col}, utilisation de la référence recommandée`
                );
                return;
            }

            for (let i = validationStartRow; i <= validationEndRow; i++) {
                try {
                    sheet.getCell(`${col}${i}`).dataValidation = {
                        type: "list",
                        allowBlank: true,
                        formulae: [`"${listString}"`],
                        showDropDown: true,
                    };
                } catch (error) {
                    console.warn(
                        `Erreur lors de l'ajout de la validation pour ${col}${i}:`,
                        error
                    );
                }
            }
        }
    };

    // Appliquer les listes déroulantes avec gestion intelligente
    try {
        if (sourcesList.length > 0) {
            const sourceLabels = sourcesList.map((s) => s.libelle);
            addDropdown(
                "C",
                sourceLabels,
                sourcesList.length > 50,
                "Sources_Ref"
            );
        }

        if (typeActionsList.length > 0) {
            const typeActionLabels = typeActionsList.map((t) => t.libelle);
            addDropdown(
                "D",
                typeActionLabels,
                typeActionsList.length > 50,
                "TypeActions_Ref"
            );
        }

        if (responsablesList.length > 0) {
            const responsableLabels = responsablesList.map((r) => r.libelle);
            addDropdown(
                "E",
                responsableLabels,
                responsablesList.length > 50,
                "Responsables_Ref"
            );
        }

        if (suivisList.length > 0) {
            const suiviLabels = suivisList.map((s) => s.nom);
            addDropdown("F", suiviLabels, suivisList.length > 50, "Suivis_Ref");
        }

        if (constatsList.length > 0) {
            const constatLabels = constatsList.map((c) => c.libelle);
            addDropdown(
                "H",
                constatLabels,
                constatsList.length > 50,
                "Constats_Ref"
            );
        }

        if (usersList.length > 0) {
            const userLabels = usersList.map((u) => u.nom_utilisateur);
            addDropdown("M", userLabels, usersList.length > 50, "Users_Ref");
        }

        // Appliquer les listes plus petites normalement
        addDropdown("J", frequenceOptions, false);
        addDropdown("L", statutOptions, false);
    } catch (error) {
        console.error("Erreur lors de l'application des validations:", error);
    }

    // Activer les filtres pour faciliter la navigation
    try {
        sheet.autoFilter = {
            from: { row: 1, column: 1 },
            to: { row: 1, column: 13 },
        };
    } catch (error) {
        console.warn("Impossible d'appliquer les filtres automatiques:", error);
    }

    // Générer le fichier avec gestion d'erreur
    try {
        const buffer = await workbook.xlsx.writeBuffer();
        saveAs(new Blob([buffer]), "SWOT.xlsx");
        console.log("Export Excel réussi");
    } catch (error) {
        console.error("Erreur lors de la génération du fichier Excel:", error);
        throw new Error("Impossible de générer le fichier Excel");
    }
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
                const description = row.getCell(7).value; // Colonne G (Description)
                const action = row.getCell(9).value; // Colonne I (Actions)

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
                const frequence = row.getCell(10).value;
                const mesure = row.getCell(11).value;
                const statut = row.getCell(12).value;
                const nomUtilisateur = row.getCell(13).value;
                const observation = row.getCell(14).value;

                importedData.push({
                    num_actions: numActionsValue,
                    date: formattedDate,
                    source_libelle: sourceLibelle,
                    type_action_libelle: typeActionLibelle,
                    responsables_libelle: responsableLibelle,
                    suivis_noms: suiviNom,
                    description: description,
                    constat_libelle: constatLibelle,
                    action: action,
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
onMounted(async () => {
    chargerActions(currentPage.value, searchQuery.value);
    await verifierTousLesStatuts();
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
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <!-- Titre -->
                <div class="p-5">
                    <!-- Titre -->
                    <div class="flex w-full mb-6">
                        <div
                            class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                        >
                            Actions
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Dans l'espace actions, vous pourriez voir et
                                gérer les informations sur les actions, que ce
                                soit Audit Interne, PTA, Audit Externe, SWOT,
                                Enquête de Satisfaction et CAC, d'y ajouter et
                                encore plus.
                            </p>
                        </div>

                        <!-- Choix d'action à faire(A.I / PTA / A.E) -->
                        <div
                            class="flex w-full mt-5 ml-4 justify-center space-x-4"
                        >
                            <!-- Lien Audit Interne -->
                            <router-link
                                to="/user/actions/auditinterne"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path ===
                                        '/user/actions/auditinterne',
                                }"
                            >
                                Audit Interne
                            </router-link>

                            <div class="border-r border-gray-300"></div>

                            <!-- Lien PTA -->
                            <router-link
                                to="/user/actions/pta"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path === '/user/actions/pta',
                                }"
                            >
                                PTA
                            </router-link>

                            <div class="border-r border-gray-300"></div>

                            <!-- Lien AE -->
                            <router-link
                                to="/user/actions/ae"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path === '/user/actions/ae',
                                }"
                            >
                                Audit Externe
                            </router-link>

                            <div class="border-r border-gray-300"></div>

                            <!-- Lien CAC -->
                            <router-link
                                to="/user/actions/cac"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path === '/user/actions/cac',
                                }"
                            >
                                CAC
                            </router-link>

                            <div class="border-r border-gray-300"></div>

                            <!-- Lien Enquete -->
                            <router-link
                                to="/user/actions/enquete"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path === '/user/actions/enquete',
                                }"
                            >
                                ENQUETE
                            </router-link>

                            <div class="border-r border-gray-300"></div>

                            <!-- Lien SWOT -->
                            <router-link
                                to="/user/actions/swot"
                                class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                                :class="{
                                    'border-b-4 border-blue-600':
                                        $route.path === '/user/actions/swot',
                                }"
                            >
                                SWOT
                            </router-link>
                        </div>

                        <!-- Ajout et barre de recherche -->
                        <div class="flex w-full mt-5 ml-4">
                            <!-- Bouton d'ajout -->
                            <router-link to="/user/actions/swot/ajouter">
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

                            <!-- Vérification du status s'il y a des actions qui doit être en retard -->
                            <div class="relative ml-4">
                                <button
                                    @click="verifierTousLesStatuts"
                                    :disabled="isCheckingStatus"
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <!-- Icône de chargement -->
                                    <svg
                                        v-if="isCheckingStatus"
                                        class="animate-spin h-4 w-4"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                            fill="none"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    <!-- Icône de vérification -->
                                    <svg
                                        v-else
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                    {{
                                        isCheckingStatus
                                            ? "Vérification en cours..."
                                            : "Vérifier tous les statuts"
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
                                    :disabled="
                                        currentPage <= 1 || totalActions === 0
                                    "
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
                                        currentPage >= lastPage ||
                                        totalActions === 0
                                    "
                                    @click="changerPage(currentPage + 1)"
                                >
                                    Suiv. <ChevronRight class="w-4 h-4" />
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
