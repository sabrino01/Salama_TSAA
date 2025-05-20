<template>
    <div class="h-screen w-64 bg-[#1e7a3a] text-white flex flex-col p-4">
        <!-- Logo -->
        <div class="mb-6">
            <img
                src="/image/tsaa.png"
                alt="Logo Salama Tsaa"
                class="w-48 py-1 mx-auto"
            />
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col space-y-2">
            <router-link
                to="/suivi/actions/auditinterne"
                :class="[
                    'flex items-center space-x-2 p-3 rounded-lg',
                    isRouteActive('/suivi/actions')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <Settings />
                <span>Actions</span>
            </router-link>
        </nav>
    </div>
</template>

<script setup>
import { useRoute } from "vue-router";
import { Settings } from "lucide-vue-next";

const route = useRoute();

// Vérifier si une route est active (pour le style de surlignage)
const isRouteActive = (routePath) => {
    if (routePath === "/suivi/actions") {
        // Pour le bouton Actions qui couvre plusieurs chemins
        return route.path.startsWith("/suivi/actions");
    }
    return route.path.startsWith(routePath);
};
</script>
