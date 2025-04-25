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
            class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 flex flex-col"
        >
            <!-- Header -->
            <div class="py-4 px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="py-6 px-6 flex-grow overflow-y-auto">
                <!-- Menu de sélection -->
                <div class="flex border-b border-gray-200 mb-4">
                    <button
                        @click="activeTab = 'dateHeure'"
                        class="py-2 px-4 mr-4"
                        :class="{
                            'border-b-2 border-blue-500 font-medium':
                                activeTab === 'dateHeure',
                        }"
                    >
                        Date et heure
                    </button>
                    <button
                        @click="activeTab = 'joursHeure'"
                        class="py-2 px-4"
                        :class="{
                            'border-b-2 border-blue-500 font-medium':
                                activeTab === 'joursHeure',
                        }"
                    >
                        Jours et heure
                    </button>
                </div>

                <!-- Tab "Date et heure" -->
                <div v-if="activeTab === 'dateHeure'">
                    <!-- Liste des blocs de dates et heures -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Dates et heures
                            </label>
                            <button
                                @click="ajouterBlocDateHeure"
                                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                            >
                                <Plus size="16" />
                            </button>
                        </div>

                        <div
                            v-for="(bloc, index) in dateHeure.blocs"
                            :key="`bloc-${index}`"
                            class="mb-4 p-3 border border-gray-200 rounded-md"
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
                                    :min="dateActuelle"
                                    @change="checkDateValidity(index)"
                                />
                            </div>

                            <!-- Date et heure de fin (visible seulement si début est rempli) -->
                            <div v-if="bloc.debut" class="mb-3">
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

                    <!-- Suivis (visible seulement si au moins un bloc complet) -->
                    <div v-if="auMoinsUnBlocComplet">
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Dates et heures de suivi
                            </label>
                            <button
                                @click="ajouterSuiviDateHeure"
                                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                            >
                                <Plus size="16" />
                            </button>
                        </div>

                        <div
                            v-for="(suivi, index) in dateHeure.suivis"
                            :key="`date-${index}`"
                            class="flex mb-2"
                        >
                            <input
                                type="datetime-local"
                                v-model="dateHeure.suivis[index]"
                                class="flex-grow rounded-md border border-gray-300 px-3 py-2"
                                :min="derniereBlocFin"
                            />
                            <button
                                @click="supprimerSuiviDateHeure(index)"
                                class="ml-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                            >
                                <X size="16" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab "Jours et heure" -->
                <div v-if="activeTab === 'joursHeure'">
                    <!-- Sélection des jours de la semaine -->
                    <div class="mb-4">
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

                    <!-- Option de répétition (visible uniquement si heures sont remplies) -->
                    <div
                        v-if="
                            joursHeure.jours.length > 0 &&
                            joursHeure.heureDebut &&
                            joursHeure.heureFin
                        "
                        class="mb-4"
                    >
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Répétition
                        </label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="joursHeure.repetition"
                                    value="uneFois"
                                    class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2">Une fois</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="joursHeure.repetition"
                                    value="toutesLesSemaines"
                                    class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2">Toutes les semaines</span>
                            </label>
                        </div>
                    </div>

                    <!-- Suivis des jours et heures (visible uniquement si répétition est sélectionnée) -->
                    <div
                        v-if="
                            joursHeure.jours.length > 0 &&
                            joursHeure.heureDebut &&
                            joursHeure.heureFin &&
                            joursHeure.repetition
                        "
                        class="mb-4"
                    >
                        <div class="flex justify-between items-center mb-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
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
            activeTab: "dateHeure",
            jours: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"],
            dateHeure: {
                blocs: [{ debut: "", fin: "" }],
                suivis: [],
            },
            joursHeure: {
                jours: [],
                heureDebut: "",
                heureFin: "",
                repetition: "", // 'uneFois' ou 'toutesLesSemaines'
                suivis: [],
            },
            erreurs: [],
        };
    },
    computed: {
        estValide() {
            if (this.activeTab === "dateHeure") {
                return this.auMoinsUnBlocComplet;
            } else if (this.activeTab === "joursHeure") {
                return (
                    this.joursHeure.jours.length > 0 &&
                    this.joursHeure.heureDebut &&
                    this.joursHeure.heureFin &&
                    this.joursHeure.repetition
                );
            }
            return false;
        },
        auMoinsUnBlocComplet() {
            return this.dateHeure.blocs.some((bloc) => bloc.debut && bloc.fin);
        },
        derniereBlocFin() {
            const blocsComplets = this.dateHeure.blocs.filter(
                (bloc) => bloc.debut && bloc.fin
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
                mode: this.activeTab,
                dateHeure:
                    this.activeTab === "dateHeure"
                        ? { ...this.dateHeure }
                        : null,
                joursHeure:
                    this.activeTab === "joursHeure"
                        ? { ...this.joursHeure }
                        : null,
            };
        },
        dateActuelle() {
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
            this.activeTab = "dateHeure";
            this.dateHeure = {
                blocs: [{ debut: "", fin: "" }],
                suivis: [],
            };
            this.joursHeure = {
                jours: [],
                heureDebut: "",
                heureFin: "",
                repetition: "",
                suivis: [],
            };
            this.erreurs = [];
        },
        ajouterBlocDateHeure() {
            this.dateHeure.blocs.push({ debut: "", fin: "" });
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
