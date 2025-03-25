<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-xs space-y-4 p-6 bg-white rounded-lg shadow-md">
            <div>
                <img
                    src="image/tsaa.png"
                    alt="Logo Salama Tsaa"
                    class="w-40 h-auto mx-auto"
                />
                <h2 class="mt-2 text-center text-xl font-bold text-gray-900">
                    Connexion
                </h2>
                <p
                    class="mt-3 text-xs text-center text-gray-600 dark:text-neutral-400 font-poppins"
                >
                    Pour accéder à votre espace membre, veuillez vous connecter
                </p>
            </div>
            <div class="mt-8">
                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label
                                for="email"
                                class="block text-xs mb-2 dark:text-white font-poppins"
                                >Nom d'utilisateur</label
                            >
                            <input
                                id="email"
                                name="email"
                                type="name"
                                v-model="email"
                                required
                                class="py-1 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            />
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block text-xs mt-2 mb-2 dark:text-white font-poppins"
                                >Mot de passe</label
                            >
                            <input
                                id="password"
                                name="password"
                                type="password"
                                v-model="password"
                                required
                                class="py-1 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            />
                        </div>
                    </div>

                    <!-- <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember-me"
                                name="remember-me"
                                type="checkbox"
                                v-model="rememberMe"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label
                                for="remember-me"
                                class="ml-2 block text-sm text-gray-900"
                            >
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="text-sm">
                            <a
                                href="#"
                                class="font-medium text-indigo-600 hover:text-indigo-500"
                            >
                                Mot de passe oublié?
                            </a>
                        </div>
                    </div> -->

                    <div>
                        <button
                            type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
import { ref } from "vue";

const email = ref("");
const password = ref("");
const rememberMe = ref(false);

const handleSubmit = () => {
    // Simulation de login sans backend
    console.log("Tentative de connexion avec:", {
        email: email.value,
        password: password.value,
        rememberMe: rememberMe.value,
    });

    // Afficher un message pour l'utilisateur
    alert(`Connexion simulée pour ${email.value}`);

    // Réinitialiser le formulaire
    email.value = "";
    password.value = "";
    rememberMe.value = false;
};
</script>
