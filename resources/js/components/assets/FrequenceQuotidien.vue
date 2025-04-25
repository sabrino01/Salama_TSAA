<!-- FrequenceQuotidien.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option quotidienne -->
        <div
            v-if="quotidienData && quotidienData.jours"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Heures principales:</span>
                {{ quotidienData.heuresPrincipales.debut }} -
                {{ quotidienData.heuresPrincipales.fin }}
            </div>
            <div>
                <span class="font-medium">Jours:</span>
                {{ getJoursResume() }}
            </div>
            <div v-if="hasSpecificHours">
                <span class="font-medium"
                    >Heures spécifiques pour certains jours</span
                >
            </div>
            <div v-if="hasSuivis">
                <span class="font-medium">Suivis:</span> définis pour
                {{ countSuivis() }} jour(s)
            </div>
        </div>

        <!-- Modal pour l'option quotidienne -->
        <ModalQuotidien
            :modelValue="showModal"
            :titre="'Quotidien'"
            @save="saveQuotidienData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalQuotidien from "./ModalQuotidien.vue";

export default {
    name: "FrequenceQuotidien",
    components: {
        ModalQuotidien,
    },
    props: {
        showModal: {
            type: Boolean,
            required: true,
        },
        modelValue: {
            type: [String, Object],
            default: "",
        },
    },
    emits: ["update:modelValue"],
    data() {
        return {
            quotidienData: null,
        };
    },
    computed: {
        hasSpecificHours() {
            if (!this.quotidienData || !this.quotidienData.jours) return false;
            return Object.values(this.quotidienData.jours).some(
                (jour) => !jour.selectionne
            );
        },
        hasSuivis() {
            if (!this.quotidienData || !this.quotidienData.suivis) return false;
            return Object.keys(this.quotidienData.suivis).length > 0;
        },
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.quotidienData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.quotidienData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveQuotidienData(data) {
            this.quotidienData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
        getJoursResume() {
            if (!this.quotidienData || !this.quotidienData.jours) return "";

            const joursSelectionnes = Object.keys(this.quotidienData.jours)
                .filter((jour) => this.quotidienData.jours[jour].selectionne)
                .join(", ");

            return joursSelectionnes || "Jours spécifiques";
        },
        countSuivis() {
            if (!this.quotidienData || !this.quotidienData.suivis) return 0;
            return Object.keys(this.quotidienData.suivis).length;
        },
    },
};
</script>
