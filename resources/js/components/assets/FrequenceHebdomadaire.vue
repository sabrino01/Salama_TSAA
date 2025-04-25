<!-- FrequenceHebdomadaire.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option hebdomadaire -->
        <div
            v-if="
                hebdomadaireData &&
                (hebdomadaireData.dateHeure || hebdomadaireData.joursHeure)
            "
            class="ml-4 text-sm text-gray-700"
        >
            <!-- Affichage pour mode "Date et heure" -->
            <template
                v-if="
                    hebdomadaireData.mode === 'dateHeure' &&
                    hebdomadaireData.dateHeure
                "
            >
                <div
                    v-if="
                        hebdomadaireData.dateHeure.blocs &&
                        hebdomadaireData.dateHeure.blocs.length > 0
                    "
                >
                    <span class="font-medium">Dates et heures:</span>
                    <div
                        v-for="(bloc, bIndex) in hebdomadaireData.dateHeure
                            .blocs"
                        :key="`bloc-${bIndex}`"
                        class="ml-2 mb-1"
                    >
                        <template v-if="bloc.debut && bloc.fin">
                            Date et heure {{ bIndex + 1 }}:
                            {{ formatDate(bloc.debut) }} -
                            {{ formatDate(bloc.fin) }}
                        </template>
                    </div>
                </div>
                <div
                    v-if="
                        hebdomadaireData.dateHeure.suivis &&
                        hebdomadaireData.dateHeure.suivis.length > 0
                    "
                    class="mt-1"
                >
                    <span class="font-medium">Suivis:</span>
                    <div class="ml-2">
                        <span
                            v-for="(suivi, index) in hebdomadaireData.dateHeure
                                .suivis"
                            :key="`suivi-${index}`"
                        >
                            {{ index > 0 ? ", " : "" }}
                            suivi {{ index + 1 }}: {{ formatDate(suivi) }}
                        </span>
                    </div>
                </div>
            </template>

            <!-- Affichage pour mode "Jours et heure" -->
            <template
                v-if="
                    hebdomadaireData.mode === 'joursHeure' &&
                    hebdomadaireData.joursHeure
                "
            >
                <div>
                    <span class="font-medium">Jours:</span>
                    {{ hebdomadaireData.joursHeure.jours.join(", ") }}
                </div>
                <div>
                    <span class="font-medium">Heures:</span>
                    {{ hebdomadaireData.joursHeure.heureDebut }} -
                    {{ hebdomadaireData.joursHeure.heureFin }}
                </div>
                <div>
                    <span class="font-medium">Répétition:</span>
                    {{
                        hebdomadaireData.joursHeure.repetition === "uneFois"
                            ? "Une fois"
                            : "Toutes les semaines"
                    }}
                </div>
                <div
                    v-if="
                        hebdomadaireData.joursHeure.suivis &&
                        hebdomadaireData.joursHeure.suivis.length > 0
                    "
                >
                    <span class="font-medium">Suivis:</span>
                    <div
                        v-for="(suivi, sIndex) in hebdomadaireData.joursHeure
                            .suivis"
                        :key="`joursHeureSuivi-${sIndex}`"
                        class="ml-2 mb-1"
                    >
                        <template v-if="suivi.jours && suivi.jours.length > 0">
                            Suivi {{ sIndex + 1 }}:
                            {{ suivi.jours.join(", ") }} -
                            {{ suivi.heureDebut }} à {{ suivi.heureFin }}
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Modal pour l'option hebdomadaire -->
        <ModalHebdomadaire
            :modelValue="showModal"
            :titre="'Hebdomadaire'"
            @save="saveHebdomadaireData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalHebdomadaire from "./ModalHebdomadaire.vue";

export default {
    name: "FrequenceHebdomadaire",
    components: {
        ModalHebdomadaire,
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
            hebdomadaireData: null, // Données spécifiques à l'option hebdomadaire
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.hebdomadaireData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.hebdomadaireData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveHebdomadaireData(data) {
            this.hebdomadaireData = data;
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
