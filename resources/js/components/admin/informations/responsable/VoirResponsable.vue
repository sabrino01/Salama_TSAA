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
                        Voir Responsable
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Voir le responsable pour connaître plus d'informations
                        sur le responsable sélectionné.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <span
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Code :
                        </span>
                        <span class="w-[50%] px-4 text-lg font-semibold"
                            >DG</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Libelle :
                        </span>
                        <span class="w-[50%] px-4 text-lg font-semibold"
                            >Directeur Générale</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Description :
                        </span>
                        <span class="w-[50%] px-4 text-lg font-semibold"
                            >dg.salama@iris.mg</span
                        >
                    </div>
                    <div class="flex w-[61.6%] justify-center mt-5">
                        <router-link to="/admin/informations/responsable"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                        <button
                            class="w-[12%] bg-green-500 text-white font-semibold rounded-md px-4 py-2"
                        >
                            <router-link
                                to="/admin/informations/responsable/editer"
                                >Editer</router-link
                            >
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
import Sidebar from "../../assets/Sidebar.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";
</script>
