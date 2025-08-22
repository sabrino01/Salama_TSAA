<template>
    <div class="overflow-x-auto">
        <table
            class="table-fixed w-full border-separate border-spacing-y-[0.5rem]"
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
                        :class="[
                            'border border-gray-300 px-4 py-2 text-center relative',
                            column.field === 'description' ? 'relative' : '',
                        ]"
                        :style="
                            column.field === 'description'
                                ? {
                                      width: descriptionWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : column.field === 'responsables_libelle'
                                ? {
                                      width: responsablesWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : column.field === 'suivis_noms'
                                ? {
                                      width: suivisWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : {}
                        "
                    >
                        <div class="flex items-center justify-center">
                            <!-- Bouton cliquable pour le label -->
                            <button
                                @click="toggleColumnFilter(column.field)"
                                class="flex items-center justify-center cursor-pointer hover:text-blue-600"
                            >
                                {{ column.label }}
                                <svg
                                    class="w-4 h-4 ml-1"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>

                            <!-- Menu déroulant pour le filtrage -->
                            <div
                                v-if="activeFilter === column.field"
                                class="absolute top-full left-0 mt-2 bg-white border border-gray-300 rounded-md shadow-lg w-64 z-50"
                            >
                                <!-- Input de recherche -->
                                <div class="p-3 border-b border-gray-200">
                                    <input
                                        type="text"
                                        v-model="columnSearches[column.field]"
                                        @input="
                                            filterColumnValues(column.field)
                                        "
                                        placeholder="Rechercher..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <!-- Liste des valeurs avec checkboxes -->
                                <div class="max-h-60 overflow-y-auto">
                                    <!-- Option "Tout sélectionner/désélectionner" -->
                                    <div class="p-2 border-b border-gray-100">
                                        <label
                                            class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="
                                                    isAllSelected(column.field)
                                                "
                                                :indeterminate.prop="
                                                    isIndeterminate(
                                                        column.field
                                                    )
                                                "
                                                @change="
                                                    toggleAllColumnValues(
                                                        column.field
                                                    )
                                                "
                                                class="mr-2"
                                            />
                                            <span
                                                class="font-medium text-sm text-gray-700"
                                            >
                                                {{
                                                    isAllSelected(column.field)
                                                        ? "Tout désélectionner"
                                                        : "Tout sélectionner"
                                                }}
                                            </span>
                                        </label>
                                    </div>

                                    <!-- Valeurs filtrées -->
                                    <div
                                        v-for="value in getFilteredColumnValues(
                                            column.field
                                        )"
                                        :key="value"
                                        class="p-2"
                                    >
                                        <label
                                            class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded"
                                        >
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    columnFilters[column.field]
                                                "
                                                :value="value"
                                                @change="applyFilters"
                                                class="mr-2"
                                            />
                                            <span
                                                class="text-sm text-gray-700 truncate"
                                            >
                                                {{ value || "(Vide)" }}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div
                                    class="p-3 border-t border-gray-200 flex justify-between"
                                >
                                    <button
                                        @click="resetColumnFilter(column.field)"
                                        class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                                    >
                                        Réinitialiser
                                    </button>
                                    <button
                                        @click="closeColumnFilter"
                                        class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                                    >
                                        Fermer
                                    </button>
                                </div>
                            </div>

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
                            <template
                                v-if="
                                    [
                                        'description',
                                        'responsables_libelle',
                                        'suivis_noms',
                                    ].includes(column.field)
                                "
                            >
                                <span
                                    class="absolute right-0 top-0 h-full w-2 cursor-col-resize bg-transparent"
                                    @mousedown="
                                        (e) => startResize(e, column.field)
                                    "
                                ></span>
                            </template>
                        </div>
                    </th>
                    <th v-if="actions" class="border border-gray-300 px-4 py-2">
                        -
                    </th>
                </tr>
            </thead>
            <tbody class="font-poppins text-center">
                <!-- Si aucune donnée n'est disponible -->
                <tr v-if="filteredAndSortedData.length === 0">
                    <td
                        :colspan="columns.length + 2"
                        class="py-4 text-gray-500 italic"
                    >
                        Aucune donnée disponible.
                    </td>
                </tr>

                <!-- Si des données sont présentes -->
                <tr
                    v-else
                    v-for="(row, rowIndex) in filteredAndSortedData"
                    :key="rowIndex"
                    class="shadow-md rounded-md"
                >
                    <!-- Checkbox pour sélectionner une ligne -->
                    <td
                        class="px-2 py-2 text-center border-l-8"
                        :class="{
                            'border-blue-500': row.statut === 'En cours',
                            'border-red-500': row.statut === 'En retard',
                            'border-green-500': row.statut === 'Clôturé',
                            'border-gray-500': row.statut === 'Abandonné',
                            'border-white': row.statut === 'Non Réalisé',
                            'border-purple-600': row.statut === 'A Reporter',
                            'border-green-600': row.statut === 'Réglé',
                            'border-gray-600': row.statut === 'Non Réglé',
                        }"
                    >
                        <input
                            type="checkbox"
                            v-model="selectedRows"
                            :value="row.num_actions"
                        />
                    </td>
                    <td
                        v-for="(column, colIndex) in columns"
                        :key="colIndex"
                        :class="[
                            'px-4 py-2',
                            [
                                'description',
                                'responsables_libelle',
                                'suivis_noms',
                            ].includes(column.field)
                                ? 'overflow-x-auto'
                                : '',
                            column.classes || '', // Ajoute la classe personnalisée si elle existe
                        ]"
                        :style="
                            column.field === 'description'
                                ? {
                                      width: descriptionWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : column.field === 'responsables_libelle'
                                ? {
                                      width: responsablesWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : column.field === 'suivis_noms'
                                ? {
                                      width: suivisWidth + 'px',
                                      minWidth: '120px',
                                      maxWidth: '600px',
                                  }
                                : {}
                        "
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
        <div
            class="bg-white p-0 rounded shadow-lg max-w-2xl w-full"
            style="max-height: 90vh"
        >
            <div
                style="
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                "
            >
                <!-- Contenu scrollable -->
                <div style="padding: 2px; overflow-y: auto; max-height: 70vh">
                    <h2 class="text-lg font-bold mb-4">Détails</h2>
                    <div
                        class="text-gray-700 whitespace-pre-wrap"
                        style="padding: 2px"
                    >
                        {{ modalContent }}
                    </div>
                </div>
                <!-- Bouton toujours visible en bas -->
                <div class="flex w-full justify-end mt-4" style="padding: 2px">
                    <button
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        @click="closeModal"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount, onMounted } from "vue";
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

// Variables existantes
const descriptionWidth = ref(350);
const responsablesWidth = ref(250);
const suivisWidth = ref(250);
const selectedRows = ref([]);
const selectAll = ref(false);
const activeTooltip = ref({
    rowIndex: null,
    colIndex: null,
});
const sortOrder = ref("desc");
const modalVisible = ref(false);
const modalContent = ref("");

// NOUVELLES VARIABLES pour les filtres de colonnes
const activeFilter = ref(null); // Colonne actuellement filtrée
const columnFilters = ref({}); // Filtres par colonne {columnField: [values...]}
const columnSearches = ref({}); // Recherche par colonne {columnField: searchTerm}
const uniqueColumnValues = ref({}); // Valeurs uniques par colonne
const filteredColumnValues = ref({}); // Valeurs filtrées par recherche

// Variables pour le redimensionnement (existantes)
let resizing = false;
let resizingField = null;

// NOUVELLE COMPUTED pour remplacer sortedData
const filteredAndSortedData = computed(() => {
    let filtered = [...props.data];

    // Appliquer les filtres de colonnes
    Object.keys(columnFilters.value).forEach((field) => {
        const selectedValues = columnFilters.value[field];
        if (selectedValues && selectedValues.length > 0) {
            filtered = filtered.filter((row) => {
                const value = row[field] || "";
                return selectedValues.includes(value);
            });
        }
    });

    // Appliquer le tri existant
    return filtered.sort((a, b) => {
        const numA = parseInt(a.num_actions.split("-")[1]);
        const numB = parseInt(b.num_actions.split("-")[1]);
        return sortOrder.value === "desc" ? numB - numA : numA - numB;
    });
});

// Garder sortedData pour compatibilité (si utilisé ailleurs)
const sortedData = computed(() => filteredAndSortedData.value);

// NOUVELLES MÉTHODES pour les filtres
const initializeColumnFilters = () => {
    props.columns.forEach((column) => {
        const uniqueValues = [
            ...new Set(props.data.map((row) => row[column.field] || "")),
        ];
        uniqueColumnValues.value[column.field] = uniqueValues;
        filteredColumnValues.value[column.field] = uniqueValues;
        columnFilters.value[column.field] = [...uniqueValues];
        columnSearches.value[column.field] = "";
    });
};

const toggleColumnFilter = (field) => {
    if (activeFilter.value === field) {
        activeFilter.value = null;
    } else {
        activeFilter.value = field;
    }
};

const closeColumnFilter = () => {
    activeFilter.value = null;
};

const filterColumnValues = (field) => {
    const searchTerm = columnSearches.value[field].toLowerCase();
    filteredColumnValues.value[field] = uniqueColumnValues.value[field].filter(
        (value) => (value || "").toString().toLowerCase().includes(searchTerm)
    );
};

const getFilteredColumnValues = (field) => {
    return filteredColumnValues.value[field] || [];
};

const isAllSelected = (field) => {
    const selectedValues = columnFilters.value[field] || [];
    const availableValues = getFilteredColumnValues(field);
    return (
        selectedValues.length === availableValues.length &&
        availableValues.length > 0
    );
};

const isIndeterminate = (field) => {
    const selectedValues = columnFilters.value[field] || [];
    const availableValues = getFilteredColumnValues(field);
    return (
        selectedValues.length > 0 &&
        selectedValues.length < availableValues.length
    );
};

const toggleAllColumnValues = (field) => {
    const availableValues = getFilteredColumnValues(field);
    if (isAllSelected(field)) {
        // Désélectionner toutes les valeurs visibles
        columnFilters.value[field] = columnFilters.value[field].filter(
            (value) => !availableValues.includes(value)
        );
    } else {
        // Sélectionner toutes les valeurs visibles
        const currentSelected = columnFilters.value[field] || [];
        columnFilters.value[field] = [
            ...new Set([...currentSelected, ...availableValues]),
        ];
    }
};

const resetColumnFilter = (field) => {
    columnFilters.value[field] = [...uniqueColumnValues.value[field]];
    columnSearches.value[field] = "";
    filteredColumnValues.value[field] = [...uniqueColumnValues.value[field]];
};

const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        activeFilter.value = null;
    }
};

// Méthodes existantes (inchangées)
const startResize = (e, field) => {
    resizing = true;
    resizingField = field;
    document.body.style.cursor = "col-resize";
    document.addEventListener("mousemove", handleResize);
    document.addEventListener("mouseup", stopResize);
};

const handleResize = (e) => {
    if (!resizing) return;
    let newWidth = Math.max(120, Math.min(600, e.clientX - 300));
    if (resizingField === "description") descriptionWidth.value = newWidth;
    if (resizingField === "responsables_libelle")
        responsablesWidth.value = newWidth;
    if (resizingField === "suivis_noms") suivisWidth.value = newWidth;
};

const stopResize = () => {
    resizing = false;
    resizingField = null;
    document.body.style.cursor = "";
    document.removeEventListener("mousemove", handleResize);
    document.removeEventListener("mouseup", stopResize);
};

const sortData = (order) => {
    sortOrder.value = order;
};

const getFilteredActions = (row) => {
    return props.filterActions(row, props.actions);
};

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

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedRows.value = filteredAndSortedData.value.map(
            (row) => row.num_actions
        );
    } else {
        selectedRows.value = [];
    }
};

const hasCustomRender = (column, row) => {
    if (!column.render) return false;
    const result = column.render(row);
    return result && typeof result === "object" && result.customRender === true;
};

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

const getTooltipPositionClass = (column, row) => {
    const content = getTooltipContent(column, row);
    if (content.length > 200) return "w-96 left-0";
    if (content.length > 100) return "w-80 left-0";
    return "w-64 left-0";
};

const toggleTooltip = (rowIndex, colIndex) => {
    if (
        activeTooltip.value.rowIndex === rowIndex &&
        activeTooltip.value.colIndex === colIndex
    ) {
        hideTooltip();
    } else {
        activeTooltip.value = { rowIndex, colIndex };
    }
};

const hideTooltip = () => {
    activeTooltip.value = { rowIndex: null, colIndex: null };
};

const openModal = (content) => {
    modalContent.value = content;
    modalVisible.value = true;
};

const closeModal = () => {
    modalVisible.value = false;
    modalContent.value = "";
};

// Watchers
watch(
    () => props.data,
    () => {
        initializeColumnFilters();
    },
    { immediate: true }
);

watch(selectedRows, (newSelectedRows) => {
    selectAll.value =
        newSelectedRows.length === filteredAndSortedData.value.length &&
        filteredAndSortedData.value.length > 0;
});

// Lifecycle hooks
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    stopResize();
    document.removeEventListener("click", handleClickOutside);
});

// Exposer pour le parent
defineExpose({ selectedRows });
</script>
