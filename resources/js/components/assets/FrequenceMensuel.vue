<!-- FrequenceMensuel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option mensuelle -->
        <div
            v-if="mensuelData && mensuelData.planification"
            class="ml-4 text-sm text-gray-700"
        >
            <!-- Affichage du type de menu -->
            <div class="mb-2">
                <span class="font-medium">Type:</span>
                <span
                    class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-1"
                >
                    {{
                        mensuelData.typeMenu === "dateHeure"
                            ? "Date et heure"
                            : "Date et heure par semaine"
                    }}
                </span>
            </div>

            <!-- Affichage pour le menu "Date et heure" -->
            <template v-if="mensuelData.typeMenu === 'dateHeure'">
                <div class="mb-1">
                    <span class="font-medium">Début:</span>
                    {{
                        formatDateTime(mensuelData.planification.dateHeureDebut)
                    }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin:</span>
                    {{ formatDateTime(mensuelData.planification.dateHeureFin) }}
                </div>

                <!-- Affichage des suivis -->
                <div
                    v-if="
                        mensuelData.planification.suivis &&
                        mensuelData.planification.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span class="font-medium">Suivis:</span>
                    <div
                        v-for="(suivi, sIndex) in mensuelData.planification
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
            <template v-if="mensuelData.typeMenu === 'dateHeureSemaine'">
                <div class="mb-1">
                    <span class="font-medium">Début:</span>
                    {{
                        formatDateTime(mensuelData.planification.dateHeureDebut)
                    }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin (mois):</span>
                    {{ formatDateTime(mensuelData.planification.dateHeureFin) }}
                </div>
                <div class="mb-1">
                    <span class="font-medium">Fin finale:</span>
                    {{
                        formatDateTime(
                            mensuelData.planification.dateHeureFinale
                        )
                    }}
                </div>

                <!-- Affichage des suivis -->
                <div
                    v-if="
                        mensuelData.planification.suivis &&
                        mensuelData.planification.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span class="font-medium">Suivis:</span>
                    <div
                        v-for="(suivi, sIndex) in mensuelData.planification
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
        <div v-else-if="!mensuelData" class="ml-4 text-sm text-gray-500 italic">
            Aucune planification mensuelle définie
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
                        this.mensuelData = null;
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.mensuelData = newValue;
                } else {
                    this.mensuelData = null;
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
