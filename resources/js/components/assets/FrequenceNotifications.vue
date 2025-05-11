<template>
    <div>
        <div v-if="frequenceObj">
            <div v-if="typeof frequenceObj === 'string'">
                <!-- Cas simple: Tout l'année -->
                <span class="px-2 py-1 rounded font-semibold">{{
                    frequenceObj
                }}</span>
            </div>

            <div v-else-if="frequenceObj.type === 'Tout l\'année'">
                <!-- Cas simple: Tout l'année -->
                <span class="px-2 py-1 rounded font-semibold"
                    >Tout l'année</span
                >
            </div>
            <div v-else-if="frequenceObj.type === 'Quotidien'">
                <!-- Cas simple: Aucun -->
                <span class="px-2 py-1 rounded font-semibold">Quotidien</span>
            </div>

            <!-- Ponctuel -->
            <div
                v-else-if="frequenceObj.type === 'Ponctuel'"
                class="flex flex-col"
            >
                <span class="font-medium">{{ frequenceObj.type }}</span>
                <div
                    :class="[
                        'px-2 py-1 rounded mt-1',
                        getBorderClass(
                            calculerJoursRestants(frequenceObj.debut)
                        ),
                    ]"
                >
                    Début:
                    {{
                        formatJoursRestants(
                            calculerJoursRestants(frequenceObj.debut)
                        )
                    }}
                </div>
                <div
                    v-if="frequenceObj.suivis && frequenceObj.suivis.length > 0"
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <span
                        v-for="(suivi, index) in frequenceObj.suivis"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(calculerJoursRestants(suivi)),
                        ]"
                    >
                        {{ formatJoursRestants(calculerJoursRestants(suivi)) }}
                    </span>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>

            <!-- Hebdomadaire mode dateHeure -->
            <div
                v-else-if="
                    frequenceObj.type === 'Hebdomadaire' &&
                    frequenceObj.mode === 'dateHeure'
                "
                class="flex flex-col"
            >
                <span class="font-medium">{{ frequenceObj.type }}</span>
                <div
                    v-if="
                        frequenceObj.dateHeure && frequenceObj.dateHeure.blocs
                    "
                >
                    <div
                        v-for="(bloc, index) in frequenceObj.dateHeure.blocs"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getBorderClass(calculerJoursRestants(bloc.debut)),
                        ]"
                    >
                        Début:
                        {{
                            formatJoursRestants(
                                calculerJoursRestants(bloc.debut)
                            )
                        }}
                    </div>
                </div>
                <div
                    v-if="
                        frequenceObj.dateHeure &&
                        frequenceObj.dateHeure.suivis &&
                        frequenceObj.dateHeure.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <span
                        v-for="(suivi, index) in frequenceObj.dateHeure.suivis"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(calculerJoursRestants(suivi)),
                        ]"
                    >
                        {{ formatJoursRestants(calculerJoursRestants(suivi)) }}
                    </span>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>

            <!-- Hebdomadaire mode joursHeure -->
            <div
                v-else-if="
                    frequenceObj.type === 'Hebdomadaire' &&
                    frequenceObj.mode === 'joursHeure'
                "
                class="flex flex-col"
            >
                <span class="font-medium"
                    >{{ frequenceObj.type }} -
                    {{ frequenceObj.joursHeure.repetition }}</span
                >
                <div class="mt-1">
                    <span
                        v-for="(jour, index) in frequenceObj.joursHeure.jours"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(calculerJoursRestantsParJour(jour)),
                        ]"
                    >
                        {{ jour }}
                        {{
                            formatJoursRestants(
                                calculerJoursRestantsParJour(jour)
                            )
                        }}
                    </span>
                </div>
                <div
                    v-if="
                        frequenceObj.joursHeure.suivis &&
                        frequenceObj.joursHeure.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <div
                        v-for="(suivi, indexSuivi) in frequenceObj.joursHeure
                            .suivis"
                        :key="indexSuivi"
                        class="mt-1"
                    >
                        <span
                            v-for="(jour, index) in suivi.jours"
                            :key="index"
                            :class="[
                                'px-2 py-1 rounded mx-1',
                                getBorderClass(
                                    calculerJoursRestantsParJour(jour, 2)
                                ),
                            ]"
                        >
                            {{ jour }}
                            {{
                                formatJoursRestants(
                                    calculerJoursRestantsParJour(jour, 2)
                                )
                            }}
                        </span>
                    </div>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>

            <!-- Annuel mode dateHeure -->
            <div
                v-else-if="
                    frequenceObj.type === 'Annuel' &&
                    frequenceObj.mode === 'dateHeure'
                "
                class="flex flex-col"
            >
                <span class="font-medium">{{ frequenceObj.type }}</span>
                <div
                    v-if="frequenceObj.dateHeure"
                    :class="[
                        'px-2 py-1 rounded mt-1',
                        getBorderClass(
                            calculerJoursRestants(frequenceObj.dateHeure.debut)
                        ),
                    ]"
                >
                    Début:
                    {{
                        formatJoursRestants(
                            calculerJoursRestants(frequenceObj.dateHeure.debut)
                        )
                    }}
                </div>
                <div
                    v-if="
                        frequenceObj.dateHeure &&
                        frequenceObj.dateHeure.suivis &&
                        frequenceObj.dateHeure.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <span
                        v-for="(suivi, index) in frequenceObj.dateHeure.suivis"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(calculerJoursRestants(suivi)),
                        ]"
                    >
                        {{ formatJoursRestants(calculerJoursRestants(suivi)) }}
                    </span>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>

            <!-- Annuel mode joursHeure -->
            <div
                v-else-if="
                    frequenceObj.type === 'Annuel' &&
                    frequenceObj.mode === 'joursHeure'
                "
                class="flex flex-col"
            >
                <span class="font-medium"
                    >{{ frequenceObj.type }} -
                    {{ frequenceObj.joursHeure.periodeMois }} -
                    {{ frequenceObj.joursHeure.frequence }}</span
                >
                <div class="mt-1">
                    <span
                        v-for="(jour, index) in frequenceObj.joursHeure.jours"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(calculerJoursRestantsParJour(jour)),
                        ]"
                    >
                        {{ jour }}
                        {{
                            formatJoursRestants(
                                calculerJoursRestantsParJour(jour)
                            )
                        }}
                    </span>
                </div>
                <div
                    v-if="
                        frequenceObj.joursHeure.suivis &&
                        frequenceObj.joursHeure.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <div
                        v-for="(suivi, indexSuivi) in frequenceObj.joursHeure
                            .suivis"
                        :key="indexSuivi"
                        class="mt-1"
                    >
                        <span
                            v-for="(jour, index) in suivi.jours"
                            :key="index"
                            :class="[
                                'px-2 py-1 rounded mx-1',
                                getBorderClass(
                                    calculerJoursRestantsParJour(jour, 2)
                                ),
                            ]"
                        >
                            {{ jour }}
                            {{
                                formatJoursRestants(
                                    calculerJoursRestantsParJour(jour, 2)
                                )
                            }}
                        </span>
                    </div>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>

            <!-- Mensuel, Bimestriel, Trimestriel, Quadrimestriel et Semestriel -->
            <div
                v-else-if="
                    [
                        'Mensuel',
                        'Bimestriel',
                        'Trimestriel',
                        'Quadrimestriel',
                        'Semestriel',
                    ].includes(frequenceObj.type)
                "
                class="flex flex-col"
            >
                <span class="font-medium"
                    >{{ frequenceObj.type }} - {{ frequenceObj.periode }}</span
                >
                <div class="mt-1">
                    <span
                        v-for="(jour, index) in frequenceObj.joursHeure.jours"
                        :key="index"
                        :class="[
                            'px-2 py-1 rounded mx-1',
                            getBorderClass(
                                calculerJoursRestantsParJour(
                                    jour,
                                    0,
                                    frequenceObj.type,
                                    frequenceObj.periode
                                )
                            ),
                        ]"
                    >
                        {{ jour }}
                        {{
                            formatJoursRestants(
                                calculerJoursRestantsParJour(
                                    jour,
                                    0,
                                    frequenceObj.type,
                                    frequenceObj.periode
                                )
                            )
                        }}
                    </span>
                </div>
                <div
                    v-if="
                        frequenceObj.joursHeure.suivis &&
                        frequenceObj.joursHeure.suivis.length > 0
                    "
                    class="mt-2"
                >
                    <span>Suivis: </span>
                    <div
                        v-for="(suivi, indexSuivi) in frequenceObj.joursHeure
                            .suivis"
                        :key="indexSuivi"
                        class="mt-1"
                    >
                        <span
                            v-for="(jour, index) in suivi.jours"
                            :key="index"
                            :class="[
                                'px-2 py-1 rounded mx-1',
                                getBorderClass(
                                    calculerJoursRestantsParJour(jour, 2)
                                ),
                            ]"
                        >
                            {{ jour }}
                            {{
                                formatJoursRestants(
                                    calculerJoursRestantsParJour(jour, 2)
                                )
                            }}
                        </span>
                    </div>
                </div>
                <div v-else class="mt-2 text-gray-500">Aucun suivi</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FrequenceInformations",
    props: {
        frequence: {
            type: [String, Object],
            required: true,
        },
    },
    data() {
        return {
            frequenceObj: null,
            joursMap: {
                Lundi: 1,
                Mardi: 2,
                Mercredi: 3,
                Jeudi: 4,
                Vendredi: 5,
                Samedi: 6,
                Dimanche: 0,
            },
        };
    },
    created() {
        this.parseFrequence();
    },
    watch: {
        frequence: {
            handler() {
                this.parseFrequence();
            },
            deep: true,
        },
    },
    methods: {
        parseFrequence() {
            // Si c'est une chaîne de caractères, vérifier si c'est du JSON
            if (typeof this.frequence === "string") {
                try {
                    this.frequenceObj = JSON.parse(this.frequence);
                } catch (e) {
                    this.frequenceObj = this.frequence;
                }
            } else {
                this.frequenceObj = this.frequence;
            }
        },

        calculerJoursRestants(dateStr) {
            if (!dateStr) return 0;

            const date = new Date(dateStr);
            const aujourdhui = new Date();
            aujourdhui.setHours(0, 0, 0, 0);

            const diffTime = date - aujourdhui;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return diffDays;
        },

        calculerJoursRestantsParJour(
            jourString,
            intervalle = 4,
            type = null,
            periode = null
        ) {
            const jourIndex = this.joursMap[jourString];
            if (jourIndex === undefined) return 0;

            const aujourdhui = new Date();
            const jourActuel = aujourdhui.getDay();
            let joursRestants = (jourIndex - jourActuel + 7) % 7;

            // Si c'est aujourd'hui mais qu'on a déjà dépassé l'heure, on ajoute 7 jours
            if (joursRestants === 0) {
                // Logique pour vérifier l'heure pourrait être ajoutée ici
                joursRestants = 7;
            }

            // Ajustements selon le type et la période pour les types mensuels et plus
            if (type) {
                if (periode === "semaineProchaine" && joursRestants < 7) {
                    joursRestants += 7;
                } else if (periode === "moisProchain") {
                    // Trouver le premier jour du mois prochain qui correspond au jour demandé
                    const premierJourMoisProchain = new Date(
                        aujourdhui.getFullYear(),
                        aujourdhui.getMonth() + 1,
                        1
                    );
                    const jourPremierMois = premierJourMoisProchain.getDay();
                    joursRestants = (jourIndex - jourPremierMois + 7) % 7;

                    // Ajouter les jours du mois en cours
                    const dernierJourMoisCourant = new Date(
                        aujourdhui.getFullYear(),
                        aujourdhui.getMonth() + 1,
                        0
                    ).getDate();
                    joursRestants +=
                        dernierJourMoisCourant - aujourdhui.getDate();
                }

                // Ajouter des mois selon le type
                const moisSupplementaires = {
                    Mensuel: 1,
                    Bimestriel: 2,
                    Trimestriel: 3,
                    Quadrimestriel: 4,
                    Semestriel: 6,
                };

                // Pour simuler l'ajout de mois, on ajoute approximativement 30 jours par mois
                if (moisSupplementaires[type]) {
                    joursRestants += moisSupplementaires[type] * 30;
                }
            }

            return joursRestants;
        },

        formatJoursRestants(jours) {
            if (jours === 0) return "Jour J";
            return jours > 0 ? `+${jours} jours` : `${jours} jours`;
        },

        getBorderClass(jours) {
            if (jours === 0) return "bg-blue-400";

            // Pour Ponctuel
            if (jours > 7) return "bg-green-400";
            if (jours > 0) return "bg-yellow-400";
            if (jours >= -7) return "bg-orange-400";
            return "bg-red-400";
        },

        getFormattedFrequence() {
            if (!this.frequenceObj) return "";

            try {
                if (typeof this.frequenceObj === "string") {
                    return this.frequenceObj;
                }

                let result = this.frequenceObj.type;

                if (this.frequenceObj.type === "Ponctuel") {
                    const joursDebut = this.calculerJoursRestants(
                        this.frequenceObj.debut
                    );
                    result += ` - debut: ${this.formatJoursRestants(
                        joursDebut
                    )}`;

                    if (
                        this.frequenceObj.suivis &&
                        this.frequenceObj.suivis.length > 0
                    ) {
                        result += " - suivis: ";
                        this.frequenceObj.suivis.forEach((suivi, index) => {
                            const joursSuivi =
                                this.calculerJoursRestants(suivi);
                            result += `${this.formatJoursRestants(joursSuivi)}${
                                index < this.frequenceObj.suivis.length - 1
                                    ? ", "
                                    : ""
                            }`;
                        });
                    }
                } else if (this.frequenceObj.type === "Hebdomadaire") {
                    if (
                        this.frequenceObj.mode === "dateHeure" &&
                        this.frequenceObj.dateHeure &&
                        this.frequenceObj.dateHeure.blocs
                    ) {
                        const joursDebut = this.calculerJoursRestants(
                            this.frequenceObj.dateHeure.blocs[0].debut
                        );
                        result += ` - debut: ${this.formatJoursRestants(
                            joursDebut
                        )}`;
                    } else if (
                        this.frequenceObj.mode === "joursHeure" &&
                        this.frequenceObj.joursHeure
                    ) {
                        result += ` - jours: ${this.frequenceObj.joursHeure.jours.join(
                            ", "
                        )}`;
                    }
                } else if (this.frequenceObj.type === "Annuel") {
                    if (
                        this.frequenceObj.mode === "dateHeure" &&
                        this.frequenceObj.dateHeure
                    ) {
                        const joursDebut = this.calculerJoursRestants(
                            this.frequenceObj.dateHeure.debut
                        );
                        result += ` - debut: ${this.formatJoursRestants(
                            joursDebut
                        )}`;
                    } else if (
                        this.frequenceObj.mode === "joursHeure" &&
                        this.frequenceObj.joursHeure
                    ) {
                        result += ` - ${this.frequenceObj.joursHeure.periodeMois} - ${this.frequenceObj.joursHeure.frequence}`;
                    }
                } else if (
                    [
                        "Mensuel",
                        "Bimestriel",
                        "Trimestriel",
                        "Quadrimestriel",
                        "Semestriel",
                    ].includes(this.frequenceObj.type)
                ) {
                    result += ` - ${
                        this.frequenceObj.periode
                    } - jours: ${this.frequenceObj.joursHeure.jours.join(
                        ", "
                    )}`;
                }

                return result;
            } catch (error) {
                console.error(
                    "Erreur lors du formatage de la fréquence:",
                    error
                );
                return String(this.frequenceObj);
            }
        },
    },
};
</script>

<style scoped>
/* Vous pouvez ajouter des styles spécifiques ici */
</style>
