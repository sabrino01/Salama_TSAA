<template>
    <div
        :class="[
            'h-screen bg-[#1e7a3a] text-white flex flex-col p-4 transition-all duration-300 fixed left-0 top-0 z-40',
            isCollapsed ? 'w-16' : 'w-64',
        ]"
    >
        <!-- Bouton pour basculer le sidebar -->
        <div class="flex justify-end mb-4">
            <button
                @click="toggleSidebar"
                class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center hover:bg-white hover:text-green-600 transition-colors duration-200"
            >
                <ChevronLeft v-if="!isCollapsed" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
            </button>
        </div>

        <!-- Logo -->
        <div class="mb-6" v-if="!isCollapsed">
            <img
                src="/image/tsaa.png"
                alt="Logo Salama Tsaa"
                class="w-48 py-1 mx-auto"
            />
        </div>

        <!-- Logo réduit -->
        <div class="mb-6 flex justify-center" v-else>
            <div
                class="w-8 h-8 bg-white rounded-full flex items-center justify-center"
            >
                <span class="text-green-600 font-bold text-sm">T</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col space-y-2">
            <router-link
                to="/suivi/actions/auditinterne"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/suivi/actions')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <Settings class="w-6 h-6 flex-shrink-0" />
                <span v-if="!isCollapsed">Actions</span>
                <!-- Tooltip pour mode réduit -->
                <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                >
                    Actions
                </div>
            </router-link>
        </nav>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { Settings, ChevronLeft, ChevronRight } from "lucide-vue-next";

const route = useRoute();
const isCollapsed = ref(false);

// Émettre les changements d'état du sidebar vers le parent
const emit = defineEmits(["sidebar-toggle"]);

// Toggle sidebar
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.setItem("suivi-sidebar-collapsed", isCollapsed.value);
    emit("sidebar-toggle", isCollapsed.value);
};

// Détecter état sidebar depuis le storage au montage
onMounted(() => {
    const saved = localStorage.getItem("suivi-sidebar-collapsed");
    if (saved !== null) {
        isCollapsed.value = saved === "true";
        emit("sidebar-toggle", isCollapsed.value);
    }
});

// Vérifier si une route est active (pour le style de surlignage)
const isRouteActive = (routePath) => {
    if (routePath === "/suivi/actions") {
        return route.path.startsWith("/suivi/actions");
    }
    return route.path.startsWith(routePath);
};
</script>
