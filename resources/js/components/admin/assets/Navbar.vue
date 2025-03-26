<template>
    <div
        class="bg-white shadow-md p-4 flex justify-end items-center space-x-4 top-0 z-50"
    >
        <!-- Nom et rôle -->
        <div class="text-right">
            <p class="font-bold">John Doe</p>
            <p class="text-gray-500 text-sm">Administrateur</p>
        </div>

        <!-- Image de profil et dropdown -->
        <div class="relative">
            <button @click="toggleDropdown" class="focus:outline-none">
                <!-- <img
                    src="https://via.placeholder.com/40"
                    alt="Profile"
                    class="w-10 h-10 rounded-full border border-gray-300"
                /> -->
                <UserCircle2 />
            </button>

            <div
                v-if="isDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg overflow-hidden z-50"
            >
                <button
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left"
                >
                    Profile
                </button>
                <button
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left"
                >
                    Déconnexion
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { UserCircle2 } from "lucide-vue-next";
const isDropdownOpen = ref(false);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
</script>
