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

        // Redirection basée sur le rôle
        if (response.data.user.role === "admin") {
            router.push("/admin/dashboard");
        } else {
            router.push("/user/dashboard");
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

// Fonction pour réinitialiser l'erreur d'un champ spécifique
const resetError = (field) => {
    erreurs[field] = "";
    if (field === "nom_utilisateur" || field === "mot_de_passe") {
        erreurs.login = ""; // Réinitialiser l'erreur générale
    }
};
</script>
