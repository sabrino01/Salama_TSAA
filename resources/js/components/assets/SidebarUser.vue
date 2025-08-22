<!-- SidebarUser.vue -->
<template>
    <div
        :class="[
            'h-screen bg-green-600 text-white flex flex-col p-4 transition-all duration-300 fixed left-0 top-0 z-40',
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
                to="/user/dashboard"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/user/dashboard')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <LayoutDashboard class="w-6 h-6 flex-shrink-0" />
                <span v-if="!isCollapsed">Dashboard</span>
                <!-- Tooltip pour mode réduit -->
                <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                >
                    Dashboard
                </div>
            </router-link>

            <!-- Utilisateurs -->
            <router-link
                :to="`/user/profile/${userId}`"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/user/profile/')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <UserCircle2 class="w-6 h-6 flex-shrink-0" />
                <span v-if="!isCollapsed">Profile</span>
                <!-- Tooltip pour mode réduit -->
                <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                >
                    Profile
                </div>
            </router-link>

            <!-- Informations -->
            <div class="relative">
                <button
                    :class="[
                        'flex p-3 w-full rounded-lg transition duration-300 group relative',
                        isCollapsed ? 'justify-center' : '',
                        {
                            'bg-white text-black': isMenuActive('informations'),
                            'hover:bg-white hover:text-black':
                                !isMenuActive('informations'),
                        },
                    ]"
                    @click="toggleMenu('informations')"
                >
                    <ListOrdered class="w-6 h-6 flex-shrink-0" />
                    <span v-if="!isCollapsed" class="flex-1 text-left pl-2.5"
                        >Informations</span
                    >
                    <ChevronDown v-if="!isCollapsed" class="w-5 h-5" />

                    <!-- Tooltip pour mode réduit -->
                    <div
                        v-if="isCollapsed"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                    >
                        Informations
                    </div>
                </button>

                <!-- Menu déroulant normal (mode étendu) -->
                <div
                    v-if="openMenu === 'informations' && !isCollapsed"
                    class="bg-white text-black rounded-lg p-2 space-y-1 mt-1"
                >
                    <router-link
                        to="/user/informations/sources"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/user/informations/sources')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Search class="w-4 h-4" />
                        <span>Sources</span>
                    </router-link>
                    <router-link
                        to="/user/informations/typeactions"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/user/informations/typeactions')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <FileCog class="w-4 h-4" />
                        <span>Type Actions</span>
                    </router-link>
                    <router-link
                        to="/user/informations/responsable"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/user/informations/responsable')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <User class="w-4 h-4" />
                        <span>Responsable</span>
                    </router-link>
                    <router-link
                        to="/user/informations/suivi"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/user/informations/suivi')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ClipboardList class="w-4 h-4" />
                        <span>Suivi</span>
                    </router-link>
                    <router-link
                        to="/user/informations/constat"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/user/informations/constat')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ChartNoAxesCombined class="w-4 h-4" />
                        <span>Constat / Action</span>
                    </router-link>
                </div>

                <!-- Menu contextuel pour mode réduit -->
                <div
                    v-if="showCollapsedMenu === 'informations' && isCollapsed"
                    class="fixed bg-white text-black rounded-lg shadow-lg p-2 space-y-1 z-50 border"
                    :style="{ left: '5rem', top: collapsedMenuPosition + 'px' }"
                >
                    <router-link
                        to="/user/informations/sources"
                        @click="hideCollapsedMenu"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/user/informations/sources')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Search class="w-4 h-4" />
                        <span>Sources</span>
                    </router-link>
                    <router-link
                        to="/user/informations/typeactions"
                        @click="hideCollapsedMenu"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/user/informations/typeactions')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <FileCog class="w-4 h-4" />
                        <span>Type Actions</span>
                    </router-link>
                    <router-link
                        to="/user/informations/responsable"
                        @click="hideCollapsedMenu"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/user/informations/responsable')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <User class="w-4 h-4" />
                        <span>Responsable</span>
                    </router-link>
                    <router-link
                        to="/user/informations/suivi"
                        @click="hideCollapsedMenu"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/user/informations/suivi')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ClipboardList class="w-4 h-4" />
                        <span>Suivi</span>
                    </router-link>
                    <router-link
                        to="/user/informations/constat"
                        @click="hideCollapsedMenu"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/user/informations/constat')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ChartNoAxesCombined class="w-4 h-4" />
                        <span>Constat / Action</span>
                    </router-link>
                </div>
            </div>

            <router-link
                to="/user/actions/auditinterne"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/user/actions')
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

            <router-link
                to="/user/notifications"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/user/notifications')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <Bell class="w-6 h-6 flex-shrink-0" />
                <span v-if="!isCollapsed">Notifications</span>
                <!-- Tooltip pour mode réduit -->
                <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                >
                    Notifications
                </div>
            </router-link>

            <router-link
                to="/user/historiques/auditinterne"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/user/historiques')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <ClockFading class="w-6 h-6 flex-shrink-0" />
                <span v-if="!isCollapsed">Historiques</span>
                <!-- Tooltip pour mode réduit -->
                <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                >
                    Historiques
                </div>
            </router-link>
        </nav>
    </div>

    <!-- Overlay pour fermer le menu en mode réduit -->
    <div
        v-if="showCollapsedMenu && isCollapsed"
        class="fixed inset-0 z-40"
        @click="hideCollapsedMenu"
    ></div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRoute } from "vue-router";
import {
    LayoutDashboard,
    User,
    ListOrdered,
    Search,
    Settings,
    FileCog,
    Bell,
    UserCircle2,
    ClipboardList,
    ChevronDown,
    ChartNoAxesCombined,
    ChevronLeft,
    ChevronRight,
    ClockFading,
} from "lucide-vue-next";

const route = useRoute();
const openMenu = ref(null);
const isCollapsed = ref(false);
const showCollapsedMenu = ref(null);
const collapsedMenuPosition = ref(0);

// Émettre les changements d'état du sidebar vers le parent
const emit = defineEmits(["sidebar-toggle"]);

// Ajouter un computed property pour l'ID de l'utilisateur connecté
const userId = computed(() => {
    const user = JSON.parse(localStorage.getItem("user"));
    return user ? user.id : null;
});

// Fonction pour basculer l'état du sidebar
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.setItem("sidebar-collapsed", isCollapsed.value); // <-- Ajout
    emit("sidebar-toggle", isCollapsed.value);

    if (isCollapsed.value) {
        openMenu.value = null;
        showCollapsedMenu.value = null;
    }
};

// Fonctions pour le menu en mode réduit
const showCollapsedMenuAt = (menu, event) => {
    if (!isCollapsed.value) return;

    const rect = event.currentTarget.getBoundingClientRect();
    collapsedMenuPosition.value = rect.top;
    showCollapsedMenu.value = menu;
};

const hideCollapsedMenu = () => {
    showCollapsedMenu.value = null;
};

// Détecter le chemin actuel et ouvrir le menu correspondant
onMounted(() => {
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isCollapsed.value = saved === "true";
        emit("sidebar-toggle", isCollapsed.value);
    }
    checkCurrentRoute();
});

// Surveiller les changements de route
watch(
    () => route.path,
    () => {
        checkCurrentRoute();
    }
);

// Vérifier la route actuelle et configurer les menus en conséquence
const checkCurrentRoute = () => {
    const path = route.path;

    // Ouvrir le menu Informations si on est dans une de ses sous-routes (seulement si pas réduit)
    if (path.includes("/user/informations") && !isCollapsed.value) {
        openMenu.value = "informations";
    }
};

// Vérifier si une route est active (pour le style de surlignage)
const isRouteActive = (routePath) => {
    if (routePath === "/user/actions") {
        return route.path.startsWith("/user/actions");
    }
    return route.path.startsWith(routePath);
};

// Vérifier si un menu déroulant doit être actif
const isMenuActive = (menu) => {
    if (menu === "informations") {
        return (
            route.path.includes("/user/informations") ||
            openMenu.value === "informations"
        );
    }
    return openMenu.value === menu;
};

// Basculer l'état d'un menu déroulant
const toggleMenu = (menu) => {
    if (isCollapsed.value) {
        // En mode réduit, montrer le menu contextuel
        if (showCollapsedMenu.value === menu) {
            hideCollapsedMenu();
        } else {
            showCollapsedMenuAt(menu, event);
        }
        return;
    }

    openMenu.value = openMenu.value === menu ? null : menu;
};
</script>
