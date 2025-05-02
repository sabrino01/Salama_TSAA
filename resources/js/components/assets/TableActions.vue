<template>
    <div class="overflow-x-auto">
        <table
            class="table-fixed w-full border-separate border-spacing-y-[0.5rem] border border-gray-300"
        >
            <thead class="bg-gray-300 text-lg">
                <tr>
                    <!-- Checkbox pour sélectionner toutes les lignes -->
                    <th
                        class="border border-gray-300 px-2 py-2 text-center w-12"
                    >
                        <input
                            type="checkbox"
                            v-model="selectAll"
                            @change="toggleSelectAll"
                        />
                    </th>
                    <th
                        v-for="(column, index) in columns"
                        :key="index"
                        class="border border-gray-300 px-4 py-2 text-center"
                    >
                        <div class="flex items-center justify-center">
                            {{ column.label }}
                            <!-- Ajouter les flèches de tri pour la colonne "N°" -->
                            <template v-if="column.field === 'num_actions'">
                                <button
                                    class="ml-2 text-gray-600 hover:text-black"
                                    @click="sortData('desc')"
                                >
                                    ▲
                                </button>
                                <button
                                    class="ml-1 text-gray-600 hover:text-black"
                                    @click="sortData('asc')"
                                >
                                    ▼
                                </button>
                            </template>
                        </div>
                    </th>
                    <th v-if="actions" class="border border-gray-300 px-4 py-2">
                        -
                    </th>
                </tr>
            </thead>
            <tbody class="font-poppins text-center">
                <tr
                    v-for="(row, rowIndex) in sortedData"
                    :key="rowIndex"
                    :class="{
                        'bg-green-300': row.statut === 'En cours',
                        'bg-red-300': row.statut === 'En retard',
                        'bg-gray-300': row.statut === 'Clôturé',
                        'bg-purple-300': row.statut === 'Abandonné',
                    }"
                    class="shadow-md rounded-md"
                >
                    <!-- Checkbox pour sélectionner une ligne -->
                    <td class="border border-gray-300 px-2 py-2 text-center">
                        <input
                            type="checkbox"
                            v-model="selectedRows"
                            :value="row.num_actions"
                        />
                    </td>
                    <td
                        v-for="(column, colIndex) in columns"
                        :key="colIndex"
                        class="px-4 py-2"
                        :class="column.classes"
                    >
                        <!-- Icônes pour la colonne "constat_libelle" -->
                        <template v-if="column.field === 'constat_libelle'">
                            <div
                                class="flex justify-center underline space-x-2"
                            >
                                <component
                                    :is="getConstatIcon(row[column.field])"
                                    class="w-5 h-5"
                                    :class="getConstatColor(row[column.field])"
                                />
                                <span>{{ row[column.field] }}</span>
                            </div>
                        </template>
                        <!-- Vérifiez si la colonne est extensible -->
                        <div v-if="column.isExpandable" class="relative">
                            <button
                                type="button"
                                class="text-black ml-2"
                                @click="openModal(column.render(row))"
                            >
                                {{ column.render(row) }}
                            </button>
                        </div>
                        <!-- Rendu personnalisé pour les colonnes avec tooltip -->
                        <div
                            v-if="hasCustomRender(column, row)"
                            class="relative"
                        >
                            <button
                                type="button"
                                class="cursor-pointer text-black hover:text-black underline decoration-dotted decoration-2 font-medium focus:outline-none"
                                @click="toggleTooltip(rowIndex, colIndex)"
                            >
                                {{ getColumnValue(column, row) }}
                            </button>
                            <!-- Tooltip personnalisé -->
                            <div
                                v-if="
                                    activeTooltip.rowIndex === rowIndex &&
                                    activeTooltip.colIndex === colIndex
                                "
                                class="absolute z-10 p-3 bg-gray-800 text-white text-left rounded shadow-lg whitespace-pre-wrap mt-1"
                                :class="getTooltipPositionClass(column, row)"
                            >
                                <div
                                    class="flex justify-between items-center mb-2 border-b border-gray-600 pb-1"
                                >
                                    <span class="font-semibold">Détails</span>
                                    <button
                                        @click="hideTooltip()"
                                        class="text-gray-300 hover:text-white"
                                    >
                                        ×
                                    </button>
                                </div>
                                {{ getTooltipContent(column, row) }}
                            </div>
                        </div>
                        <!-- Rendu standard pour les autres colonnes -->
                        <template
                            v-if="
                                column.field !== 'constat_libelle' &&
                                !column.isExpandable &&
                                !hasCustomRender(column, row)
                            "
                        >
                            {{ getColumnValue(column, row) }}
                        </template>
                    </td>
                    <td v-if="actions.length > 0" class="py-3 space-x-2">
                        <button
                            v-for="(action, actionIndex) in getFilteredActions(
                                row
                            )"
                            :key="actionIndex"
                            :class="action.class"
                            @click="action.handler(row)"
                        >
                            {{ action.label }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div
        v-if="modalVisible"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white p-6 rounded shadow-lg max-w-lg w-full">
            <h2 class="text-lg font-bold mb-4">Détails</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ modalContent }}</p>
            <div class="flex w-full justify-end">
                <button
                    class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    @click="closeModal"
                >
                    Fermer
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import {
    RefreshCcw,
    FileCheck,
    Check,
    X,
    CheckCircle,
    Search,
    AlertTriangle,
    Ban,
    Clock,
} from "lucide-vue-next";
const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    actions: {
        type: Array,
        required: false,
        default: () => [],
    },
    filterActions: {
        type: Function,
        default: (row, actions) => actions,
    },
});

// Gestion des lignes sélectionnées
const selectedRows = ref([]);
const selectAll = ref(false);

// Gestion du tooltip actif
const activeTooltip = ref({
    rowIndex: null,
    colIndex: null,
});

// Gestion des données triées
const sortOrder = ref("desc"); // Ordre de tri par défaut (desc)
const sortedData = computed(() => {
    return [...props.data].sort((a, b) => {
        const numA = parseInt(a.num_actions.split("-")[1]);
        const numB = parseInt(b.num_actions.split("-")[1]);
        return sortOrder.value === "desc" ? numB - numA : numA - numB; // Inverser la logique
    });
});

// Fonction pour trier les données
const sortData = (order) => {
    sortOrder.value = order;
};

// Fonction pour filtrer les actions
const getFilteredActions = (row) => {
    return props.filterActions(row, props.actions);
};

// Fonction pour obtenir l'icône en fonction de la valeur de "constat_libelle"
const getConstatIcon = (value) => {
    const icons = {
        "En Cours": RefreshCcw,
        Réalisé: Check,
        "Non Réalisé": X,
        "Ecart Réglé": CheckCircle,
        "Ecart Réglé à Suivre": Search,
        "Ecart non réglé": AlertTriangle,
        "Réalisé partiel": FileCheck,
        "Actions abandonnées": Ban,
        Retard: Clock,
    };
    return icons[value] || null;
};

// Fonction pour obtenir la couleur en fonction de la valeur de "constat_libelle"
const getConstatColor = (value) => {
    const colors = {
        "En Cours": "text-blue-500",
        Réalisé: "text-green-500",
        "Non Réalisé": "text-red-500",
        "Ecart Réglé": "text-blue-800",
        "Ecart Réglé à Suivre": "text-gray-500",
        "Ecart non réglé": "text-yellow-500",
        "Réalisé partiel": "text-green-600",
        "Actions abandonnées": "text-purple-500",
        Retard: "text-red-500",
    };
    return colors[value] || "";
};

// Fonction pour gérer la sélection/désélection de toutes les lignes
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedRows.value = props.data.map((row) => row.num_actions);
    } else {
        selectedRows.value = [];
    }
};

// Fonction pour vérifier si une colonne a un rendu personnalisé
const hasCustomRender = (column, row) => {
    if (!column.render) return false;
    const result = column.render(row);
    return result && typeof result === "object" && result.customRender === true;
};

// Fonction pour obtenir la valeur à afficher dans une cellule
const getColumnValue = (column, row) => {
    if (column.render) {
        const result = column.render(row);
        if (
            result &&
            typeof result === "object" &&
            result.customRender === true
        ) {
            return result.value;
        }
        return result;
    }
    return row[column.field];
};

// Fonction pour obtenir le contenu du tooltip
const getTooltipContent = (column, row) => {
    if (column.render) {
        const result = column.render(row);
        if (
            result &&
            typeof result === "object" &&
            result.customRender === true
        ) {
            return result.tooltipContent || "";
        }
    }
    return "";
};

// Détermine la classe de position du tooltip en fonction de la longueur du contenu
const getTooltipPositionClass = (column, row) => {
    const content = getTooltipContent(column, row);
    // Ajuster la largeur en fonction de la longueur du contenu
    if (content.length > 200) return "w-96 left-0";
    if (content.length > 100) return "w-80 left-0";
    return "w-64 left-0";
};

// Fonctions pour gérer l'affichage du tooltip
const toggleTooltip = (rowIndex, colIndex) => {
    // Si le tooltip est déjà actif pour cette cellule, le fermer
    if (
        activeTooltip.value.rowIndex === rowIndex &&
        activeTooltip.value.colIndex === colIndex
    ) {
        hideTooltip();
    } else {
        // Sinon, ouvrir le tooltip pour cette cellule
        activeTooltip.value = { rowIndex, colIndex };
    }
};

const hideTooltip = () => {
    activeTooltip.value = { rowIndex: null, colIndex: null };
};

const modalVisible = ref(false); // État pour afficher ou masquer la modale
const modalContent = ref(""); // Contenu à afficher dans la modale

// Fonction pour ouvrir la modale
const openModal = (content) => {
    modalContent.value = content;
    modalVisible.value = true;
};

// Fonction pour fermer la modale
const closeModal = () => {
    modalVisible.value = false;
    modalContent.value = "";
};

// Mettre à jour `selectAll` si toutes les lignes sont sélectionnées ou non
watch(selectedRows, (newSelectedRows) => {
    selectAll.value =
        newSelectedRows.length === props.data.length && props.data.length > 0;
});

// expose pour que le parent puisse accéder à selectedRows, importer les données dans AuditInterne.vue
defineExpose({ selectedRows });
</script>

<style scoped>
/* Supprimer les bordures entre les lignes */
tbody tr td {
    border: none;
}

table {
    border: none;
}
</style>
