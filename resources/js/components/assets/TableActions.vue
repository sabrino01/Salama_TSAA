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
                    >
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
                        <template v-else>
                            {{ row[column.field] }}
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
</template>

<script setup>
import { ref, computed, watch } from "vue";
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
const sortOrder = ref("asc"); // Ordre de tri par défaut
const sortedData = computed(() => {
    return [...props.data].sort((a, b) => {
        const numA = parseInt(a.num_actions.split("-")[1]);
        const numB = parseInt(b.num_actions.split("-")[1]);
        return sortOrder.value === "asc" ? numA - numB : numB - numA;
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

// Mettre à jour `selectAll` si toutes les lignes sont sélectionnées ou non
watch(selectedRows, (newSelectedRows) => {
    selectAll.value =
        newSelectedRows.length === props.data.length && props.data.length > 0;
});
</script>

<style scoped>
/* Supprimer les bordures entre les lignes */
tbody tr td {
    border: none;
}

table {
    border: none;
}

/* Style pour le tooltip */
.cursor-help {
    text-decoration-style: dotted;
    text-decoration-thickness: 1px;
    text-underline-offset: 2px;
}
</style>
