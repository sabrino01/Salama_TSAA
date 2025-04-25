<!-- FrequenceQuadrimestriel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option trimestrielle -->
        <div
            v-if="quadrimestrielData && quadrimestrielData.joursHeure"
            class="ml-4 text-sm text-gray-700"
        >
            <div>
                <span class="font-medium">Jours:</span>
                {{ quadrimestrielData.joursHeure.jours.join(", ") }}
            </div>
            <div>
                <span class="font-medium">Heures:</span>
                {{ quadrimestrielData.joursHeure.heureDebut }} -
                {{ quadrimestrielData.joursHeure.heureFin }}
            </div>
            <div>
                <span class="font-medium">Commencement:</span>
                <template v-if="quadrimestrielData.periode">
                    {{
                        quadrimestrielData.periode === "cetteSemaine"
                            ? "Dès cette semaine"
                            : ""
                    }}
                    {{
                        quadrimestrielData.periode === "semaineProchaine"
                            ? "Semaine prochaine"
                            : ""
                    }}
                    {{
                        quadrimestrielData.periode === "moisProchain"
                            ? "Le mois prochain"
                            : ""
                    }}
                </template>
                <template v-else> Non défini </template>
            </div>
            <div
                v-if="
                    quadrimestrielData.joursHeure.suivis &&
                    quadrimestrielData.joursHeure.suivis.length > 0
                "
            >
                <span class="font-medium">Suivis:</span>
                <div
                    v-for="(suivi, sIndex) in quadrimestrielData.joursHeure
                        .suivis"
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
        <ModalQuadrimestriel
            :modelValue="showModal"
            :titre="'Quadrimestriel'"
            @save="saveQuadrimestrielData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalQuadrimestriel from "./ModalQuadrimestriel.vue";

export default {
    name: "FrequenceQuadrimestriel",
    components: {
        ModalQuadrimestriel,
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
            quadrimestrielData: null, // Données spécifiques à l'option quadrimestrielle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.quadrimestrielData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.quadrimestrielData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveQuadrimestrielData(data) {
            this.quadrimestrielData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value); // Émet l'événement pour mettre à jour showModal
        },
    },
};
</script>
