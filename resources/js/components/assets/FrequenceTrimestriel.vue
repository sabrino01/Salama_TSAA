<!-- FrequenceTrimestriel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option trimestrielle -->
        <div
            v-if="trimestrielData && trimestrielData.joursHeure"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Jours:</span>
                {{ trimestrielData.joursHeure.jours.join(", ") }}
            </div>
            <div>
                <span class="font-medium">Heures:</span>
                {{ trimestrielData.joursHeure.heureDebut }} -
                {{ trimestrielData.joursHeure.heureFin }}
            </div>
            <div>
                <span class="font-medium">Commencement:</span>
                <template v-if="trimestrielData.periode">
                    {{
                        trimestrielData.periode === "cetteSemaine"
                            ? "Dès cette semaine"
                            : ""
                    }}
                    {{
                        trimestrielData.periode === "semaineProchaine"
                            ? "Semaine prochaine"
                            : ""
                    }}
                    {{
                        trimestrielData.periode === "moisProchain"
                            ? "Le mois prochain"
                            : ""
                    }}
                </template>
                <template v-else> Non défini </template>
            </div>
            <div
                v-if="
                    trimestrielData.joursHeure.suivis &&
                    trimestrielData.joursHeure.suivis.length > 0
                "
            >
                <span class="font-medium">Suivis:</span>
                <div
                    v-for="(suivi, sIndex) in trimestrielData.joursHeure.suivis"
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

        <!-- Modal pour l'option Trimestriel -->
        <ModalTrimestriel
            :modelValue="showModal"
            :titre="'Trimestriel'"
            @save="saveTrimestrielData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalTrimestriel from "./ModalTrimestriel.vue";

export default {
    name: "FrequenceTrimestriel",
    components: {
        ModalTrimestriel,
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
            trimestrielData: null, // Données spécifiques à l'option trimestrielle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.trimestrielData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.trimestrielData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveTrimestrielData(data) {
            this.trimestrielData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
    },
};
</script>
