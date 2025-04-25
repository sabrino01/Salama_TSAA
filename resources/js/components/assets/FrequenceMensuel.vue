<!-- FrequenceMensuel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option mensuelle -->
        <div
            v-if="mensuelData && mensuelData.joursHeure"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Jours:</span>
                {{ mensuelData.joursHeure.jours.join(", ") }}
            </div>
            <div>
                <span class="font-medium">Heures:</span>
                {{ mensuelData.joursHeure.heureDebut }} -
                {{ mensuelData.joursHeure.heureFin }}
            </div>
            <div>
                <span class="font-medium">Commencement:</span>
                <template v-if="mensuelData.periode">
                    {{
                        mensuelData.periode === "cetteSemaine"
                            ? "Dès cette semaine"
                            : ""
                    }}
                    {{
                        mensuelData.periode === "semaineProchaine"
                            ? "Semaine prochaine"
                            : ""
                    }}
                    {{
                        mensuelData.periode === "moisProchain"
                            ? "Le mois prochain"
                            : ""
                    }}
                </template>
                <template v-else> Non défini </template>
            </div>
            <div
                v-if="
                    mensuelData.joursHeure.suivis &&
                    mensuelData.joursHeure.suivis.length > 0
                "
            >
                <span class="font-medium">Suivis:</span>
                <div
                    v-for="(suivi, sIndex) in mensuelData.joursHeure.suivis"
                    :key="`joursHeureSuivi-${sIndex}`"
                    class="ml-2 mb-1"
                >
                    <template v-if="suivi.jours && suivi.jours.length > 0">
                        Suivi {{ sIndex + 1 }}: {{ suivi.jours.join(", ") }} -
                        {{ suivi.heureDebut }} à {{ suivi.heureFin }}
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal pour l'option Mensuel -->
        <ModalMensuel
            :modelValue="showModal"
            :titre="'Mensuel'"
            @save="saveMensuelData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalMensuel from "./ModalMensuel.vue";

export default {
    name: "FrequenceMensuel",
    components: {
        ModalMensuel,
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
    emits: ["update:modelValue", "update:showModal"],
    data() {
        return {
            mensuelData: null, // Données spécifiques à l'option mensuelle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.mensuelData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.mensuelData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveMensuelData(data) {
            this.mensuelData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
    },
};
</script>
