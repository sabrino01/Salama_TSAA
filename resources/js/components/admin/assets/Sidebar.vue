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
            <button
                @click="selectButton('dashboard')"
                :class="[
                    'flex items-center space-x-2 p-3 rounded-lg',
                    activeButton === 'dashboard'
                        ? 'bg-white text-black'
                        : 'hover:bg-white hover:text-black',
                ]"
            >
                <LayoutDashboard />
                <router-link to="/admin/dashboard">Dashboard</router-link>
            </button>

            <!-- Utilisateurs -->
            <div>
                <button
                    class="flex items-center space-x-2 p-3 w-full rounded-lg transition duration-300"
                    :class="{
                        'bg-white text-black': openMenu === 'utilisateurs',
                        'hover:bg-white hover:text-black':
                            openMenu !== 'utilisateurs',
                    }"
                    @click="toggleMenu('utilisateurs')"
                >
                    <Users />
                    <span>Utilisateurs</span>
                </button>
                <div
                    v-if="openMenu === 'utilisateurs'"
                    class="bg-white text-black rounded-lg p-2 space-y-1"
                >
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <Users2 />
                        <router-link to="/admin/utilisateurs/membres"
                            >Membres</router-link
                        >
                    </button>
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <UserCircle2 />
                        <span>Profile</span>
                    </button>
                </div>
            </div>

            <!-- Informations -->
            <div>
                <button
                    class="flex items-center space-x-2 p-3 w-full hover:bg-white hover:text-black rounded-lg"
                    :class="{
                        'bg-white text-black': openMenu === 'informations',
                        'hover:bg-white hover:text-black':
                            openMenu !== 'informations',
                    }"
                    @click="toggleMenu('informations')"
                >
                    <ListOrdered />
                    <span>Informations</span>
                </button>
                <div
                    v-if="openMenu === 'informations'"
                    class="bg-white text-black rounded-lg p-2 space-y-1"
                >
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <Search />
                        <span>Sources</span>
                    </button>
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <Settings />
                        <span>Type Actions</span>
                    </button>
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <User />
                        <span>Responsable</span>
                    </button>
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <ClipboardList />
                        <span>Suivi</span>
                    </button>
                    <button
                        class="flex items-center space-x-2 p-2 w-full hover:bg-gray-200 rounded-lg"
                    >
                        <BarChart />
                        <span>Constat</span>
                    </button>
                </div>
            </div>

            <button
                class="flex items-center space-x-2 p-3 hover:bg-white hover:text-black rounded-lg"
            >
                <Settings />
                <span>Actions</span>
            </button>
            <button
                class="flex items-center space-x-2 p-3 hover:bg-white hover:text-black rounded-lg"
            >
                <Bell />
                <span>Notifications</span>
            </button>
        </nav>
    </div>
</template>

<script setup>
import { ref } from "vue";
import {
    LayoutDashboard,
    Users,
    User,
    ListOrdered,
    Search,
    Settings,
    BarChart,
    Bell,
    UserCircle2,
    Users2,
    ClipboardList,
} from "lucide-vue-next";

const openMenu = ref(null);

const activeButton = ref(null);

const toggleMenu = (menu) => {
    openMenu.value = openMenu.value === menu ? null : menu;
};

const selectButton = (button) => {
    activeButton.value = button;

    // Fermer les autres menus déroulants si nécessaire
    if (["membres", "profile"].includes(button)) {
        openMenu.value = "utilisateurs";
    } else {
        openMenu.value = null;
    }
};
</script>
