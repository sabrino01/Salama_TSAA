<!-- FrequenceBimestriel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option bimestrielle -->
        <div
            v-if="bimestrielData && bimestrielData.joursHeure"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Jours:</span>
                {{ bimestrielData.joursHeure.jours.join(", ") }}
            </div>
            <div>
                <span class="font-medium">Heures:</span>
                {{ bimestrielData.joursHeure.heureDebut }} -
                {{ bimestrielData.joursHeure.heureFin }}
            </div>
            <div>
                <span class="font-medium">Commencement:</span>
                <template v-if="bimestrielData.periode">
                    {{
                        bimestrielData.periode === "cetteSemaine"
                            ? "Dès cette semaine"
                            : ""
                    }}
                    {{
                        bimestrielData.periode === "semaineProchaine"
                            ? "Semaine prochaine"
                            : ""
                    }}
                    {{
                        bimestrielData.periode === "moisProchain"
                            ? "Le mois prochain"
                            : ""
                    }}
                </template>
                <template v-else> Non défini </template>
            </div>
            <div
                v-if="
                    bimestrielData.joursHeure.suivis &&
                    bimestrielData.joursHeure.suivis.length > 0
                "
            >
                <span class="font-medium">Suivis:</span>
                <div
                    v-for="(suivi, sIndex) in bimestrielData.joursHeure.suivis"
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
        <ModalBimestriel
            :modelValue="showModal"
            :titre="'Bimestriel'"
            @save="saveBimestrielData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalBimestriel from "./ModalBimestriel.vue";

export default {
    name: "FrequenceBimestriel",
    components: {
        ModalBimestriel,
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
            bimestrielData: null, // Données spécifiques à l'option bimestrielle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.bimestrielData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.bimestrielData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveBimestrielData(data) {
            this.bimestrielData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
    },
};
</script>
