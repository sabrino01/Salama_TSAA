<!-- FrequenceAnnuel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option annuelle -->
        <div
            v-if="annuelData && (annuelData.dateHeure || annuelData.joursHeure)"
            class="ml-4 text-sm text-gray-700"
        >
            <!-- Affichage pour mode "Date et heure" -->
            <template
                v-if="annuelData.mode === 'dateHeure' && annuelData.dateHeure"
            >
                <div>
                    <span class="font-medium">Début:</span>
                    {{ formatDate(annuelData.dateHeure.debut) }}
                </div>
                <div>
                    <span class="font-medium">Fin:</span>
                    {{ formatDate(annuelData.dateHeure.fin) }}
                </div>
                <div
                    v-if="
                        annuelData.dateHeure.suivis &&
                        annuelData.dateHeure.suivis.length > 0
                    "
                >
                    <span class="font-medium">Suivis:</span>
                    <span
                        v-for="(suivi, index) in annuelData.dateHeure.suivis"
                        :key="index"
                    >
                        {{ index > 0 ? ", " : "" }}
                        suivi {{ index + 1 }}: {{ formatDate(suivi) }}
                    </span>
                </div>
            </template>

            <!-- Affichage pour mode "Jours et heure" -->
            <template
                v-if="annuelData.mode === 'joursHeure' && annuelData.joursHeure"
            >
                <div>
                    <span class="font-medium">Jours:</span>
                    {{ annuelData.joursHeure.jours.join(", ") }}
                </div>
                <div>
                    <span class="font-medium">Heures:</span>
                    {{ annuelData.joursHeure.heureDebut }} -
                    {{ annuelData.joursHeure.heureFin }}
                </div>
                <div>
                    <span class="font-medium">Période:</span>
                    {{
                        annuelData.joursHeure.periodeMois === "premier"
                            ? "Premier mois"
                            : "Dernier mois"
                    }}
                </div>
                <div>
                    <span class="font-medium">Répétition:</span>
                    {{
                        annuelData.joursHeure.frequence === "uneFois"
                            ? "Une fois"
                            : "Tous les ans"
                    }}
                </div>
                <div
                    v-if="
                        annuelData.joursHeure.suivis &&
                        annuelData.joursHeure.suivis.length > 0
                    "
                >
                    <span class="font-medium">Suivis:</span>
                    {{ annuelData.joursHeure.suivis.length }} suivi(s)
                </div>
            </template>
        </div>

        <!-- Modal pour l'option annuelle -->
        <ModalAnnuel
            :modelValue="showModal"
            :titre="'Annuel'"
            @save="saveAnnuelData"
            @update:modelValue="updateShowModal"
        />
    </div>
</template>

<script>
import ModalAnnuel from "./ModalAnnuel.vue";

export default {
    name: "FrequenceAnnuel",
    components: {
        ModalAnnuel,
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
            annuelData: null, // Données spécifiques à l'option annuelle
        };
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (typeof newValue === "string") {
                    try {
                        const parsed = JSON.parse(newValue);
                        if (parsed && parsed.type) {
                            this.annuelData = parsed;
                        }
                    } catch (e) {
                        // Si ce n'est pas un JSON valide, on ignore
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.annuelData = newValue;
                }
            },
            immediate: true,
        },
    },
    methods: {
        saveAnnuelData(data) {
            this.annuelData = data;
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
