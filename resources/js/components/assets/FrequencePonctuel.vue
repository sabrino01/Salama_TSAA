<!-- FrequencePonctuel.vue -->
<template>
    <div class="relative w-full">
        <!-- Résumé des données pour l'option ponctuelle -->
        <div
            v-if="
                ponctuelData &&
                ponctuelData.creneaux &&
                ponctuelData.creneaux.length > 0
            "
            class="ml-4 text-sm text-gray-700 space-y-3"
        >
            <div
                v-for="(creneau, index) in ponctuelData.creneaux"
                :key="index"
                class="border-l-4 border-blue-500 pl-3 py-2 bg-gray-50 rounded-r"
            >
                <div class="font-medium text-gray-800 mb-1">
                    Créneau {{ index + 1 }}:
                </div>
                <div class="ml-2">
                    <div>
                        <span class="font-medium">Début:</span>
                        {{ formatDate(creneau.debut) }}
                    </div>
                    <div>
                        <span class="font-medium">Fin:</span>
                        {{ formatDate(creneau.fin) }}
                    </div>
                    <div
                        v-if="creneau.suivis && creneau.suivis.length > 0"
                        class="mt-1"
                    >
                        <span class="font-medium">Suivis:</span>
                        <div class="ml-2 text-xs">
                            <div
                                v-for="(suivi, suiviIndex) in creneau.suivis"
                                :key="suiviIndex"
                                class="mt-1"
                            >
                                • Suivi {{ suiviIndex + 1 }}:
                                {{ formatDate(suivi) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affichage si aucune donnée -->
        <div
            v-else-if="
                ponctuelData &&
                (!ponctuelData.creneaux || ponctuelData.creneaux.length === 0)
            "
            class="ml-4 text-sm text-gray-500 italic"
        >
            Aucun créneau configuré
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
    emits: ["update:modelValue", "update:showModal"],
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
                        this.ponctuelData = null;
                    }
                } else if (newValue && typeof newValue === "object") {
                    this.ponctuelData = newValue;
                } else {
                    this.ponctuelData = null;
                }
            },
            immediate: true,
        },
    },
    computed: {
        totalCreneaux() {
            return this.ponctuelData && this.ponctuelData.creneaux
                ? this.ponctuelData.creneaux.length
                : 0;
        },
        totalSuivis() {
            if (!this.ponctuelData || !this.ponctuelData.creneaux) return 0;
            return this.ponctuelData.creneaux.reduce((total, creneau) => {
                return total + (creneau.suivis ? creneau.suivis.length : 0);
            }, 0);
        },
    },
    methods: {
        savePonctuelData(data) {
            this.ponctuelData = data;
            this.$emit("update:modelValue", data);
        },
        updateShowModal(value) {
            this.$emit("update:showModal", value);
        },
        formatDate(dateString) {
            if (!dateString) return "";
            try {
                const date = new Date(dateString);
                // Vérifier si la date est valide
                if (isNaN(date.getTime())) return "Date invalide";

                const jour = date.getDate().toString().padStart(2, "0");
                const mois = (date.getMonth() + 1).toString().padStart(2, "0");
                const annee = date.getFullYear();
                const heures = date.getHours().toString().padStart(2, "0");
                const minutes = date.getMinutes().toString().padStart(2, "0");
                return `${jour}/${mois}/${annee} à ${heures}:${minutes}`;
            } catch (error) {
                return "Date invalide";
            }
        },
        // Méthodes utilitaires pour les statistiques
        getCreneauxCount() {
            return this.totalCreneaux;
        },
        getSuivisCount() {
            return this.totalSuivis;
        },
        // Méthode pour obtenir un résumé textuel
        getResume() {
            if (
                !this.ponctuelData ||
                !this.ponctuelData.creneaux ||
                this.ponctuelData.creneaux.length === 0
            ) {
                return "Aucun créneau configuré";
            }

            const nbCreneaux = this.totalCreneaux;
            const nbSuivis = this.totalSuivis;

            let resume = `${nbCreneaux} créneau${nbCreneaux > 1 ? "x" : ""}`;
            if (nbSuivis > 0) {
                resume += ` avec ${nbSuivis} suivi${nbSuivis > 1 ? "s" : ""}`;
            }

            return resume;
        },
    },
};
</script>
