<!-- ModalQuotidien.vue -->
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
                <!-- Heures principales -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Heure de début</label
                        >
                        <input
                            type="time"
                            v-model="heureDebut"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Heure de fin</label
                        >
                        <input
                            type="time"
                            v-model="heureFin"
                            class="w-full rounded-md border border-gray-300 px-3 py-2"
                            :min="heureDebut"
                        />
                    </div>
                </div>

                <!-- Jours de la semaine (visible seulement si heures définies) -->
                <div v-if="heureDebut && heureFin" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Jours d'application</label
                    >
                    <div class="grid grid-cols-5 gap-4">
                        <div v-for="jour in jours" :key="jour" class="mb-2">
                            <label class="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="joursSelectionnes[jour]"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @change="handleJourChange(jour)"
                                />
                                <span class="ml-2">{{ jour }}</span>
                            </label>

                            <!-- Heures spécifiques si le jour est décoché -->
                            <div
                                v-if="!joursSelectionnes[jour]"
                                class="mt-2 grid grid-cols-2 gap-2"
                            >
                                <input
                                    type="time"
                                    v-model="heuresSpecifiques[jour].debut"
                                    class="rounded-md border border-gray-300 px-2 py-1 text-sm"
                                    placeholder="Début"
                                />
                                <input
                                    type="time"
                                    v-model="heuresSpecifiques[jour].fin"
                                    class="rounded-md border border-gray-300 px-2 py-1 text-sm"
                                    placeholder="Fin"
                                    :min="heuresSpecifiques[jour].debut"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suivis (visible seulement si jours définis) -->
                <div
                    v-if="heureDebut && heureFin && hasSelectedDays"
                    class="mt-4"
                >
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700"
                            >Suivis (optionnel)</label
                        >
                    </div>

                    <div class="border border-gray-200 rounded-md p-4">
                        <div
                            v-for="jour in jours"
                            :key="`suivi-${jour}`"
                            class="mb-3"
                        >
                            <label class="inline-flex items-center mb-2">
                                <input
                                    type="checkbox"
                                    v-model="suivis[jour].active"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2 font-medium">{{ jour }}</span>
                            </label>

                            <!-- Heures de suivi si le jour est coché -->
                            <div v-if="suivis[jour].active" class="pl-6">
                                <div
                                    v-for="(periode, index) in suivis[jour]
                                        .periodes"
                                    :key="`periode-${jour}-${index}`"
                                    class="flex items-center mb-2"
                                >
                                    <input
                                        type="time"
                                        v-model="periode.debut"
                                        class="rounded-md border border-gray-300 px-3 py-1"
                                        placeholder="Début"
                                    />
                                    <span class="mx-2">-</span>
                                    <input
                                        type="time"
                                        v-model="periode.fin"
                                        class="rounded-md border border-gray-300 px-3 py-1"
                                        placeholder="Fin"
                                        :min="periode.debut"
                                    />
                                    <button
                                        @click="supprimerPeriode(jour, index)"
                                        class="ml-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                                    >
                                        <X size="16" />
                                    </button>
                                </div>
                                <button
                                    @click="ajouterPeriode(jour)"
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm mt-1"
                                >
                                    <Plus size="16" />
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
    name: "ModalQuotidien",
    components: {
        X,
        Plus,
    },
    props: {
        modelValue: Boolean,
        titre: {
            type: String,
            default: "Quotidien",
        },
    },
    emits: ["update:modelValue", "save"],
    data() {
        return {
            jours: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"],
            heureDebut: "",
            heureFin: "",
            joursSelectionnes: {
                Lundi: true,
                Mardi: true,
                Mercredi: true,
                Jeudi: true,
                Vendredi: true,
            },
            heuresSpecifiques: {
                Lundi: { debut: "", fin: "" },
                Mardi: { debut: "", fin: "" },
                Mercredi: { debut: "", fin: "" },
                Jeudi: { debut: "", fin: "" },
                Vendredi: { debut: "", fin: "" },
            },
            suivis: {
                Lundi: { active: false, periodes: [] },
                Mardi: { active: false, periodes: [] },
                Mercredi: { active: false, periodes: [] },
                Jeudi: { active: false, periodes: [] },
                Vendredi: { active: false, periodes: [] },
            },
        };
    },
    computed: {
        estValide() {
            return this.heureDebut && this.heureFin && this.hasSelectedDays;
        },
        hasSelectedDays() {
            return (
                Object.values(this.joursSelectionnes).some(
                    (selected) => selected
                ) ||
                Object.values(this.heuresSpecifiques).some(
                    (heure) => heure.debut && heure.fin
                )
            );
        },
        donneesQuotidien() {
            const donnees = {
                type: this.titre,
                heuresPrincipales: {
                    debut: this.heureDebut,
                    fin: this.heureFin,
                },
                jours: {},
            };

            // Pour chaque jour, on stocke s'il est sélectionné et ses heures spécifiques si nécessaire
            for (const jour of this.jours) {
                donnees.jours[jour] = {
                    selectionne: this.joursSelectionnes[jour],
                    heures: !this.joursSelectionnes[jour]
                        ? {
                              debut: this.heuresSpecifiques[jour].debut,
                              fin: this.heuresSpecifiques[jour].fin,
                          }
                        : { debut: this.heureDebut, fin: this.heureFin },
                };
            }

            // Traitement des suivis
            donnees.suivis = {};
            for (const jour of this.jours) {
                if (
                    this.suivis[jour].active &&
                    this.suivis[jour].periodes.length > 0
                ) {
                    donnees.suivis[jour] = this.suivis[jour].periodes.filter(
                        (periode) => periode.debut && periode.fin
                    );
                }
            }

            return donnees;
        },
    },
    methods: {
        fermerModal() {
            this.$emit("update:modelValue", false);
            this.resetForm();
        },
        resetForm() {
            this.heureDebut = "";
            this.heureFin = "";
            this.jours.forEach((jour) => {
                this.joursSelectionnes[jour] = true;
                this.heuresSpecifiques[jour] = { debut: "", fin: "" };
                this.suivis[jour] = { active: false, periodes: [] };
            });
        },
        handleJourChange(jour) {
            if (!this.joursSelectionnes[jour]) {
                // Si jour décoché, initialiser avec les heures principales
                this.heuresSpecifiques[jour].debut = this.heureDebut;
                this.heuresSpecifiques[jour].fin = this.heureFin;
            }
        },
        ajouterPeriode(jour) {
            this.suivis[jour].periodes.push({ debut: "", fin: "" });
        },
        supprimerPeriode(jour, index) {
            this.suivis[jour].periodes.splice(index, 1);
        },
        enregistrer() {
            if (this.estValide) {
                const donnees = this.donneesQuotidien;
                this.$emit("save", donnees);
                this.fermerModal();
            }
        },
    },
    watch: {
        heureDebut(newVal) {
            if (newVal) {
                // Mettre à jour les heures spécifiques des jours non sélectionnés
                for (const jour of this.jours) {
                    if (
                        !this.joursSelectionnes[jour] &&
                        !this.heuresSpecifiques[jour].debut
                    ) {
                        this.heuresSpecifiques[jour].debut = newVal;
                    }
                }
            }
        },
        heureFin(newVal) {
            if (newVal) {
                // Mettre à jour les heures spécifiques des jours non sélectionnés
                for (const jour of this.jours) {
                    if (
                        !this.joursSelectionnes[jour] &&
                        !this.heuresSpecifiques[jour].fin
                    ) {
                        this.heuresSpecifiques[jour].fin = newVal;
                    }
                }
            }
        },
    },
};
</script>
