<!-- ModalPonctuel.vue -->
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
            <div class="py-4 px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-hidden flex flex-col min-h-0">
                <div class="py-6 px-6 overflow-y-auto flex-1 custom-scrollbar">
                    <!-- Créneaux principaux (début/fin) -->
                    <div class="mb-6">
                        <div
                            class="flex justify-between items-center mb-3 sticky top-0 bg-white py-2 z-10 border-b border-gray-100"
                        >
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Créneaux horaires ({{
                                    creneaux.length
                                }})</label
                            >
                            <button
                                @click="ajouterCreneau"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center gap-1 transition-colors"
                            >
                                <Plus size="16" />
                                Ajouter un créneau
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(creneau, index) in creneaux"
                                :key="index"
                                class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors"
                            >
                                <div
                                    class="flex justify-between items-center mb-3"
                                >
                                    <h4
                                        class="text-sm font-medium text-gray-700 flex items-center gap-2"
                                    >
                                        <span
                                            class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"
                                        >
                                            {{ index + 1 }}
                                        </span>
                                        Créneau {{ index + 1 }}
                                    </h4>
                                    <button
                                        v-if="creneaux.length > 1"
                                        @click="supprimerCreneau(index)"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm transition-colors"
                                        title="Supprimer ce créneau"
                                    >
                                        <X size="14" />
                                    </button>
                                </div>

                                <!-- Date et heure de début -->
                                <div class="mb-3">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Date et heure de début</label
                                    >
                                    <input
                                        type="datetime-local"
                                        v-model="creneau.debut"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        @change="
                                            checkDateValidityForCreneau(index)
                                        "
                                    />
                                </div>

                                <!-- Date et heure de fin -->
                                <div class="mb-3">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Date et heure de fin</label
                                    >
                                    <input
                                        type="datetime-local"
                                        v-model="creneau.fin"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :min="creneau.debut"
                                        @change="
                                            checkDateValidityForCreneau(index)
                                        "
                                    />
                                </div>

                                <!-- Suivis pour ce créneau (visible seulement si début et fin sont remplis) -->
                                <div
                                    v-if="creneau.debut && creneau.fin"
                                    class="mt-4 border-t pt-3"
                                >
                                    <div
                                        class="flex justify-between items-center mb-2"
                                    >
                                        <label
                                            class="block text-sm font-medium text-gray-700 flex items-center gap-1"
                                        >
                                            <span
                                                >Dates et heures de suivi</span
                                            >
                                            <span
                                                v-if="creneau.suivis.length > 0"
                                                class="bg-green-500 text-white text-xs px-2 py-0.5 rounded-full"
                                            >
                                                {{ creneau.suivis.length }}
                                            </span>
                                        </label>
                                        <button
                                            @click="ajouterSuivi(index)"
                                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm transition-colors"
                                            title="Ajouter un suivi"
                                        >
                                            <Plus size="14" />
                                        </button>
                                    </div>

                                    <div
                                        class="space-y-2 max-h-40 overflow-y-auto"
                                    >
                                        <div
                                            v-for="(
                                                suivi, suiviIndex
                                            ) in creneau.suivis"
                                            :key="suiviIndex"
                                            class="flex gap-2"
                                        >
                                            <input
                                                type="datetime-local"
                                                v-model="
                                                    creneau.suivis[suiviIndex]
                                                "
                                                class="flex-grow rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                                :min="creneau.fin"
                                                :placeholder="`Suivi ${
                                                    suiviIndex + 1
                                                }`"
                                            />
                                            <button
                                                @click="
                                                    supprimerSuivi(
                                                        index,
                                                        suiviIndex
                                                    )
                                                "
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition-colors"
                                                title="Supprimer ce suivi"
                                            >
                                                <X size="14" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="py-3 px-6 border-t border-gray-200 flex justify-between items-center bg-white"
            >
                <div class="text-sm text-gray-600">
                    {{ creneaux.length }} créneau{{
                        creneaux.length > 1 ? "x" : ""
                    }}
                    • {{ totalSuivis }} suivi{{ totalSuivis > 1 ? "s" : "" }}
                </div>
                <div class="flex space-x-4">
                    <button
                        @click="fermerModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                        Annuler
                    </button>
                    <button
                        @click="enregistrer"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                        :disabled="!estValide"
                        :class="{ 'opacity-50 cursor-not-allowed': !estValide }"
                    >
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Plus, X } from "lucide-vue-next";

export default {
    name: "ModalPonctuel",
    components: {
        X,
        Plus,
    },
    props: {
        modelValue: Boolean,
        titre: {
            type: String,
            default: "Ponctuel",
        },
    },
    emits: ["update:modelValue", "save"],
    data() {
        return {
            creneaux: [
                {
                    debut: "",
                    fin: "",
                    suivis: [],
                },
            ],
            erreurs: [],
        };
    },
    computed: {
        estValide() {
            return this.creneaux.some(
                (creneau) => creneau.debut && creneau.fin
            );
        },
        donneesPonctuelles() {
            return {
                type: this.titre,
                creneaux: this.creneaux.filter(
                    (creneau) => creneau.debut && creneau.fin
                ),
            };
        },
        totalSuivis() {
            return this.creneaux.reduce((total, creneau) => {
                return total + (creneau.suivis ? creneau.suivis.length : 0);
            }, 0);
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
            this.creneaux = [
                {
                    debut: "",
                    fin: "",
                    suivis: [],
                },
            ];
            this.erreurs = [];
        },
        ajouterCreneau() {
            this.creneaux.push({
                debut: "",
                fin: "",
                suivis: [],
            });
        },
        supprimerCreneau(index) {
            if (this.creneaux.length > 1) {
                this.creneaux.splice(index, 1);
            }
        },
        ajouterSuivi(creneauIndex) {
            this.creneaux[creneauIndex].suivis.push("");
        },
        supprimerSuivi(creneauIndex, suiviIndex) {
            this.creneaux[creneauIndex].suivis.splice(suiviIndex, 1);
        },
        checkDateValidityForCreneau(creneauIndex) {
            const creneau = this.creneaux[creneauIndex];
            if (creneau.debut && creneau.fin) {
                if (new Date(creneau.fin) <= new Date(creneau.debut)) {
                    creneau.fin = "";
                    this.erreurs.push(
                        `Créneau ${
                            creneauIndex + 1
                        }: La date de fin doit être postérieure à la date de début`
                    );
                }
            }
        },
        enregistrer() {
            if (this.estValide) {
                const donnees = this.donneesPonctuelles;
                this.$emit("save", donnees);
                this.fermerModal();
            }
        },
    },
};
</script>

<style scoped>
/* Scrollbar personnalisée pour une meilleure UX */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation pour les nouveaux créneaux */
.creneau-enter-active {
    transition: all 0.3s ease;
}

.creneau-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
