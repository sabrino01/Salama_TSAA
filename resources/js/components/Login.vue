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
import { Eye, EyeOff } from "lucide-vue-next";

const router = useRouter();
const credentials = reactive({
    nom_utilisateur: "",
    mot_de_passe: "",
});

const erreurs = reactive({
    nom_utilisateur: "",
    mot_de_passe: "",
    login: "",
});

const showPassword = reactive({
    mot_de_passe: false,
});

const handleLogin = async () => {
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));
    if (!credentials.nom_utilisateur) {
        erreurs.nom_utilisateur = "Le nom d'utilisateur est requis.";
    }
    if (!credentials.mot_de_passe) {
        erreurs.mot_de_passe = "Le mot de passe est requis.";
    }
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const response = await axios.post("/api/login", credentials);
        if (response.data.token) {
            localStorage.setItem("token", response.data.token);
            localStorage.setItem("user", JSON.stringify(response.data.user));
        }
        const hasAlerts = await checkExistingAlerts(response.data.user.id);
        const roleRoutes = {
            admin: "/admin",
            user: "/user",
            responsable: "/responsable/actions/auditinterne",
            suivi: "/suivi/actions/auditinterne",
        };
        const role = response.data.user.role;
        const baseRoute = roleRoutes[role] || "/";
        if (["admin", "user"].includes(role)) {
            if (hasAlerts) {
                router.push(`${baseRoute}/notifications`);
            } else {
                router.push(`${baseRoute}/dashboard`);
            }
        } else {
            router.push(baseRoute);
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                erreurs[key] = error.response.data.errors[key][0];
            });
        } else if (error.response && error.response.data.message) {
            erreurs.login = error.response.data.message;
        }
    }
};

const parseDateTime = (dateTimeStr) => {
    if (!dateTimeStr) return null;
    return new Date(dateTimeStr);
};

const calculerJoursRestants = (dateTimeStr) => {
    if (!dateTimeStr) return 0;
    const inputDate = parseDateTime(dateTimeStr);
    if (!inputDate) return 0;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const inputDateOnly = new Date(inputDate);
    inputDateOnly.setHours(0, 0, 0, 0);
    const diffTime = inputDateOnly - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};

const formatDateHeure = (dateTimeStr) => {
    if (!dateTimeStr) return "";
    const date = parseDateTime(dateTimeStr);
    if (!date) return dateTimeStr;
    return date.toLocaleString("fr-FR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const getNotificationThresholds = (typeFrequence, typeEvent) => {
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
};

const getNotificationClass = (typeFrequence, typeEvent, dateTimeStr) => {
    const jours = calculerJoursRestants(dateTimeStr);
    const seuils = getNotificationThresholds(typeFrequence, typeEvent);
    if (jours === 0) return "border-blue-400";
    if (jours > 0) {
        if (jours <= seuils.commencement) {
            return "border-yellow-400";
        }
        return "border-green-400";
    }
    if (jours < 0) {
        if (Math.abs(jours) <= seuils.retard) {
            return "border-orange-400";
        }
        return "border-red-400";
    }
    return "border-gray-400";
};

const formatNotificationStatus = (typeFrequence, typeEvent, dateTimeStr) => {
    const jours = calculerJoursRestants(dateTimeStr);
    if (jours === 0) return "(Aujourd'hui)";
    if (jours > 0) return `(dans ${jours} jour${jours > 1 ? "s" : ""})`;
    return `(il y a ${Math.abs(jours)} jour${Math.abs(jours) > 1 ? "s" : ""})`;
};

const getNextRecurrentDate = (dateDebut, intervalMois) => {
    const aujourd = new Date();
    const prochaine = new Date(dateDebut);
    while (prochaine <= aujourd) {
        prochaine.setMonth(prochaine.getMonth() + intervalMois);
    }
    return prochaine;
};

const getNextAnnualDate = (dateDebut) => {
    const aujourd = new Date();
    const prochaine = new Date(dateDebut);
    prochaine.setFullYear(aujourd.getFullYear());
    if (prochaine <= aujourd) {
        prochaine.setFullYear(prochaine.getFullYear() + 1);
    }
    return prochaine;
};

const getRecurrentNotifications = (frequenceObj) => {
    if (!frequenceObj || typeof frequenceObj === "string") {
        return [];
    }
    const notifications = [];
    const today = new Date();

    if (
        [
            "Mensuel",
            "Bimestriel",
            "Trimestriel",
            "Quadrimestriel",
            "Semestriel",
        ].includes(frequenceObj.type)
    ) {
        const planification = frequenceObj.planification;
        if (planification) {
            const debut = parseDateTime(planification.dateHeureDebut);
            const fin = parseDateTime(
                planification.dateHeureFin || planification.dateHeureFinale
            );
            if (debut && fin) {
                const intervalles = {
                    Mensuel: 1,
                    Bimestriel: 2,
                    Trimestriel: 3,
                    Quadrimestriel: 4,
                    Semestriel: 6,
                };
                const intervalMois = intervalles[frequenceObj.type];
                const prochaineDeclenchement = getNextRecurrentDate(
                    debut,
                    intervalMois
                );
                const joursRestants = calculerJoursRestants(
                    prochaineDeclenchement.toISOString()
                );
                if (Math.abs(joursRestants) <= 7) {
                    notifications.push({
                        type: "recurrent",
                        joursRestants,
                        message: `Prochaine occurrence : ${formatDateHeure(
                            prochaineDeclenchement.toISOString()
                        )}`,
                        details: `Se répète tous les ${intervalMois} mois jusqu'au ${formatDateHeure(
                            fin.toISOString()
                        )}`,
                        class: getNotificationClass(
                            frequenceObj.type.toLowerCase(),
                            "debut",
                            prochaineDeclenchement.toISOString()
                        ),
                    });
                }
            }
        }
    }

    if (frequenceObj.type === "Annuel" && frequenceObj.dateHeure) {
        const debut = parseDateTime(frequenceObj.dateHeure.debut);
        if (debut) {
            const prochaineAnnee = getNextAnnualDate(debut);
            const joursRestants = calculerJoursRestants(
                prochaineAnnee.toISOString()
            );
            if (Math.abs(joursRestants) <= 7) {
                notifications.push({
                    type: "recurrent",
                    joursRestants,
                    message: `Prochaine occurrence : ${formatDateHeure(
                        prochaineAnnee.toISOString()
                    )}`,
                    details: "Se répète chaque année",
                    class: getNotificationClass(
                        "annuel",
                        "debut",
                        prochaineAnnee.toISOString()
                    ),
                });
            }
        }
    }

    return notifications;
};

const checkExistingAlerts = async (userId) => {
    try {
        const isAlertEnabled = localStorage.getItem("appAlert") === "true";
        if (!isAlertEnabled) {
            return false;
        }
        const response = await axios.get(`/api/notifications`, {
            params: {
                user_id: userId,
                with_pagination: false,
            },
        });
        if (
            !response.data ||
            !response.data.en_cours ||
            !response.data.en_retard
        ) {
            console.error("Format de réponse invalide:", response.data);
            return false;
        }
        const enCours = response.data.en_cours.data || [];
        const enRetard = response.data.en_retard.data || [];
        const hasActiveAlerts =
            enCours.some((item) => needsAlert(item.frequence)) ||
            enRetard.some((item) => needsAlert(item.frequence));
        return hasActiveAlerts;
    } catch (error) {
        console.error("Erreur lors de la vérification des alertes:", error);
        return false;
    }
};

const needsAlert = (frequence) => {
    if (!frequence) return false;
    try {
        const frequenceObj =
            typeof frequence === "string" ? JSON.parse(frequence) : frequence;
        const infosDebut = verifierAlerteDebut(frequenceObj);
        const infosSuivis = verifierAlerteSuivis(frequenceObj);
        const recurrentNotifications = getRecurrentNotifications(frequenceObj);
        return (
            infosDebut.doitAlerter ||
            infosSuivis.doitAlerter ||
            recurrentNotifications.length > 0
        );
    } catch (error) {
        console.error("Erreur lors de l'analyse de la fréquence:", error);
        return false;
    }
};

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
    if (joursRestants === 0) {
        joursRestants = 7;
    }
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
                Mensuel: 5,
                Bimestriel: 7,
                Trimestriel: 10,
                Quadrimestriel: 10,
                Semestriel: 14,
                Annuel: 21,
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
                const plagesAlerte = {
                    Mensuel: 3,
                    Bimestriel: 5,
                    Trimestriel: 7,
                    Quadrimestriel: 7,
                    Semestriel: 10,
                    Annuel: 14,
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

const resetError = (field) => {
    erreurs[field] = "";
    if (field === "nom_utilisateur" || field === "mot_de_passe") {
        erreurs.login = "";
    }
};
</script>
