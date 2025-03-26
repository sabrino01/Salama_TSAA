<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-6 bg-gray-100 pb-16">
                <div
                    class="flex w-auto rounded-xl shadow-lg justify-center items-center bg-white"
                >
                    <h1 class="text-lg font-poppins">
                        Bienvenue dans les membres.
                    </h1>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "./assets/Sidebar.vue";
import Navbar from "./assets/Navbar.vue";
import Footer from "./assets/Footer.vue";
</script>
