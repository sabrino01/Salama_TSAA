<template>
    <div class="overflow-x-auto">
        <table
            class="table-fixed w-full border-collapse border border-gray-300"
        >
            <thead class="bg-gray-300 text-lg">
                <tr>
                    <th
                        v-for="(column, index) in columns"
                        :key="index"
                        class="border border-gray-300 px-4 py-2 text-center"
                    >
                        {{ column.label }}
                    </th>
                    <th v-if="actions" class="border border-gray-300 px-4 py-2">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="font-poppins text-center">
                <tr v-for="(row, rowIndex) in data" :key="rowIndex">
                    <td
                        v-for="(column, colIndex) in columns"
                        :key="colIndex"
                        class="border border-gray-300 px-4 py-2"
                    >
                        {{ row[column.field] }}
                    </td>
                    <td
                        v-if="actions.length > 0"
                        class="border border-gray-300 px-4 py-2"
                    >
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
import { defineProps } from "vue";

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

const getFilteredActions = (row) => {
    return props.filterActions(row, props.actions);
};
</script>

<style scoped>
/* Ajoutez des styles personnalisés si nécessaire */
</style>
