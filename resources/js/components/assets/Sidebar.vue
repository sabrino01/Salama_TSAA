<template>
    <div
        :class="[
            'h-screen bg-[#0062ff] text-white flex flex-col p-4 transition-all duration-300 fixed left-0 top-0 z-40',
            isCollapsed ? 'w-16' : 'w-64',
        ]"
    >
        <!-- Bouton pour basculer le sidebar -->
        <div class="flex justify-end mb-4">
            <button
                @click="toggleSidebar"
                class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center hover:bg-white hover:text-blue-600 transition-colors duration-200"
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
                <span class="text-blue-600 font-bold text-sm">T</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col space-y-2">
            <router-link
                to="/admin/dashboard"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/admin/dashboard')
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
            <div class="relative">
                <button
                    :class="[
                        'flex p-3 w-full rounded-lg transition duration-300 group relative',
                        isCollapsed ? 'justify-center' : '',
                        {
                            'bg-white text-black': isMenuActive('utilisateurs'),
                            'hover:bg-white hover:text-black':
                                !isMenuActive('utilisateurs'),
                        },
                    ]"
                    @click="(event) => toggleMenu('utilisateurs', event)"
                >
                    <Users class="w-6 h-6 flex-shrink-0" />
                    <span v-if="!isCollapsed" class="flex-1 text-left pl-2.5"
                        >Utilisateurs</span
                    >
                    <ChevronDown v-if="!isCollapsed" class="w-5 h-5" />

                    <!-- Tooltip pour mode réduit -->
                    <div
                        v-if="isCollapsed"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50"
                    >
                        Utilisateurs
                    </div>
                </button>

                <!-- Menu déroulant normal (mode étendu) -->
                <div
                    v-if="openMenu === 'utilisateurs' && !isCollapsed"
                    class="bg-white text-black rounded-lg p-2 space-y-1 mt-1"
                >
                    <router-link
                        to="/admin/utilisateurs/membres"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/utilisateurs/membres')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Users2 class="w-4 h-4" />
                        <span>Membres</span>
                    </router-link>
                    <router-link
                        :to="`/admin/utilisateurs/profile/${userId}`"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/utilisateurs/profile')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <UserCircle2 class="w-4 h-4" />
                        <span>Profile</span>
                    </router-link>
                </div>

                <!-- Menu contextuel pour mode réduit -->
                <div
                    v-if="showCollapsedMenu === 'utilisateurs' && isCollapsed"
                    class="fixed bg-white text-black rounded-lg shadow-lg p-2 space-y-1 z-50 border"
                    :style="{ left: '5rem', top: collapsedMenuPosition + 'px' }"
                >
                    <router-link
                        to="/admin/utilisateurs/membres"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/utilisateurs/membres')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Users2 class="w-4 h-4" />
                        <span>Membres</span>
                    </router-link>
                    <router-link
                        :to="`/admin/utilisateurs/profile/${userId}`"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/utilisateurs/profile')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <UserCircle2 class="w-4 h-4" />
                        <span>Profile</span>
                    </router-link>
                </div>
            </div>

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
                    @click="(event) => toggleMenu('informations', event)"
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
                        to="/admin/informations/sources"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/informations/sources')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Search class="w-4 h-4" />
                        <span>Sources</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/typeactions"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/informations/typeactions')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <FileCog class="w-4 h-4" />
                        <span>Type Actions</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/responsable"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/informations/responsable')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <User class="w-4 h-4" />
                        <span>Responsable</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/suivi"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/informations/suivi')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ClipboardList class="w-4 h-4" />
                        <span>Suivi</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/constat"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg',
                            isRouteActive('/admin/informations/constat')
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
                        to="/admin/informations/sources"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/informations/sources')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <Search class="w-4 h-4" />
                        <span>Sources</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/typeactions"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/informations/typeactions')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <FileCog class="w-4 h-4" />
                        <span>Type Actions</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/responsable"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/informations/responsable')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <User class="w-4 h-4" />
                        <span>Responsable</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/suivi"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/informations/suivi')
                                ? 'bg-gray-200'
                                : 'hover:bg-gray-200',
                        ]"
                    >
                        <ClipboardList class="w-4 h-4" />
                        <span>Suivi</span>
                    </router-link>
                    <router-link
                        to="/admin/informations/constat"
                        @click="onCollapsedMenuItemClick"
                        :class="[
                            'flex items-center space-x-2 p-2 w-full rounded-lg whitespace-nowrap',
                            isRouteActive('/admin/informations/constat')
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
                to="/admin/actions/auditinterne"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/admin/actions')
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
                to="/admin/notifications"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/admin/notifications')
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
                to="/admin/historiques/auditinterne"
                :class="[
                    'flex items-center p-3 rounded-lg group relative',
                    isCollapsed ? 'justify-center' : 'space-x-2',
                    isRouteActive('/admin/historiques')
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
    Users,
    User,
    ListOrdered,
    Search,
    Settings,
    FileCog,
    Bell,
    UserCircle2,
    Users2,
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

// ID utilisateur depuis storage
const userId = computed(() => {
    const user = JSON.parse(localStorage.getItem("user"));
    return user ? user.id : null;
});

// Toggle sidebar
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.setItem("sidebar-collapsed", isCollapsed.value);
    emit("sidebar-toggle", isCollapsed.value);

    if (isCollapsed.value) {
        openMenu.value = null;
        showCollapsedMenu.value = null;
    }
};

// Montrer menu réduit à la bonne position
const showCollapsedMenuAt = (menu, event) => {
    if (!isCollapsed.value) return;

    const rect = event.currentTarget.getBoundingClientRect();
    collapsedMenuPosition.value = rect.top;
    showCollapsedMenu.value = menu;
};
const hideCollapsedMenu = () => {
    showCollapsedMenu.value = null;
};

// Fonction appelée lors d’un clic sur un item du menu réduit
const onCollapsedMenuItemClick = () => {
    hideCollapsedMenu();
};

// Détecter état sidebar depuis le storage au montage
onMounted(() => {
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isCollapsed.value = saved === "true";
        emit("sidebar-toggle", isCollapsed.value);
    }
    checkCurrentRoute();
});

// Surveiller changement de route + fermer menu réduit
watch(
    () => route.path,
    () => {
        checkCurrentRoute();
        hideCollapsedMenu();
    }
);

const checkCurrentRoute = () => {
    const path = route.path;

    if (path.includes("/admin/utilisateurs") && !isCollapsed.value) {
        openMenu.value = "utilisateurs";
    } else if (path.includes("/admin/informations") && !isCollapsed.value) {
        openMenu.value = "informations";
    } else {
        openMenu.value = null;
    }
};

const isRouteActive = (routePath) => {
    if (routePath === "/admin/actions") {
        return route.path.startsWith("/admin/actions");
    }
    return route.path.startsWith(routePath);
};

const isMenuActive = (menu) => {
    if (menu === "utilisateurs") {
        return (
            route.path.includes("/admin/utilisateurs") ||
            openMenu.value === "utilisateurs"
        );
    } else if (menu === "informations") {
        return (
            route.path.includes("/admin/informations") ||
            openMenu.value === "informations"
        );
    }
    return openMenu.value === menu;
};

// Toggle menu (passer event ! important)
const toggleMenu = (menu, event) => {
    if (isCollapsed.value) {
        // menu réduit
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
