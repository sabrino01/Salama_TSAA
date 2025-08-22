<!-- ModalSemestriel.vue -->
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
            class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 flex flex-col max-h-[90vh]"
        >
            <!-- Header -->
            <div class="py-4 px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="py-6 px-6 flex-grow overflow-y-auto">
                <!-- Sélection du type de menu -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type de planification
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                v-model="typeMenu"
                                value="dateHeure"
                                class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2">Date et heure</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                v-model="typeMenu"
                                value="dateHeureSemaine"
                                class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2">Date et heure par semaine</span>
                        </label>
                    </div>
                </div>

                <!-- Menu Date et Heure -->
                <div v-if="typeMenu === 'dateHeure'">
                    <!-- Date et heure de début -->
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Date et heure de début
                        </label>
                        <input
                            type="datetime-local"
                            v-model="planification.dateHeureDebut"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    <!-- Date et heure de fin (visible si début défini) -->
                    <div v-if="planification.dateHeureDebut" class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Date et heure de fin
                        </label>
                        <input
                            type="datetime-local"
                            v-model="planification.dateHeureFin"
                            :min="planification.dateHeureDebut"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    <!-- Suivis (visible si début et fin définis) -->
                    <div
                        v-if="
                            planification.dateHeureDebut &&
                            planification.dateHeureFin
                        "
                        class="mb-4"
                    >
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Dates et heures de suivi
                            </label>
                            <button
                                @click="ajouterSuivi"
                                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                            >
                                <Plus size="16" />
                            </button>
                        </div>

                        <div
                            v-for="(suivi, index) in planification.suivis"
                            :key="`suivi-${index}`"
                            class="mb-3 p-3 border border-gray-200 rounded-md"
                        >
                            <div class="mb-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Date et heure de suivi {{ index + 1 }}
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="suivi.dateHeure"
                                    :min="planification.dateHeureFin"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                />
                            </div>
                            <div class="flex justify-end">
                                <button
                                    @click="supprimerSuivi(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                                >
                                    <X size="16" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Date et Heure par Semaine -->
                <div v-if="typeMenu === 'dateHeureSemaine'">
                    <!-- Date et heure de début -->
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Date et heure de début
                        </label>
                        <input
                            type="datetime-local"
                            v-model="planificationSemaine.dateHeureDebut"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    <!-- Date et heure de fin (limitée au mois en cours) -->
                    <div
                        v-if="planificationSemaine.dateHeureDebut"
                        class="mb-4"
                    >
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Date et heure de fin (dans le mois en cours)
                        </label>
                        <input
                            type="datetime-local"
                            v-model="planificationSemaine.dateHeureFin"
                            :min="planificationSemaine.dateHeureDebut"
                            :max="finDuMois"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    <!-- Date et heure finale -->
                    <div
                        v-if="
                            planificationSemaine.dateHeureDebut &&
                            planificationSemaine.dateHeureFin
                        "
                        class="mb-4"
                    >
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Date et heure finale
                        </label>
                        <input
                            type="datetime-local"
                            v-model="planificationSemaine.dateHeureFinale"
                            :min="planificationSemaine.dateHeureFin"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    <!-- Suivis (visible si toutes les dates sont définies) -->
                    <div
                        v-if="
                            planificationSemaine.dateHeureDebut &&
                            planificationSemaine.dateHeureFin &&
                            planificationSemaine.dateHeureFinale
                        "
                        class="mb-4"
                    >
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Dates et heures de suivi
                            </label>
                            <button
                                @click="ajouterSuiviSemaine"
                                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                            >
                                <Plus size="16" />
                            </button>
                        </div>

                        <div
                            v-for="(
                                suivi, index
                            ) in planificationSemaine.suivis"
                            :key="`suivi-semaine-${index}`"
                            class="mb-3 p-3 border border-gray-200 rounded-md"
                        >
                            <div class="mb-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Date et heure de suivi {{ index + 1 }}
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="suivi.dateHeure"
                                    :min="planificationSemaine.dateHeureFinale"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                />
                            </div>
                            <div class="flex justify-end">
                                <button
                                    @click="supprimerSuiviSemaine(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                                >
                                    <X size="16" />
                                </button>
                            </div>
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
    name: "ModalSemestriel",
    components: {
        X,
        Plus,
    },
    props: {
        modelValue: Boolean,
        titre: {
            type: String,
            default: "Semestriel",
        },
    },
    emits: ["update:modelValue", "save"],
    data() {
        return {
            typeMenu: "", // 'dateHeure' ou 'dateHeureSemaine'
            // Données pour le menu "Date et heure"
            planification: {
                dateHeureDebut: "",
                dateHeureFin: "",
                suivis: [],
            },
            // Données pour le menu "Date et heure par semaine"
            planificationSemaine: {
                dateHeureDebut: "",
                dateHeureFin: "",
                dateHeureFinale: "",
                suivis: [],
            },
        };
    },
    computed: {
        estValide() {
            if (this.typeMenu === "dateHeure") {
                return (
                    this.planification.dateHeureDebut &&
                    this.planification.dateHeureFin
                );
            } else if (this.typeMenu === "dateHeureSemaine") {
                return (
                    this.planificationSemaine.dateHeureDebut &&
                    this.planificationSemaine.dateHeureFin &&
                    this.planificationSemaine.dateHeureFinale
                );
            }
            return false;
        },
        finDuMois() {
            if (!this.planificationSemaine.dateHeureDebut) return "";

            const dateDebut = new Date(
                this.planificationSemaine.dateHeureDebut
            );
            const annee = dateDebut.getFullYear();
            const mois = dateDebut.getMonth();

            // Dernier jour du mois
            const dernierJour = new Date(annee, mois + 1, 0);

            // Format pour input datetime-local
            const finMois = new Date(
                dernierJour.getFullYear(),
                dernierJour.getMonth(),
                dernierJour.getDate(),
                23,
                59
            );
            return finMois.toISOString().slice(0, 16);
        },
    },
    methods: {
        fermerModal() {
            this.$emit("update:modelValue", false);
            this.resetForm();
        },
        resetForm() {
            this.typeMenu = "";
            this.planification = {
                dateHeureDebut: "",
                dateHeureFin: "",
                suivis: [],
            };
            this.planificationSemaine = {
                dateHeureDebut: "",
                dateHeureFin: "",
                dateHeureFinale: "",
                suivis: [],
            };
        },
        ajouterSuivi() {
            this.planification.suivis.push({
                dateHeure: "",
            });
        },
        supprimerSuivi(index) {
            this.planification.suivis.splice(index, 1);
        },
        ajouterSuiviSemaine() {
            this.planificationSemaine.suivis.push({
                dateHeure: "",
            });
        },
        supprimerSuiviSemaine(index) {
            this.planificationSemaine.suivis.splice(index, 1);
        },
        enregistrer() {
            if (this.estValide) {
                const donnees = {
                    type: this.titre,
                    typeMenu: this.typeMenu,
                    planification:
                        this.typeMenu === "dateHeure"
                            ? { ...this.planification }
                            : { ...this.planificationSemaine },
                };
                this.$emit("save", donnees);
                this.fermerModal();
            }
        },
    },
    watch: {
        // Réinitialiser les données quand on change de type de menu
        typeMenu() {
            this.planification = {
                dateHeureDebut: "",
                dateHeureFin: "",
                suivis: [],
            };
            this.planificationSemaine = {
                dateHeureDebut: "",
                dateHeureFin: "",
                dateHeureFinale: "",
                suivis: [],
            };
        },
    },
};
</script>
