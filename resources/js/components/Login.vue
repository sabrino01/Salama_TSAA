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
                <form class="space-y-8" @submit.prevent="handleSubmit">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label
                                for="email"
                                class="block text-sm mb-3 dark:text-white font-poppins"
                                >Nom d'utilisateur</label
                            >
                            <input
                                id="email"
                                name="email"
                                type="name"
                                v-model="email"
                                required
                                class="py-2 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            />
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block text-sm mt-4 mb-3 dark:text-white font-poppins"
                                >Mot de passe</label
                            >
                            <input
                                id="password"
                                name="password"
                                type="password"
                                v-model="password"
                                required
                                class="py-2 px-4 block w-full border-gray-400 border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            />
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
