<!-- FrequenceSemestriel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option semestrielle -->
        <div
            v-if="semestrielData && semestrielData.planification"
            class="ml-4 text-sm text-gray-700"
        >
            <!-- Affichage du type de menu -->
            <div class="mb-2">
                <span class="font-medium">Type:</span>
                <span
                    class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-1"
                >
                    {{
                        semestrielData.typeMenu === "dateHeure"
                            ? "Date et heure"
                            : "Date et heure par semaine"
                    }}
                </span>
            </div>

            <!-- Affichage pour le menu "Date et heure" -->
            <template v-if="semestrielData.typeMenu === 'dateHeure'">
                <div class="mb-1">
                    <span class="font-medium">Début:</span>
                    {{
                        formatDateTime(
                            semestrielData.planification.dateHeureDebut
                        )
                    }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin:</span>
                    {{
                        formatDateTime(
                            semestrielData.planification.dateHeureFin
                        )
                    }}
                </div>

                <!-- Affichage des suivis -->
                <div
                    v-if="
                        semestrielData.planification.suivis &&
                        semestrielData.planification.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span class="font-medium">Suivis:</span>
                    <div
                        v-for="(suivi, sIndex) in semestrielData.planification
                            .suivis"
                        :key="`suivi-${sIndex}`"
                        class="ml-2 mb-1"
                    >
                        <template v-if="suivi.dateHeure">
                            Suivi {{ sIndex + 1 }}:
                            {{ formatDateTime(suivi.dateHeure) }}
                        </template>
                    </div>
                </div>
            </template>

            <!-- Affichage pour le menu "Date et heure par semaine" -->
            <template v-if="semestrielData.typeMenu === 'dateHeureSemaine'">
                <div class="mb-1">
                    <span class="font-medium">Début:</span>
                    {{
                        formatDateTime(
                            semestrielData.planification.dateHeureDebut
                        )
                    }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin (mois):</span>
                    {{
                        formatDateTime(
                            semestrielData.planification.dateHeureFin
                        )
                    }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin finale:</span>
                    {{
                        formatDateTime(
                            semestrielData.planification.dateHeureFinale
                        )
                    }}
                </div>

                <!-- Affichage des suivis -->
                <div
                    v-if="
                        semestrielData.planification.suivis &&
                        semestrielData.planification.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span class="font-medium">Suivis:</span>
                    <div
                        v-for="(suivi, sIndex) in semestrielData.planification
                            .suivis"
                        :key="`suivi-semaine-${sIndex}`"
                        class="ml-2 mb-1"
                    >
                        <template v-if="suivi.dateHeure">
                            Suivi {{ sIndex + 1 }}:
                            {{ formatDateTime(suivi.dateHeure) }}
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Message si aucune donnée -->
        <div
            v-else-if="!semestrielData"
            class="ml-4 text-sm text-gray-500 italic"
        >
            Aucune planification semestrielle définie
        </div>

        <!-- Modal pour l'option Semestriel -->
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
            semestrielData: null, // Données spécifiques à l'option mensuelle
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
                        this.semestrielData = null;
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.semestrielData = newValue;
                } else {
                    this.semestrielData = null;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveSemestrielData(data) {
            this.trimestrielData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value);
        },
        formatDateTime(dateTimeString) {
            if (!dateTimeString) return "Non défini";

            try {
                const date = new Date(dateTimeString);
                const options = {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: false,
                };

                return date
                    .toLocaleDateString("fr-FR", options)
                    .replace(",", " à");
            } catch (e) {
                return dateTimeString;
            }
        },
    },
};
</script>
