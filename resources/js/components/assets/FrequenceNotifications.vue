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
                <!-- Cas simple: Quotidien -->
                <span class="px-2 py-1 rounded font-semibold">Quotidien</span>
            </div>

            <!-- Ponctuel -->
            <div
                v-else-if="frequenceObj.type === 'Ponctuel'"
                class="flex flex-col"
            >
                <span class="font-medium">{{ frequenceObj.type }}</span>
                <div
                    v-if="
                        frequenceObj.creneaux &&
                        frequenceObj.creneaux.length > 0
                    "
                >
                    <div
                        v-for="(creneau, index) in frequenceObj.creneaux"
                        :key="index"
                        class="mt-1"
                    >
                        <!-- Début du créneau -->
                        <div
                            :class="[
                                'px-2 py-1 rounded mt-1',
                                getNotificationClass(
                                    'ponctuel',
                                    'debut',
                                    creneau.debut
                                ),
                            ]"
                        >
                            Début: {{ formatDateHeure(creneau.debut) }}
                            {{
                                formatNotificationStatus(
                                    "ponctuel",
                                    "debut",
                                    creneau.debut
                                )
                            }}
                        </div>

                        <!-- Fin du créneau -->
                        <div
                            v-if="creneau.fin"
                            :class="[
                                'px-2 py-1 rounded mt-1',
                                getNotificationClass(
                                    'ponctuel',
                                    'fin',
                                    creneau.fin
                                ),
                            ]"
                        >
                            Fin: {{ formatDateHeure(creneau.fin) }}
                            {{
                                formatNotificationStatus(
                                    "ponctuel",
                                    "fin",
                                    creneau.fin
                                )
                            }}
                        </div>

                        <!-- Suivis du créneau -->
                        <div
                            v-if="creneau.suivis && creneau.suivis.length > 0"
                            class="mt-2"
                        >
                            <span class="font-medium">Suivis: </span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span
                                    v-for="(
                                        suivi, suiviIndex
                                    ) in creneau.suivis"
                                    :key="suiviIndex"
                                    :class="[
                                        'px-2 py-1 rounded',
                                        getNotificationClass(
                                            'ponctuel',
                                            'suivi',
                                            suivi
                                        ),
                                    ]"
                                >
                                    {{ formatDateHeure(suivi) }}
                                    {{
                                        formatNotificationStatus(
                                            "ponctuel",
                                            "suivi",
                                            suivi
                                        )
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hebdomadaire -->
            <div
                v-else-if="frequenceObj.type === 'Hebdomadaire'"
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
                        class="mt-1"
                    >
                        <!-- Début du bloc -->
                        <div
                            :class="[
                                'px-2 py-1 rounded mt-1',
                                getNotificationClass(
                                    'hebdomadaire',
                                    'debut',
                                    bloc.debut
                                ),
                            ]"
                        >
                            Début: {{ formatDateHeure(bloc.debut) }}
                            {{
                                formatNotificationStatus(
                                    "hebdomadaire",
                                    "debut",
                                    bloc.debut
                                )
                            }}
                        </div>

                        <!-- Fin du bloc -->
                        <div
                            v-if="bloc.fin"
                            :class="[
                                'px-2 py-1 rounded mt-1',
                                getNotificationClass(
                                    'hebdomadaire',
                                    'fin',
                                    bloc.fin
                                ),
                            ]"
                        >
                            Fin: {{ formatDateHeure(bloc.fin) }}
                            {{
                                formatNotificationStatus(
                                    "hebdomadaire",
                                    "fin",
                                    bloc.fin
                                )
                            }}
                        </div>

                        <!-- Jours -->
                        <div
                            v-if="bloc.jours && bloc.jours.length > 0"
                            class="mt-1"
                        >
                            <span class="font-medium">Jours: </span>
                            <span
                                v-for="(jour, jourIndex) in bloc.jours"
                                :key="jourIndex"
                                class="px-2 py-1 rounded mx-1 bg-gray-200"
                            >
                                {{ jour }}
                            </span>
                        </div>
                    </div>

                    <!-- Suivis globaux -->
                    <div
                        v-if="
                            frequenceObj.dateHeure.suivis &&
                            frequenceObj.dateHeure.suivis.length > 0
                        "
                        class="mt-2"
                    >
                        <span class="font-medium">Suivis: </span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span
                                v-for="(suivi, suiviIndex) in frequenceObj
                                    .dateHeure.suivis"
                                :key="suiviIndex"
                                :class="[
                                    'px-2 py-1 rounded',
                                    getNotificationClass(
                                        'hebdomadaire',
                                        'suivi',
                                        suivi
                                    ),
                                ]"
                            >
                                {{ formatDateHeure(suivi) }}
                                {{
                                    formatNotificationStatus(
                                        "hebdomadaire",
                                        "suivi",
                                        suivi
                                    )
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Annuel -->
            <div
                v-else-if="frequenceObj.type === 'Annuel'"
                class="flex flex-col"
            >
                <span class="font-medium">{{ frequenceObj.type }}</span>
                <div v-if="frequenceObj.dateHeure">
                    <!-- Début annuel -->
                    <div
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getNotificationClass(
                                'annuel',
                                'debut',
                                frequenceObj.dateHeure.debut
                            ),
                        ]"
                    >
                        Début:
                        {{ formatDateHeure(frequenceObj.dateHeure.debut) }}
                        {{
                            formatNotificationStatus(
                                "annuel",
                                "debut",
                                frequenceObj.dateHeure.debut
                            )
                        }}
                    </div>

                    <!-- Fin annuelle -->
                    <div
                        v-if="frequenceObj.dateHeure.fin"
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getNotificationClass(
                                'annuel',
                                'fin',
                                frequenceObj.dateHeure.fin
                            ),
                        ]"
                    >
                        Fin: {{ formatDateHeure(frequenceObj.dateHeure.fin) }}
                        {{
                            formatNotificationStatus(
                                "annuel",
                                "fin",
                                frequenceObj.dateHeure.fin
                            )
                        }}
                    </div>

                    <!-- Suivis annuels -->
                    <div
                        v-if="
                            frequenceObj.dateHeure.suivis &&
                            frequenceObj.dateHeure.suivis.length > 0
                        "
                        class="mt-2"
                    >
                        <span class="font-medium">Suivis: </span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span
                                v-for="(suivi, suiviIndex) in frequenceObj
                                    .dateHeure.suivis"
                                :key="suiviIndex"
                                :class="[
                                    'px-2 py-1 rounded',
                                    getNotificationClass(
                                        'annuel',
                                        'suivi',
                                        suivi
                                    ),
                                ]"
                            >
                                {{ formatDateHeure(suivi) }}
                                {{
                                    formatNotificationStatus(
                                        "annuel",
                                        "suivi",
                                        suivi
                                    )
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensuel, Bimestriel, Trimestriel, Quadrimestriel, Semestriel -->
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
                <span class="font-medium">
                    {{ frequenceObj.type }} - {{ frequenceObj.typeMenu }}
                </span>

                <div v-if="frequenceObj.planification">
                    <!-- Date début -->
                    <div
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getNotificationClass(
                                frequenceObj.type.toLowerCase(),
                                'debut',
                                frequenceObj.planification.dateHeureDebut
                            ),
                        ]"
                    >
                        Début:
                        {{
                            formatDateHeure(
                                frequenceObj.planification.dateHeureDebut
                            )
                        }}
                        {{
                            formatNotificationStatus(
                                frequenceObj.type.toLowerCase(),
                                "debut",
                                frequenceObj.planification.dateHeureDebut
                            )
                        }}
                    </div>

                    <!-- Date fin -->
                    <div
                        v-if="frequenceObj.planification.dateHeureFin"
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getNotificationClass(
                                frequenceObj.type.toLowerCase(),
                                'fin',
                                frequenceObj.planification.dateHeureFin
                            ),
                        ]"
                    >
                        Fin:
                        {{
                            formatDateHeure(
                                frequenceObj.planification.dateHeureFin
                            )
                        }}
                        {{
                            formatNotificationStatus(
                                frequenceObj.type.toLowerCase(),
                                "fin",
                                frequenceObj.planification.dateHeureFin
                            )
                        }}
                    </div>

                    <!-- Date finale (pour typeMenu dateHeureSemaine) -->
                    <div
                        v-if="frequenceObj.planification.dateHeureFinale"
                        :class="[
                            'px-2 py-1 rounded mt-1',
                            getNotificationClass(
                                frequenceObj.type.toLowerCase(),
                                'finale',
                                frequenceObj.planification.dateHeureFinale
                            ),
                        ]"
                    >
                        Fin finale:
                        {{
                            formatDateHeure(
                                frequenceObj.planification.dateHeureFinale
                            )
                        }}
                        {{
                            formatNotificationStatus(
                                frequenceObj.type.toLowerCase(),
                                "finale",
                                frequenceObj.planification.dateHeureFinale
                            )
                        }}
                    </div>

                    <!-- Suivis -->
                    <div
                        v-if="
                            frequenceObj.planification.suivis &&
                            frequenceObj.planification.suivis.length > 0
                        "
                        class="mt-2"
                    >
                        <span class="font-medium">Suivis: </span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span
                                v-for="(suivi, suiviIndex) in frequenceObj
                                    .planification.suivis"
                                :key="suiviIndex"
                                :class="[
                                    'px-2 py-1 rounded',
                                    getNotificationClass(
                                        frequenceObj.type.toLowerCase(),
                                        'suivi',
                                        suivi.dateHeure
                                    ),
                                ]"
                            >
                                {{ formatDateHeure(suivi.dateHeure) }}
                                {{
                                    formatNotificationStatus(
                                        frequenceObj.type.toLowerCase(),
                                        "suivi",
                                        suivi.dateHeure
                                    )
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des notifications récurrentes et progressives -->
        <div v-if="getAllNotifications().length > 0" class="mt-4 border-t pt-4">
            <h4 class="font-medium text-gray-700 mb-2">
                Notifications actives
                <span class="text-sm font-normal text-gray-500">
                    ({{ getAllNotifications().length }} notification{{
                        getAllNotifications().length > 1 ? "s" : ""
                    }})
                </span>
            </h4>
            <div class="space-y-2 max-h-96 overflow-y-auto">
                <div
                    v-for="(notification, index) in getAllNotifications()"
                    :key="index"
                    :class="[
                        'px-3 py-2 rounded border-l-4 transition-all duration-200 hover:shadow-md',
                        notification.class,
                        notification.priority === 0
                            ? 'ring-2 ring-blue-300'
                            : '',
                        notification.priority <= 3 && notification.priority > 0
                            ? 'border-l-yellow-600'
                            : '',
                        notification.priority > 10
                            ? 'border-l-red-600'
                            : 'border-l-blue-600',
                    ]"
                >
                    <div class="flex justify-between items-start">
                        <div class="flex-grow">
                            <div class="font-medium flex items-center">
                                <!-- Icône selon la priorité -->
                                <span class="mr-2">
                                    <span v-if="notification.priority === 0"
                                        >🔴</span
                                    >
                                    <span
                                        v-else-if="
                                            notification.priority <= 3 &&
                                            notification.priority > 0
                                        "
                                        >⚠️</span
                                    >
                                    <span
                                        v-else-if="
                                            notification.type.includes('RETARD')
                                        "
                                        >🚨</span
                                    >
                                    <span
                                        v-else-if="
                                            notification.type.includes(
                                                'récurrent'
                                            )
                                        "
                                        >🔄</span
                                    >
                                    <span v-else>📅</span>
                                </span>
                                {{ notification.type }}
                            </div>
                            <div class="text-sm mt-1">
                                {{ notification.message }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                {{ notification.details }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 ml-4 flex-shrink-0">
                            <div v-if="notification.targetDate">
                                {{
                                    formatNotificationStatus(
                                        "",
                                        "",
                                        notification.targetDate.toISOString()
                                    )
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé des notifications urgentes -->
            <div
                v-if="getUrgentNotificationsCount() > 0"
                class="mt-3 p-2 bg-red-50 border border-red-200 rounded"
            >
                <div class="text-sm font-medium text-red-800">
                    🚨 {{ getUrgentNotificationsCount() }} notification{{
                        getUrgentNotificationsCount() > 1 ? "s" : ""
                    }}
                    urgente{{ getUrgentNotificationsCount() > 1 ? "s" : "" }}
                </div>
                <div class="text-xs text-red-600 mt-1">
                    {{ getUrgentNotificationsSummary() }}
                </div>
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

        // Convertit une date ISO en objet Date
        parseDateTime(dateTimeStr) {
            if (!dateTimeStr) return null;
            return new Date(dateTimeStr);
        },

        // Calcule la différence en jours entre une date et aujourd'hui
        calculerJoursRestants(dateTimeStr) {
            if (!dateTimeStr) return 0;

            const inputDate = this.parseDateTime(dateTimeStr);
            if (!inputDate) return 0;

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const inputDateOnly = new Date(inputDate);
            inputDateOnly.setHours(0, 0, 0, 0);

            const diffTime = inputDateOnly - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return diffDays;
        },

        // Formate une date/heure pour l'affichage
        formatDateHeure(dateTimeStr) {
            if (!dateTimeStr) return "";
            const date = this.parseDateTime(dateTimeStr);
            if (!date) return dateTimeStr;

            return date.toLocaleString("fr-FR", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        // Retourne la classe CSS selon le statut de notification
        getNotificationClass(typeFrequence, typeEvent, dateTimeStr) {
            const jours = this.calculerJoursRestants(dateTimeStr);
            const seuils = this.getNotificationThresholds(
                typeFrequence,
                typeEvent
            );

            if (jours === 0) return "bg-blue-400 text-white";

            // Commencement (avant la date)
            if (jours > 0) {
                if (jours <= seuils.commencement) {
                    return "bg-yellow-400 text-black"; // Notification de commencement
                }
                return "bg-green-400 text-white"; // Pas encore temps
            }

            // Retard (après la date)
            if (jours < 0) {
                if (Math.abs(jours) <= seuils.retard) {
                    return "bg-orange-400 text-white"; // Notification de retard
                }
                return "bg-red-400 text-white"; // Très en retard
            }

            return "bg-gray-400 text-white";
        },

        // Retourne les seuils de notification selon le type
        getNotificationThresholds(typeFrequence, typeEvent) {
            const defaults = { commencement: 7, retard: 7 };

            if (typeEvent === "suivi") {
                return { commencement: 4, retard: 4 };
            }

            switch (typeFrequence) {
                case "ponctuel":
                case "hebdomadaire":
                case "annuel":
                case "mensuel":
                case "bimestriel":
                case "trimestriel":
                case "quadrimestriel":
                case "semestriel":
                    return defaults;
                default:
                    return defaults;
            }
        },

        // Formate le statut de notification
        formatNotificationStatus(typeFrequence, typeEvent, dateTimeStr) {
            const jours = this.calculerJoursRestants(dateTimeStr);

            if (jours === 0) return "(Aujourd'hui)";
            if (jours > 0) return `(dans ${jours} jour${jours > 1 ? "s" : ""})`;
            return `(il y a ${Math.abs(jours)} jour${
                Math.abs(jours) > 1 ? "s" : ""
            })`;
        },

        // Génère toutes les notifications (progressives + récurrentes)
        getAllNotifications() {
            if (!this.frequenceObj || typeof this.frequenceObj === "string") {
                return [];
            }

            const notifications = [];

            // Ajouter les notifications progressives
            notifications.push(...this.getProgressiveNotifications());

            // Ajouter les notifications récurrentes
            notifications.push(...this.getRecurrentNotifications());

            // Trier par priorité
            return notifications.sort((a, b) => a.priority - b.priority);
        },

        // Génère les notifications progressives (J-7 à J+7)
        getProgressiveNotifications() {
            const notifications = [];
            const dates = this.getAllImportantDates();

            dates.forEach((dateInfo) => {
                const targetDate = this.parseDateTime(dateInfo.dateTime);
                if (!targetDate) return;

                const seuils = this.getNotificationThresholds(
                    dateInfo.frequenceType,
                    dateInfo.eventType
                );

                // Notifications de commencement (J-7 à J-1)
                for (let i = seuils.commencement; i >= 1; i--) {
                    const notifDate = new Date(targetDate);
                    notifDate.setDate(notifDate.getDate() - i);

                    const joursRestants = this.calculerJoursRestants(
                        notifDate.toISOString()
                    );

                    // Seulement afficher les notifications des 7 prochains jours
                    if (joursRestants >= 0 && joursRestants <= 7) {
                        notifications.push({
                            type: `${dateInfo.label} - J-${i}`,
                            message: `Dans ${i} jour${i > 1 ? "s" : ""} : ${
                                dateInfo.description
                            }`,
                            details: `Date cible : ${this.formatDateHeure(
                                dateInfo.dateTime
                            )}`,
                            class: "bg-yellow-400 text-black",
                            priority: i, // Plus le chiffre est petit, plus c'est prioritaire
                            targetDate: targetDate,
                            triggerDate: notifDate,
                        });
                    }
                }

                // Notification Jour J
                const joursRestantsJ = this.calculerJoursRestants(
                    dateInfo.dateTime
                );
                if (joursRestantsJ === 0) {
                    notifications.push({
                        type: `${dateInfo.label} - JOUR J`,
                        message: `AUJOURD'HUI : ${dateInfo.description}`,
                        details: `Maintenant : ${this.formatDateHeure(
                            dateInfo.dateTime
                        )}`,
                        class: "bg-blue-500 text-white font-bold",
                        priority: 0,
                        targetDate: targetDate,
                        triggerDate: targetDate,
                    });
                }

                // Notifications de retard (J+1 à J+7)
                for (let i = 1; i <= seuils.retard; i++) {
                    const notifDate = new Date(targetDate);
                    notifDate.setDate(notifDate.getDate() + i);

                    const joursRestants = this.calculerJoursRestants(
                        notifDate.toISOString()
                    );

                    // Seulement afficher les notifications des 7 derniers jours
                    if (joursRestants >= -7 && joursRestants < 0) {
                        const severity =
                            i <= 3 ? "bg-orange-400" : "bg-red-500";
                        notifications.push({
                            type: `${dateInfo.label} - RETARD J+${i}`,
                            message: `Retard de ${i} jour${
                                i > 1 ? "s" : ""
                            } : ${dateInfo.description}`,
                            details: `Date cible dépassée : ${this.formatDateHeure(
                                dateInfo.dateTime
                            )}`,
                            class: `${severity} text-white`,
                            priority: 10 + i, // Retards moins prioritaires
                            targetDate: targetDate,
                            triggerDate: notifDate,
                        });
                    }
                }
            });

            return notifications;
        },

        // Génère les notifications récurrentes
        getRecurrentNotifications() {
            const notifications = [];

            // Pour les fréquences périodiques (Mensuel, Bimestriel, etc.)
            if (
                [
                    "Mensuel",
                    "Bimestriel",
                    "Trimestriel",
                    "Quadrimestriel",
                    "Semestriel",
                ].includes(this.frequenceObj.type)
            ) {
                const planification = this.frequenceObj.planification;
                if (planification) {
                    const debut = this.parseDateTime(
                        planification.dateHeureDebut
                    );
                    const fin = this.parseDateTime(
                        planification.dateHeureFin ||
                            planification.dateHeureFinale
                    );

                    if (debut && fin) {
                        const intervalles = {
                            Mensuel: 1,
                            Bimestriel: 2,
                            Trimestriel: 3,
                            Quadrimestriel: 4,
                            Semestriel: 6,
                        };

                        const intervalMois =
                            intervalles[this.frequenceObj.type];
                        const prochaineDeclenchement =
                            this.getNextRecurrentDate(debut, intervalMois);

                        // Seulement si cette notification n'est pas déjà dans les progressives
                        if (
                            !this.isDateInProgressiveNotifications(
                                prochaineDeclenchement.toISOString()
                            )
                        ) {
                            notifications.push({
                                type: `${this.frequenceObj.type} récurrent`,
                                message: `Prochaine occurrence : ${this.formatDateHeure(
                                    prochaineDeclenchement.toISOString()
                                )}`,
                                details: `Se répète tous les ${intervalMois} mois jusqu'au ${this.formatDateHeure(
                                    fin.toISOString()
                                )}`,
                                class: this.getNotificationClass(
                                    this.frequenceObj.type.toLowerCase(),
                                    "debut",
                                    prochaineDeclenchement.toISOString()
                                ),
                                priority: 100, // Moins prioritaire que les notifications progressives
                                targetDate: prochaineDeclenchement,
                            });
                        }
                    }
                }
            }

            // Pour les fréquences annuelles
            if (
                this.frequenceObj.type === "Annuel" &&
                this.frequenceObj.dateHeure
            ) {
                const debut = this.parseDateTime(
                    this.frequenceObj.dateHeure.debut
                );
                if (debut) {
                    const prochaineAnnee = this.getNextAnnualDate(debut);

                    // Seulement si cette notification n'est pas déjà dans les progressives
                    if (
                        !this.isDateInProgressiveNotifications(
                            prochaineAnnee.toISOString()
                        )
                    ) {
                        notifications.push({
                            type: "Annuel récurrent",
                            message: `Prochaine occurrence : ${this.formatDateHeure(
                                prochaineAnnee.toISOString()
                            )}`,
                            details: "Se répète chaque année",
                            class: this.getNotificationClass(
                                "annuel",
                                "debut",
                                prochaineAnnee.toISOString()
                            ),
                            priority: 101, // Moins prioritaire que les notifications progressives
                            targetDate: prochaineAnnee,
                        });
                    }
                }
            }

            return notifications;
        },

        // Vérifie si une date est déjà dans les notifications progressives
        isDateInProgressiveNotifications(dateTime) {
            const progressiveNotifications = this.getProgressiveNotifications();
            return progressiveNotifications.some(
                (notif) =>
                    notif.targetDate &&
                    Math.abs(
                        notif.targetDate.getTime() -
                            new Date(dateTime).getTime()
                    ) <
                        24 * 60 * 60 * 1000 // Même jour
            );
        },

        // Extrait toutes les dates importantes de la fréquence
        getAllImportantDates() {
            const dates = [];

            if (!this.frequenceObj || typeof this.frequenceObj === "string") {
                return dates;
            }

            // Ponctuel
            if (
                this.frequenceObj.type === "Ponctuel" &&
                this.frequenceObj.creneaux
            ) {
                this.frequenceObj.creneaux.forEach((creneau, index) => {
                    if (creneau.debut) {
                        dates.push({
                            dateTime: creneau.debut,
                            frequenceType: "ponctuel",
                            eventType: "debut",
                            label: "Ponctuel - Début",
                            description: `Début du créneau ${index + 1}`,
                        });
                    }
                    if (creneau.fin) {
                        dates.push({
                            dateTime: creneau.fin,
                            frequenceType: "ponctuel",
                            eventType: "fin",
                            label: "Ponctuel - Fin",
                            description: `Fin du créneau ${index + 1}`,
                        });
                    }
                    if (creneau.suivis) {
                        creneau.suivis.forEach((suivi, suiviIndex) => {
                            dates.push({
                                dateTime: suivi,
                                frequenceType: "ponctuel",
                                eventType: "suivi",
                                label: "Ponctuel - Suivi",
                                description: `Suivi ${
                                    suiviIndex + 1
                                } du créneau ${index + 1}`,
                            });
                        });
                    }
                });
            }

            // Hebdomadaire
            if (
                this.frequenceObj.type === "Hebdomadaire" &&
                this.frequenceObj.dateHeure
            ) {
                if (this.frequenceObj.dateHeure.blocs) {
                    this.frequenceObj.dateHeure.blocs.forEach((bloc, index) => {
                        if (bloc.debut) {
                            dates.push({
                                dateTime: bloc.debut,
                                frequenceType: "hebdomadaire",
                                eventType: "debut",
                                label: "Hebdomadaire - Début",
                                description: `Début du bloc ${index + 1}`,
                            });
                        }
                        if (bloc.fin) {
                            dates.push({
                                dateTime: bloc.fin,
                                frequenceType: "hebdomadaire",
                                eventType: "fin",
                                label: "Hebdomadaire - Fin",
                                description: `Fin du bloc ${index + 1}`,
                            });
                        }
                    });
                }

                if (this.frequenceObj.dateHeure.suivis) {
                    this.frequenceObj.dateHeure.suivis.forEach(
                        (suivi, suiviIndex) => {
                            dates.push({
                                dateTime: suivi,
                                frequenceType: "hebdomadaire",
                                eventType: "suivi",
                                label: "Hebdomadaire - Suivi",
                                description: `Suivi ${suiviIndex + 1}`,
                            });
                        }
                    );
                }
            }

            // Annuel
            if (
                this.frequenceObj.type === "Annuel" &&
                this.frequenceObj.dateHeure
            ) {
                if (this.frequenceObj.dateHeure.debut) {
                    dates.push({
                        dateTime: this.frequenceObj.dateHeure.debut,
                        frequenceType: "annuel",
                        eventType: "debut",
                        label: "Annuel - Début",
                        description: "Début de l'événement annuel",
                    });
                }
                if (this.frequenceObj.dateHeure.fin) {
                    dates.push({
                        dateTime: this.frequenceObj.dateHeure.fin,
                        frequenceType: "annuel",
                        eventType: "fin",
                        label: "Annuel - Fin",
                        description: "Fin de l'événement annuel",
                    });
                }
                if (this.frequenceObj.dateHeure.suivis) {
                    this.frequenceObj.dateHeure.suivis.forEach(
                        (suivi, suiviIndex) => {
                            dates.push({
                                dateTime: suivi,
                                frequenceType: "annuel",
                                eventType: "suivi",
                                label: "Annuel - Suivi",
                                description: `Suivi annuel ${suiviIndex + 1}`,
                            });
                        }
                    );
                }
            }

            // Fréquences périodiques (Mensuel, Bimestriel, etc.)
            if (
                [
                    "Mensuel",
                    "Bimestriel",
                    "Trimestriel",
                    "Quadrimestriel",
                    "Semestriel",
                ].includes(this.frequenceObj.type) &&
                this.frequenceObj.planification
            ) {
                const planification = this.frequenceObj.planification;

                if (planification.dateHeureDebut) {
                    dates.push({
                        dateTime: planification.dateHeureDebut,
                        frequenceType: this.frequenceObj.type.toLowerCase(),
                        eventType: "debut",
                        label: `${this.frequenceObj.type} - Début`,
                        description: `Début de l'événement ${this.frequenceObj.type.toLowerCase()}`,
                    });
                }

                if (planification.dateHeureFin) {
                    dates.push({
                        dateTime: planification.dateHeureFin,
                        frequenceType: this.frequenceObj.type.toLowerCase(),
                        eventType: "fin",
                        label: `${this.frequenceObj.type} - Fin`,
                        description: `Fin de l'événement ${this.frequenceObj.type.toLowerCase()}`,
                    });
                }

                if (planification.dateHeureFinale) {
                    dates.push({
                        dateTime: planification.dateHeureFinale,
                        frequenceType: this.frequenceObj.type.toLowerCase(),
                        eventType: "finale",
                        label: `${this.frequenceObj.type} - Fin finale`,
                        description: `Fin finale de l'événement ${this.frequenceObj.type.toLowerCase()}`,
                    });
                }

                if (planification.suivis) {
                    planification.suivis.forEach((suivi, suiviIndex) => {
                        dates.push({
                            dateTime: suivi.dateHeure,
                            frequenceType: this.frequenceObj.type.toLowerCase(),
                            eventType: "suivi",
                            label: `${this.frequenceObj.type} - Suivi`,
                            description: `Suivi ${
                                suiviIndex + 1
                            } de l'événement ${this.frequenceObj.type.toLowerCase()}`,
                        });
                    });
                }
            }

            return dates;
        },

        // Calcule la prochaine date récurrente pour les événements périodiques
        getNextRecurrentDate(startDate, intervalMonths) {
            const now = new Date();
            let nextDate = new Date(startDate);

            // Trouver la prochaine occurrence après aujourd'hui
            while (nextDate <= now) {
                nextDate.setMonth(nextDate.getMonth() + intervalMonths);
            }

            return nextDate;
        },

        // Calcule la prochaine date anniversaire pour les événements annuels
        getNextAnnualDate(startDate) {
            const now = new Date();
            const nextDate = new Date(startDate);

            // Mettre à l'année courante
            nextDate.setFullYear(now.getFullYear());

            // Si la date est déjà passée cette année, prendre l'année suivante
            if (nextDate <= now) {
                nextDate.setFullYear(now.getFullYear() + 1);
            }

            return nextDate;
        },

        // Compte les notifications urgentes (priorité <= 3)
        getUrgentNotificationsCount() {
            return this.getAllNotifications().filter(
                (notif) => notif.priority <= 3
            ).length;
        },

        // Génère un résumé des notifications urgentes
        getUrgentNotificationsSummary() {
            const urgent = this.getAllNotifications().filter(
                (notif) => notif.priority <= 3
            );

            if (urgent.length === 0) return "";

            const jourJ = urgent.filter((notif) => notif.priority === 0);
            const prochains = urgent.filter(
                (notif) => notif.priority > 0 && notif.priority <= 3
            );
            const retards = urgent.filter((notif) => notif.priority > 10);

            let summary = [];

            if (jourJ.length > 0) {
                summary.push(`${jourJ.length} aujourd'hui`);
            }

            if (prochains.length > 0) {
                summary.push(`${prochains.length} dans les prochains jours`);
            }

            if (retards.length > 0) {
                summary.push(`${retards.length} en retard`);
            }

            return summary.join(", ");
        },

        // Génère les prochaines occurrences pour les fréquences périodiques
        getUpcomingOccurrences(maxOccurrences = 5) {
            const occurrences = [];

            if (
                [
                    "Mensuel",
                    "Bimestriel",
                    "Trimestriel",
                    "Quadrimestriel",
                    "Semestriel",
                ].includes(this.frequenceObj.type)
            ) {
                const planification = this.frequenceObj.planification;
                if (!planification || !planification.dateHeureDebut)
                    return occurrences;

                const debut = this.parseDateTime(planification.dateHeureDebut);
                const fin = this.parseDateTime(
                    planification.dateHeureFin || planification.dateHeureFinale
                );

                if (!debut) return occurrences;

                const intervalles = {
                    Mensuel: 1,
                    Bimestriel: 2,
                    Trimestriel: 3,
                    Quadrimestriel: 4,
                    Semestriel: 6,
                };

                const intervalMois = intervalles[this.frequenceObj.type];
                let dateActuelle = this.getNextRecurrentDate(
                    debut,
                    intervalMois
                );

                for (let i = 0; i < maxOccurrences; i++) {
                    if (fin && dateActuelle > fin) break;

                    occurrences.push({
                        date: new Date(dateActuelle),
                        formatted: this.formatDateHeure(
                            dateActuelle.toISOString()
                        ),
                        joursRestants: this.calculerJoursRestants(
                            dateActuelle.toISOString()
                        ),
                    });

                    dateActuelle.setMonth(
                        dateActuelle.getMonth() + intervalMois
                    );
                }
            }

            return occurrences;
        },

        // Vérifie si une fréquence est active (a des dates futures)
        isFrequenceActive() {
            const dates = this.getAllImportantDates();
            const now = new Date();

            return dates.some((dateInfo) => {
                const date = this.parseDateTime(dateInfo.dateTime);
                return date && date > now;
            });
        },

        // Obtient la prochaine échéance importante
        getNextDeadline() {
            const dates = this.getAllImportantDates();
            const now = new Date();

            const futureDates = dates
                .map((dateInfo) => ({
                    ...dateInfo,
                    parsedDate: this.parseDateTime(dateInfo.dateTime),
                }))
                .filter(
                    (dateInfo) =>
                        dateInfo.parsedDate && dateInfo.parsedDate > now
                )
                .sort((a, b) => a.parsedDate - b.parsedDate);

            return futureDates.length > 0 ? futureDates[0] : null;
        },

        // Obtient les statistiques des notifications
        getNotificationStats() {
            const notifications = this.getAllNotifications();

            return {
                total: notifications.length,
                jourJ: notifications.filter((n) => n.priority === 0).length,
                prochainement: notifications.filter(
                    (n) => n.priority > 0 && n.priority <= 3
                ).length,
                retards: notifications.filter((n) => n.priority > 10).length,
                recurrentes: notifications.filter((n) =>
                    n.type.includes("récurrent")
                ).length,
            };
        },
    },
};
</script>

<style scoped>
/* Styles pour les animations et transitions */
.transition-all {
    transition: all 0.2s ease;
}

/* Styles pour les notifications urgentes */
.ring-2 {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

/* Améliorer la lisibilité des petits textes */
.text-xs {
    font-size: 0.75rem;
    line-height: 1rem;
}

/* Styles pour le scroll des notifications */
.max-h-96 {
    max-height: 24rem;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Styles responsives */
@media (max-width: 640px) {
    .flex-wrap {
        flex-wrap: wrap;
    }

    .ml-4 {
        margin-left: 0;
        margin-top: 0.5rem;
    }
}
</style>
