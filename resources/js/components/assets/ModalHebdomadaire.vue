<!-- ModalHebdomadaire.vue -->
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
            class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 flex flex-col max-h-[90vh]"
        >
            <!-- Header -->
            <div class="py-4 px-6 border-b border-gray-200 flex-shrink-0">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="py-6 px-6 flex-grow overflow-y-auto min-h-0">
                <!-- Liste des blocs de dates et heures -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Dates et heures
                        </label>
                        <button
                            @click="ajouterBlocDateHeure"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm flex-shrink-0"
                        >
                            <Plus size="16" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(bloc, index) in dateHeure.blocs"
                            :key="`bloc-${index}`"
                            class="p-3 border border-gray-200 rounded-md"
                        >
                            <!-- Date et heure du début -->
                            <div class="mb-3">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Date et heure du début
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="bloc.debut"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                    @change="checkDateValidity(index)"
                                />
                            </div>

                            <!-- Sélection des jours de la semaine (visible seulement si début est rempli) -->
                            <div v-if="bloc.debut" class="mb-3">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Jours de la semaine
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <label
                                        v-for="jour in jours"
                                        :key="jour"
                                        class="inline-flex items-center"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="bloc.jours"
                                            :value="jour"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-1"
                                        />
                                        <span class="ml-1">{{ jour }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Date et heure de fin (visible seulement si début est rempli et au moins un jour sélectionné) -->
                            <div
                                v-if="
                                    bloc.debut &&
                                    bloc.jours &&
                                    bloc.jours.length > 0
                                "
                                class="mb-3"
                            >
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                >
                                    Date et heure de fin
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="bloc.fin"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2"
                                    :min="bloc.debut"
                                    @change="checkDateValidity(index)"
                                />
                            </div>

                            <!-- Bouton pour supprimer le bloc -->
                            <div class="flex justify-end">
                                <button
                                    @click="supprimerBlocDateHeure(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm"
                                    :disabled="dateHeure.blocs.length === 1"
                                >
                                    <X size="16" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suivis (visible seulement si au moins un bloc complet) -->
                <div v-if="auMoinsUnBlocComplet">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Dates et heures de suivi
                        </label>
                        <button
                            @click="ajouterSuiviDateHeure"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm flex-shrink-0"
                        >
                            <Plus size="16" />
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(suivi, index) in dateHeure.suivis"
                            :key="`date-${index}`"
                            class="flex"
                        >
                            <input
                                type="datetime-local"
                                v-model="dateHeure.suivis[index]"
                                class="flex-grow rounded-md border border-gray-300 px-3 py-2"
                            />
                            <button
                                @click="supprimerSuiviDateHeure(index)"
                                class="ml-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex-shrink-0"
                            >
                                <X size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="py-3 px-6 border-t border-gray-200 flex justify-end space-x-4 flex-shrink-0"
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
    name: "ModalHebdomadaire",
    components: {
        X,
        Plus,
    },
    props: {
        modelValue: Boolean,
        titre: {
            type: String,
            default: "Hebdomadaire",
        },
    },
    emits: ["update:modelValue", "save"],
    data() {
        return {
            jours: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"],
            dateHeure: {
                blocs: [{ debut: "", fin: "", jours: [] }],
                suivis: [],
            },
            erreurs: [],
        };
    },
    computed: {
        estValide() {
            return this.auMoinsUnBlocComplet;
        },
        auMoinsUnBlocComplet() {
            return this.dateHeure.blocs.some(
                (bloc) =>
                    bloc.debut &&
                    bloc.fin &&
                    bloc.jours &&
                    bloc.jours.length > 0
            );
        },
        derniereBlocFin() {
            const blocsComplets = this.dateHeure.blocs.filter(
                (bloc) =>
                    bloc.debut &&
                    bloc.fin &&
                    bloc.jours &&
                    bloc.jours.length > 0
            );
            if (blocsComplets.length === 0) return null;

            let derniereDate = blocsComplets[0].fin;
            blocsComplets.forEach((bloc) => {
                if (new Date(bloc.fin) > new Date(derniereDate)) {
                    derniereDate = bloc.fin;
                }
            });

            return derniereDate;
        },
        donneesHebdomadaire() {
            return {
                type: this.titre,
                mode: "dateHeure",
                dateHeure: { ...this.dateHeure },
            };
        },
        dateActuelle() {
            // Cette méthode n'est plus utilisée pour limiter les dates
            // mais conservée au cas où elle serait nécessaire ailleurs
            const maintenant = new Date();
            const annee = maintenant.getFullYear();
            const mois = (maintenant.getMonth() + 1)
                .toString()
                .padStart(2, "0");
            const jour = maintenant.getDate().toString().padStart(2, "0");
            const heures = maintenant.getHours().toString().padStart(2, "0");
            const minutes = maintenant.getMinutes().toString().padStart(2, "0");
            return `${annee}-${mois}-${jour}T${heures}:${minutes}`;
        },
    },
    methods: {
        fermerModal() {
            this.$emit("update:modelValue", false);
            this.resetForm();
        },
        resetForm() {
            this.dateHeure = {
                blocs: [{ debut: "", fin: "", jours: [] }],
                suivis: [],
            };
            this.erreurs = [];
        },
        ajouterBlocDateHeure() {
            this.dateHeure.blocs.push({ debut: "", fin: "", jours: [] });
        },
        supprimerBlocDateHeure(index) {
            if (this.dateHeure.blocs.length > 1) {
                this.dateHeure.blocs.splice(index, 1);
            }
        },
        ajouterSuiviDateHeure() {
            this.dateHeure.suivis.push("");
        },
        supprimerSuiviDateHeure(index) {
            this.dateHeure.suivis.splice(index, 1);
        },
        checkDateValidity(index) {
            const bloc = this.dateHeure.blocs[index];
            if (bloc.debut && bloc.fin) {
                if (new Date(bloc.fin) <= new Date(bloc.debut)) {
                    bloc.fin = "";
                    this.erreurs.push(
                        "La date de fin doit être postérieure à la date de début"
                    );
                }
            }
        },
        enregistrer() {
            if (this.estValide) {
                const donnees = this.donneesHebdomadaire;
                this.$emit("save", donnees);
                this.fermerModal();
            }
        },
    },
};
</script>
