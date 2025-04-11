<template>
    <div
        class="bg-white shadow-md p-4 flex justify-end items-center space-x-4 top-0 z-50"
    >
        <div
            class="border-l border-gray-200 ml-4 pl-4 flex items-center space-x-4"
        >
            <!-- Nom et rôle -->
            <div class="text-right">
                <p class="font-bold">{{ userData.nom_utilisateur }}</p>
                <p class="text-gray-500 text-sm">{{ userData.departement }}</p>
            </div>
        </div>
        <!-- Image de profil et dropdown -->
        <div class="relative">
            <button @click="toggleDropdown" class="focus:outline-none">
                <img
                    src="/image/user.png"
                    alt="Profile"
                    class="w-10 h-10 rounded-full border border-gray-300"
                />
            </button>

            <div
                v-if="isDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white border shadow-lg rounded-lg overflow-hidden z-50"
            >
                <button
                    @click="goToProfile"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left"
                >
                    Profile
                </button>
                <div class="border-b border-gray-300"></div>
                <button
                    @click="handleLogout"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left"
                >
                    Déconnexion
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import emitter from "../../utils/eventBus";

const router = useRouter();
const isDropdownOpen = ref(false);
const userData = ref({
    // Ajout de userData qui manquait aussi
    nom_utilisateur: "",
    departement: "",
});
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const goToProfile = () => {
    const user = JSON.parse(localStorage.getItem("user"));
    if (user && user.role === "admin") {
        router.push(`/admin/utilisateurs/profile/${user.id}`);
    } else {
        router.push(`/user/profile/${user.id}`);
    }
    isDropdownOpen.value = false; // Fermer le dropdown après la redirection
};

const handleLogout = () => {
    // Supprimer les données du localStorage
    localStorage.removeItem("user");
    localStorage.removeItem("token");

    // Fermer le dropdown
    isDropdownOpen.value = false;

    // Rediriger vers la page de connexion
    router.push("/login");
};

// Écouter l'événement de mise à jour
const updateUserData = (updatedUser) => {
    userData.value = updatedUser;
};

onMounted(() => {
    // Récupérer les données de l'utilisateur depuis le localStorage
    const user = JSON.parse(localStorage.getItem("user"));
    if (user) {
        userData.value = user;
    }

    // Ajouter l'écouteur d'événement
    emitter.on("user-updated", updateUserData);
});

// Nettoyer l'écouteur d'événement lors de la destruction du composant
onUnmounted(() => {
    emitter.off("user-updated", updateUserData);
});
</script>
