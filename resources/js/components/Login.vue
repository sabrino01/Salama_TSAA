<template>
    <div class="flex justify-center min-h-screen items-center bg-gray-50">
        <div class="max-w-lg space-y-6 p-8 bg-white rounded-xl shadow-lg">
            <div>
                <img
                    src="image/tsaa.png"
                    alt="Logo Salama Tsaa"
                    class="w-56 mx-auto"
                />
                <h2 class="mt-2 text-center text-2xl font-bold text-gray-900">
                    Connexion
                </h2>
                <p
                    class="mt-4 text-sm text-gray-600 dark:text-neutral-400 font-poppins"
                >
                    Pour accéder à votre espace membre, veuillez vous
                </p>
                <p
                    class="text-sm text-center text-gray-600 dark:text-neutral-400 font-poppins"
                >
                    connecter
                </p>
            </div>
            <div class="mt-8">
                <form class="space-y-8" @submit.prevent="handleLogin">
                    <p
                        v-if="erreurs.login"
                        class="text-white text-center text-sm mt-1 border bg-red-500 p-2"
                    >
                        {{ erreurs.login }}
                    </p>
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label
                                for="nom_utilisateur"
                                class="block text-sm mb-3 dark:text-white font-poppins"
                                >Nom d'utilisateur</label
                            >
                            <input
                                id="nom_utilisateur"
                                v-model="credentials.nom_utilisateur"
                                type="text"
                                class="py-2 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                :class="{
                                    'border-gray-400': !erreurs.nom_utilisateur,
                                    'border-red-500': erreurs.nom_utilisateur,
                                }"
                                @input="
                                    resetError('nom_utilisateur');
                                    resetError('login');
                                "
                            />
                            <p
                                v-if="erreurs.nom_utilisateur"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.nom_utilisateur }}
                            </p>
                        </div>
                        <div>
                            <label
                                for="mot_de_passe"
                                class="block text-sm mt-4 mb-3 dark:text-white font-poppins"
                                >Mot de passe</label
                            >
                            <div class="relative">
                                <input
                                    id="mot_de_passe"
                                    v-model="credentials.mot_de_passe"
                                    :type="
                                        showPassword.mot_de_passe
                                            ? 'text'
                                            : 'password'
                                    "
                                    class="py-2 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    :class="{
                                        'border-gray-400':
                                            !erreurs.mot_de_passe,
                                        'border-red-500': erreurs.mot_de_passe,
                                    }"
                                    @input="
                                        resetError('mot_de_passe');
                                        resetError('login');
                                    "
                                />
                                <button
                                    v-if="credentials.mot_de_passe"
                                    type="button"
                                    class="absolute right-3 top-2 text-gray-500"
                                    @click="
                                        showPassword.mot_de_passe =
                                            !showPassword.mot_de_passe
                                    "
                                >
                                    <Eye
                                        v-if="showPassword.mot_de_passe"
                                        class="w-5 h-5"
                                    />
                                    <EyeOff v-else class="w-5 h-5" />
                                </button>
                            </div>
                            <p
                                v-if="erreurs.mot_de_passe"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.mot_de_passe }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="group w-full flex justify-center py-2 px-4 border border-transparent text-md font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Se connecter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// Import des icônes Lucide
import { Eye, EyeOff } from "lucide-vue-next";

const router = useRouter();
const credentials = reactive({
    nom_utilisateur: "",
    mot_de_passe: "",
});

const erreurs = reactive({
    nom_utilisateur: "",
    mot_de_passe: "",
});

const showPassword = reactive({
    mot_de_passe: false,
});

const handleLogin = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!credentials.nom_utilisateur) {
        erreurs.nom_utilisateur = "Le nom d'utilisateur est requis.";
    }
    if (!credentials.mot_de_passe) {
        erreurs.mot_de_passe = "Le mot de passe est requis.";
    }

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const response = await axios.post("/api/login", credentials);

        // Stocker le token si votre API en renvoie un
        if (response.data.token) {
            localStorage.setItem("token", response.data.token);
            // Stocker les informations de l'utilisateur
            localStorage.setItem("user", JSON.stringify(response.data.user));
        }

        // Vérifier les alertes en utilisant la route existante
        const hasAlerts = await checkExistingAlerts(response.data.user.id);

        // Redirection basée sur le rôle et les alertes
        const baseRoute =
            response.data.user.role === "admin" ? "/admin" : "/user";

        if (hasAlerts) {
            router.push(`${baseRoute}/notifications`);
        } else {
            router.push(`${baseRoute}/dashboard`);
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                erreurs[key] = error.response.data.errors[key][0];
            });
        } else if (error.response && error.response.data.message) {
            // Erreur générale d'authentification
            erreurs.login = error.response.data.message;
        }
    }
};

// Fonction pour vérifier les alertes en utilisant les routes existantes
const checkExistingAlerts = async (userId) => {
    try {
        // Vérifie d'abord si les alertes sont activées
        const isAlertEnabled = localStorage.getItem("appAlert") === "true";

        if (!isAlertEnabled) {
            return false;
        }

        // Utilise la route getAllNotifications existante
        const response = await axios.get(`/api/notifications`, {
            params: {
                user_id: userId,
            },
        });

        // Vérifie d'abord que les données existent et ont le bon format
        if (
            !response.data ||
            !response.data.en_cours ||
            !response.data.en_retard
        ) {
            console.error("Format de réponse invalide:", response.data);
            return false;
        }

        // Vérifie si en_cours.data existe (pagination)
        const enCours = response.data.en_cours.data || [];
        const enRetard = response.data.en_retard.data || [];

        // Vérifie s'il y a des alertes actives dans les données
        const hasActiveAlerts =
            enCours.some((item) => needsAlert(item.frequence)) ||
            enRetard.some((item) => needsAlert(item.frequence));

        return hasActiveAlerts;
    } catch (error) {
        console.error("Erreur lors de la vérification des alertes:", error);
        return false;
    }
};

// Fonction pour vérifier si une fréquence nécessite une alerte
const needsAlert = (frequence) => {
    if (!frequence) return false;

    try {
        const frequenceObj =
            typeof frequence === "string" ? JSON.parse(frequence) : frequence;

        // Utilise la même logique que dans votre composant Notifications.vue
        const infosDebut = verifierAlerteDebut(frequenceObj);
        const infosSuivis = verifierAlerteSuivis(frequenceObj);

        return infosDebut.doitAlerter || infosSuivis.doitAlerter;
    } catch (error) {
        console.error("Erreur lors de l'analyse de la fréquence:", error);
        return false;
    }
};
// Données pour la gestion des fréquences
const joursMap = {
    Lundi: 1,
    Mardi: 2,
    Mercredi: 3,
    Jeudi: 4,
    Vendredi: 5,
    Samedi: 6,
    Dimanche: 0,
};

const calculerJoursRestantsParJour = (
    jourString,
    intervalle = 4,
    type = null,
    periode = null
) => {
    const jourIndex = joursMap[jourString];
    if (jourIndex === undefined) return 0;

    const aujourdhui = new Date();
    const jourActuel = aujourdhui.getDay();
    let joursRestants = (jourIndex - jourActuel + 7) % 7;

    // Si c'est aujourd'hui mais qu'on a déjà dépassé l'heure, on ajoute 7 jours
    if (joursRestants === 0) {
        joursRestants = 7;
    }

    // Ajustements pour les périodes spécifiques
    if (type) {
        if (periode === "semaineProchaine" && joursRestants < 7) {
            joursRestants += 7;
        } else if (periode === "moisProchain") {
            const premierJourMoisProchain = new Date(
                aujourdhui.getFullYear(),
                aujourdhui.getMonth() + 1,
                1
            );
            const jourPremierMois = premierJourMoisProchain.getDay();
            joursRestants = (jourIndex - jourPremierMois + 7) % 7;

            const dernierJourMoisCourant = new Date(
                aujourdhui.getFullYear(),
                aujourdhui.getMonth() + 1,
                0
            ).getDate();
            joursRestants += dernierJourMoisCourant - aujourdhui.getDate();
        }

        // Ajustement pour les fréquences mensuelles et plus
        const moisSupplementaires = {
            Mensuel: 1,
            Bimestriel: 2,
            Trimestriel: 3,
            Quadrimestriel: 4,
            Semestriel: 6,
        };

        if (moisSupplementaires[type]) {
            joursRestants += moisSupplementaires[type] * 30;
        }
    }

    return joursRestants;
};

const verifierAlerteDebut = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };

    let doitAlerter = false;
    let joursRestants = 0;

    switch (frequenceObj.type) {
        case "Ponctuel":
            joursRestants = calculerJoursRestants(frequenceObj.debut);
            doitAlerter = joursRestants >= -7 && joursRestants <= 7;
            break;

        case "Hebdomadaire":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.blocs
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.dateHeure.blocs.forEach((bloc) => {
                    const jours = calculerJoursRestants(bloc.debut);
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
                doitAlerter = joursRestants >= -7 && joursRestants <= 7;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.joursHeure.jours.forEach((jour) => {
                    const jours = calculerJoursRestantsParJour(jour);
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            }
            break;

        case "Mensuel":
        case "Bimestriel":
        case "Trimestriel":
        case "Quadrimestriel":
        case "Semestriel":
        case "Annuel":
            const plagesAlerte = {
                Mensuel: 5, // ±5 jours
                Bimestriel: 7, // ±7 jours
                Trimestriel: 10, // ±10 jours
                Quadrimestriel: 10, // ±10 jours
                Semestriel: 14, // ±14 jours
                Annuel: 21, // ±21 jours
            };

            if (frequenceObj.mode === "dateHeure" && frequenceObj.dateHeure) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.debut
                );
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure
            ) {
                let minJours = Number.MAX_SAFE_INTEGER;
                frequenceObj.joursHeure.jours.forEach((jour) => {
                    const jours = calculerJoursRestantsParJour(
                        jour,
                        4,
                        frequenceObj.type
                    );
                    if (Math.abs(jours) < Math.abs(minJours)) {
                        minJours = jours;
                    }
                });
                joursRestants = minJours;
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            }
            break;
    }

    return { doitAlerter, joursRestants };
};

const verifierAlerteSuivis = (frequenceObj) => {
    if (typeof frequenceObj === "string") return { doitAlerter: false };

    let doitAlerter = false;
    let joursRestants = 0;

    switch (frequenceObj.type) {
        case "Ponctuel":
            if (frequenceObj.suivis?.length > 0) {
                joursRestants = calculerJoursRestants(frequenceObj.suivis[0]);
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            }
            break;

        case "Hebdomadaire":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.suivis?.length > 0
            ) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.suivis[0]
                );
                doitAlerter = joursRestants >= -4 && joursRestants <= 4;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const premierSuivi = frequenceObj.joursHeure.suivis[0];
                if (premierSuivi.jours?.length > 0) {
                    let minJours = Number.MAX_SAFE_INTEGER;
                    premierSuivi.jours.forEach((jour) => {
                        const jours = calculerJoursRestantsParJour(jour, 2);
                        if (Math.abs(jours) < Math.abs(minJours)) {
                            minJours = jours;
                        }
                    });
                    joursRestants = minJours;
                    doitAlerter = joursRestants >= -2 && joursRestants <= 2;
                }
            }
            break;

        case "Mensuel":
        case "Bimestriel":
        case "Trimestriel":
        case "Quadrimestriel":
        case "Semestriel":
        case "Annuel":
            if (
                frequenceObj.mode === "dateHeure" &&
                frequenceObj.dateHeure?.suivis?.length > 0
            ) {
                joursRestants = calculerJoursRestants(
                    frequenceObj.dateHeure.suivis[0]
                );
                // Plages d'alerte selon le type
                const plagesAlerte = {
                    Mensuel: 3, // ±3 jours
                    Bimestriel: 5, // ±5 jours
                    Trimestriel: 7, // ±7 jours
                    Quadrimestriel: 7, // ±7 jours
                    Semestriel: 10, // ±10 jours
                    Annuel: 14, // ±14 jours
                };
                const plage = plagesAlerte[frequenceObj.type] || 7;
                doitAlerter = joursRestants >= -plage && joursRestants <= plage;
            } else if (
                frequenceObj.mode === "joursHeure" &&
                frequenceObj.joursHeure?.suivis?.length > 0
            ) {
                const premierSuivi = frequenceObj.joursHeure.suivis[0];
                if (premierSuivi.jours?.length > 0) {
                    let minJours = Number.MAX_SAFE_INTEGER;
                    premierSuivi.jours.forEach((jour) => {
                        const jours = calculerJoursRestantsParJour(
                            jour,
                            2,
                            frequenceObj.type
                        );
                        if (Math.abs(jours) < Math.abs(minJours)) {
                            minJours = jours;
                        }
                    });
                    joursRestants = minJours;
                    // Même plages d'alerte que ci-dessus
                    const plagesAlerte = {
                        Mensuel: 3,
                        Bimestriel: 5,
                        Trimestriel: 7,
                        Quadrimestriel: 7,
                        Semestriel: 10,
                        Annuel: 14,
                    };
                    const plage = plagesAlerte[frequenceObj.type] || 7;
                    doitAlerter =
                        joursRestants >= -plage && joursRestants <= plage;
                }
            }
            break;
    }

    return { doitAlerter, joursRestants };
};

// Fonction pour réinitialiser l'erreur d'un champ spécifique
const resetError = (field) => {
    erreurs[field] = "";
    if (field === "nom_utilisateur" || field === "mot_de_passe") {
        erreurs.login = ""; // Réinitialiser l'erreur générale
    }
};
</script>
