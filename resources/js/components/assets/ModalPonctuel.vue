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
            class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 flex flex-col"
        >
            <!-- Header -->
            <div class="py-4 px-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-center">{{ titre }}</h2>
            </div>

            <!-- Body -->
            <div class="py-6 px-6 flex-grow overflow-y-auto">
                <!-- Date et heure du début -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Date et heure du début</label
                    >
                    <input
                        type="datetime-local"
                        v-model="debut"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                        :min="dateActuelle"
                        @change="checkDateValidity"
                    />
                </div>

                <!-- Date et heure de fin (visible seulement si début est rempli) -->
                <div v-if="debut" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Date et heure de fin</label
                    >
                    <input
                        type="datetime-local"
                        v-model="fin"
                        class="w-full rounded-md border border-gray-300 px-3 py-2"
                        :min="debut"
                        @change="checkDateValidity"
                    />
                </div>

                <!-- Suivis (visible seulement si fin est rempli) -->
                <div v-if="fin && debut">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700"
                            >Dates et heures de suivi</label
                        >
                        <button
                            @click="ajouterSuivi"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm"
                        >
                            <Plus size="16" />
                        </button>
                    </div>

                    <div
                        v-for="(suivi, index) in suivis"
                        :key="index"
                        class="flex mb-2"
                    >
                        <input
                            type="datetime-local"
                            v-model="suivis[index]"
                            class="flex-grow rounded-md border border-gray-300 px-3 py-2"
                            :min="fin"
                        />
                        <button
                            @click="supprimerSuivi(index)"
                            class="ml-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                        >
                            <X size="16" />
                        </button>
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
            debut: "",
            fin: "",
            suivis: [],
            erreurs: [],
        };
    },
    computed: {
        estValide() {
            return this.debut && this.fin;
        },
        donneesPonctuelles() {
            return {
                type: this.titre,
                debut: this.debut,
                fin: this.fin,
                suivis: [...this.suivis],
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
            this.debut = "";
            this.fin = "";
            this.suivis = [];
            this.erreurs = [];
        },
        ajouterSuivi() {
            this.suivis.push("");
        },
        supprimerSuivi(index) {
            this.suivis.splice(index, 1);
        },
        checkDateValidity() {
            if (this.debut && this.fin) {
                if (new Date(this.fin) <= new Date(this.debut)) {
                    this.fin = "";
                    this.erreurs.push(
                        "La date de fin doit être postérieure à la date de début"
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
