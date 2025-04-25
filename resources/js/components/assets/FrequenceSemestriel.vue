<!-- FrequenceSemestriel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option bimestrielle -->
        <div
            v-if="semestrielData && semestrielData.joursHeure"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Jours:</span>
                {{ semestrielData.joursHeure.jours.join(", ") }}
            </div>
            <div>
                <span class="font-medium">Heures:</span>
                {{ semestrielData.joursHeure.heureDebut }} -
                {{ semestrielData.joursHeure.heureFin }}
            </div>
            <div>
                <span class="font-medium">Commencement:</span>
                <template v-if="semestrielData.periode">
                    {{
                        semestrielData.periode === "cetteSemaine"
                            ? "Dès cette semaine"
                            : ""
                    }}
                    {{
                        semestrielData.periode === "semaineProchaine"
                            ? "Semaine prochaine"
                            : ""
                    }}
                    {{
                        semestrielData.periode === "moisProchain"
                            ? "Le mois prochain"
                            : ""
                    }}
                </template>
                <template v-else> Non défini </template>
            </div>
            <div
                v-if="
                    semestrielData.joursHeure.suivis &&
                    semestrielData.joursHeure.suivis.length > 0
                "
            >
                <span class="font-medium">Suivis:</span>
                <div
                    v-for="(suivi, sIndex) in semestrielData.joursHeure.suivis"
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

        <!-- Modal pour l'option Bimestriel -->
        <ModalSemestriel
            :modelValue="showModal"
            :titre="'Semestriel'"
            @save="saveSemestrielData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalSemestriel from "./ModalSemestriel.vue";

export default {
    name: "FrequenceSemestriel",
    components: {
        ModalSemestriel,
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
            semestrielData: null, // Données spécifiques à l'option bimestrielle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.semestrielData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.semestrielData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveSemestrielData(data) {
            this.semestrielData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
    },
};
</script>
