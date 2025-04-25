<!-- FrequencePonctuel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option ponctuelle -->
        <div
            v-if="ponctuelData && ponctuelData.debut"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Début:</span>
                {{ formatDate(ponctuelData.debut) }}
            </div>
            <div>
                <span class="font-medium">Fin:</span>
                {{ formatDate(ponctuelData.fin) }}
            </div>
            <div v-if="ponctuelData.suivis && ponctuelData.suivis.length > 0">
                <span class="font-medium">Suivis:</span>
                <span
                    v-for="(suivi, index) in ponctuelData.suivis"
                    :key="index"
                >
                    {{ index > 0 ? ", " : "" }}
                    suivi {{ index + 1 }}: {{ formatDate(suivi) }}
                </span>
            </div>
        </div>

        <!-- Modal pour l'option ponctuelle -->
        <ModalPonctuel
            :modelValue="showModal"
            :titre="'Ponctuel'"
            @save="savePonctuelData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalPonctuel from "./ModalPonctuel.vue";

export default {
    name: "FrequencePonctuel",
    components: {
        ModalPonctuel,
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
            ponctuelData: null, // Données spécifiques à l'option ponctuelle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.ponctuelData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.ponctuelData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        savePonctuelData(data) {
            this.ponctuelData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
        formatDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            const jour = date.getDate().toString().padStart(2, "0");
            const mois = (date.getMonth() + 1).toString().padStart(2, "0");
            const annee = date.getFullYear();
            const heures = date.getHours().toString().padStart(2, "0");
            const minutes = date.getMinutes().toString().padStart(2, "0");

            return `${jour}/${mois}/${annee} à ${heures}:${minutes}`;
        },
    },
};
</script>
