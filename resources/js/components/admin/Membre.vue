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
                        Membres
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace membres, vous pouvez voir et gérer les
                        membres de votre association, ajouter des membres et de
                        les supprimer.
                    </p>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
                    <!-- Bouton d'ajout -->
                    <button
                        class="flex items-center justify-center bg-[#0062ff] text-white px-4 py-2 rounded-md w-40"
                    >
                        <Plus class="w-5 h-5 mr-2" /> Ajouter
                    </button>

                    <!-- Barre de recherche -->
                    <div
                        class="flex items-center ml-6 border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                    >
                        <Search class="w-5 h-5 mr-2 text-gray-500" />
                        <input
                            type="text"
                            placeholder="Rechercher...."
                            class="outline-none bg-transparent text-gray-800 placeholder-gray-500"
                        />
                    </div>
                </div>

                <!-- Tableau des membres -->
                <div class="mt-5 ml-4">
                    <table class="table-fixed w-full h-[11.5rem]">
                        <thead class="bg-gray-300 text-lg h-[2.5rem]">
                            <tr>
                                <th>Nom</th>
                                <th>Nom d'utilisateur</th>
                                <th>Département</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="font-poppins text-center">
                            <tr>
                                <td>Lorem ipsum dolor</td>
                                <td>Lorem</td>
                                <td class="text-gray-600">RSI</td>
                                <td>
                                    <button type="button" class="text-red-500">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Lorem ipsum dolor</td>
                                <td>Lorem</td>
                                <td class="text-gray-600">RSI</td>
                                <td>
                                    <button type="button" class="text-red-500">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Lorem ipsum dolor</td>
                                <td>Lorem</td>
                                <td class="text-gray-600">RSI</td>
                                <td>
                                    <button type="button" class="text-red-500">
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="flex w-full mt-5 justify-between">
                    <!-- Résultat -->
                    <div
                        class="flex items-center text-gray-500 justify-start px-4"
                    >
                        Résultat <strong>1-10</strong> sur <strong>50</strong>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-end space-x-4">
                        <!-- Bouton Précédent -->
                        <button
                            class="flex items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                        >
                            <ChevronLeft class="w-5 h-5" /> Préc.
                        </button>

                        <!-- Pagination -->
                        <div class="flex items-center space-x-2">
                            <button
                                class="px-3 py-1 rounded-md bg-gray-200 text-black font-medium"
                            >
                                1
                            </button>
                            <span class="text-gray-500">...</span>
                            <button
                                class="px-3 py-1 rounded-md bg-gray-200 text-black font-medium"
                            >
                                5
                            </button>
                        </div>

                        <!-- Bouton Suivant -->
                        <button
                            class="flex items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                        >
                            Suiv. <ChevronRight class="w-5 h-5" />
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
import Sidebar from "./assets/Sidebar.vue";
import Navbar from "./assets/Navbar.vue";
import Footer from "./assets/Footer.vue";
import { Info, Plus, Search, ChevronLeft, ChevronRight } from "lucide-vue-next";
</script>
