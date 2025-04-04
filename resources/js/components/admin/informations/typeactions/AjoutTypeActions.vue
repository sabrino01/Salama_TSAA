<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-5 bg-gray-50 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Ajout Types d'Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Ajouter des types d'actions pour avoir des types
                        d'actions à faire.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <label
                            for="code"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </label>
                        <input
                            type="text"
                            id="code"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="libelle"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </label>
                        <input
                            type="text"
                            id="libelle"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="actionpour"
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action pour :
                        </label>
                        <select
                            id="actionpour"
                            class="w-[20%] border border-gray-400 rounded-md px-4 py-2"
                        >
                            <option value="1">Audit Interne</option>
                            <option value="2">PTA</option>
                        </select>
                    </div>
                    <div class="flex w-[64%] justify-center mt-5">
                        <router-link to="/admin/informations/typeactions"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            class="w-[15%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                        >
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
</script>
