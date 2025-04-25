<!-- ModalQuadrimestriel.vue -->
<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <!-- Overlay -->
        <div
            class="fixed inset-0 bg-black opacity-50"
            @click="fermerModal"
        ></div>

        <!-- Modal -->
        <div
            class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 flex flex-col"
        >
            <!-- Header -->
            <div class="py-4 px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="py-6 px-6 flex-grow overflow-y-auto">
                <!-- Sélection des jours de la semaine -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choix du jours
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="jour in jours"
                            :key="jour"
                            class="inline-flex items-center"
                        >
                            <input
                                type="checkbox"
                                v-model="joursHeure.jours"
                                :value="jour"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-1"
                            />
                            <span class="ml-1">{{ jour }}</span>
                        </label>
                    </div>
                </div>

                <!-- Heure de début et fin (visible uniquement si au moins un jour est sélectionné) -->
                <div
                    v-if="joursHeure.jours.length > 0"
                    class="grid grid-cols-2 gap-4 mb-4"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Heure de début
                        </label>
                        <input
                            type="time"
                            v-model="joursHeure.heureDebut"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Heure de fin
                        </label>
                        <input
                            type="time"
                            v-model="joursHeure.heureFin"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                            :min="joursHeure.heureDebut"
                        />
                    </div>
                </div>

                <!-- Champ pour définir la période -->
                <div
                    v-if="
                        joursHeure.jours.length > 0 &&
                        joursHeure.heureDebut &&
                        joursHeure.heureFin
                    "
                    class="mb-4"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Définir la période
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                v-model="periode"
                                value="cetteSemaine"
                                class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2">Dès cette semaine</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                v-model="periode"
                                value="semaineProchaine"
                                class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2">Semaine prochaine</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                v-model="periode"
                                value="moisProchain"
                                class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2">Le mois prochain</span>
                        </label>
                    </div>
                </div>

                <!-- Suivis des jours et heures (visible uniquement si période est définie) -->
                <div
                    v-if="
                        joursHeure.jours.length > 0 &&
                        joursHeure.heureDebut &&
                        joursHeure.heureFin &&
                        periode
                    "
                    class="mb-4"
                >
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Jours et heures de suivi
                        </label>
                        <button
                            @click="ajouterSuiviJoursHeure"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                        >
                            <Plus size="16" />
                        </button>
                    </div>

                    <div
                        v-for="(suivi, index) in joursHeure.suivis"
                        :key="`jour-${index}`"
                        class="mb-3 p-2 border border-gray-200 rounded-md"
                    >
                        <div class="flex flex-wrap gap-2 mb-2">
                            <label
                                v-for="jour in jours"
                                :key="`suivi-${index}-${jour}`"
                                class="inline-flex items-center"
                            >
                                <input
                                    type="checkbox"
                                    v-model="suivi.jours"
                                    :value="jour"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-1"
                                />
                                <span class="ml-1">{{ jour }}</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Heure de début
                                </label>
                                <input
                                    type="time"
                                    v-model="suivi.heureDebut"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Heure de fin
                                </label>
                                <input
                                    type="time"
                                    v-model="suivi.heureFin"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button
                                @click="supprimerSuiviJoursHeure(index)"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                            >
                                <X size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="py-3 px-6 border-t border-gray-200 flex justify-end space-x-4"
            >
                <button
                    @click="fermerModal"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100"
                >
                    Annuler
                </button>
                <button
                    @click="enregistrer"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                    :disabled="!estValide"
                >
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { Plus, X } from "lucide-vue-next";

export default {
    name: "ModalQuadrimestriel",
    components: {
        X,
        Plus,
    },
    props: {
        modelValue: Boolean,
        titre: {
            type: String,
            default: "Quadrimestriel",
        },
    },
    emits: ["update:modelValue", "save"],
    data() {
        return {
            jours: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"],
            joursHeure: {
                jours: [],
                heureDebut: "",
                heureFin: "",
                suivis: [],
            },
            periode: "", // Stocke la période sélectionnée
        };
    },
    computed: {
        estValide() {
            return (
                this.joursHeure.jours.length > 0 &&
                this.joursHeure.heureDebut &&
                this.joursHeure.heureFin &&
                this.periode // Vérifie que la période est définie
            );
        },
    },
    methods: {
        fermerModal() {
            this.$emit("update:modelValue", false);
            this.resetForm();
        },
        resetForm() {
            this.joursHeure = {
                jours: [],
                heureDebut: "",
                heureFin: "",
                suivis: [],
            };
            this.periode = ""; // Réinitialise la période
        },
        ajouterSuiviJoursHeure() {
            this.joursHeure.suivis.push({
                jours: [],
                heureDebut: "",
                heureFin: "",
            });
        },
        supprimerSuiviJoursHeure(index) {
            this.joursHeure.suivis.splice(index, 1);
        },
        enregistrer() {
            if (this.estValide) {
                const donnees = {
                    type: this.titre,
                    joursHeure: { ...this.joursHeure },
                    periode: this.periode, // Inclut la période dans les données
                };
                this.$emit("save", donnees);
                this.fermerModal();
            }
        },
    },
};
</script>
