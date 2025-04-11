<template>
    <div class="h-screen w-64 bg-[#0062ff] text-white flex flex-col p-4">
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
                to="/admin/dashboard"
                :class="[
                    'flex items-center space-x-2 p-3 rounded-lg',
                    isRouteActive('/admin/dashboard')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <LayoutDashboard /><span>Dashboard</span>
            </router-link>
            <!-- Utilisateurs -->
            <div>
                <button
                    class="flex p-3 w-full rounded-lg transition duration-300"
                    :class="{
                        'bg-white text-black': isMenuActive('utilisateurs'),
                        'hover:bg-white hover:text-black':
                            !isMenuActive('utilisateurs'),
                    }"
                    @click="toggleMenu('utilisateurs')"
                >
                    <Users class="basis-[10%]" />
                    <span class="basis-[80%] text-left pl-2.5"
                        >Utilisateurs</span
                    >
                    <ChevronDown class="basis-[10%]" />
                </button>
                <div
                    v-if="openMenu === 'utilisateurs'"
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
                        <Users2 />
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
                        <UserCircle2 />
                        <span>Profile</span>
                    </router-link>
                </div>
            </div>

            <!-- Informations -->
            <div>
                <button
                    class="flex p-3 w-full rounded-lg transition duration-300"
                    :class="{
                        'bg-white text-black': isMenuActive('informations'),
                        'hover:bg-white hover:text-black':
                            !isMenuActive('informations'),
                    }"
                    @click="toggleMenu('informations')"
                >
                    <ListOrdered class="basis-[10%]" />
                    <span class="basis-[80%] text-left pl-2.5"
                        >Informations</span
                    >
                    <ChevronDown class="basis-[10%]" />
                </button>
                <div
                    v-if="openMenu === 'informations'"
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
                        <Search />
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
                        <FileCog />
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
                        <User />
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
                        <ClipboardList />
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
                        <ChartNoAxesCombined />
                        <span>Constat</span>
                    </router-link>
                </div>
            </div>

            <router-link
                to="/admin/actions/auditinterne"
                :class="[
                    'flex items-center space-x-2 p-3 rounded-lg',
                    isRouteActive('/admin/actions')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <Settings />
                <span>Actions</span>
            </router-link>
            <router-link
                to="/admin/notifications"
                :class="[
                    'flex items-center space-x-2 p-3 rounded-lg',
                    isRouteActive('/admin/notifications')
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <Bell />
                <span>Notifications</span>
            </router-link>
        </nav>
    </div>
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
} from "lucide-vue-next";

const route = useRoute();
const openMenu = ref(null);

// Ajouter un computed property pour l'ID de l'utilisateur connecté
const userId = computed(() => {
    const user = JSON.parse(localStorage.getItem("user"));
    return user ? user.id : null;
});

// Détecter le chemin actuel et ouvrir le menu correspondant
onMounted(() => {
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

    // Ouvrir le menu Utilisateurs si on est dans une de ses sous-routes
    if (path.includes("/admin/utilisateurs")) {
        openMenu.value = "utilisateurs";
    }

    // Ouvrir le menu Informations si on est dans une de ses sous-routes
    else if (path.includes("/admin/informations")) {
        openMenu.value = "informations";
    }
};

// Vérifier si une route est active (pour le style de surlignage)
const isRouteActive = (routePath) => {
    if (routePath === "/admin/actions") {
        // Pour le bouton Actions qui couvre plusieurs chemins
        return route.path.startsWith("/admin/actions");
    }
    return route.path.startsWith(routePath);
};

// Vérifier si un menu déroulant doit être actif
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

// Basculer l'état d'un menu déroulant
const toggleMenu = (menu) => {
    openMenu.value = openMenu.value === menu ? null : menu;
};
</script>
