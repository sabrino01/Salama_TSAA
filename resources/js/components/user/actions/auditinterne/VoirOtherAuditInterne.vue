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
                        Voir Audit Interne
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans cet espace, vous pourriez voir et connaître plus
                        d'informations sur l'Audit Interne sélectionnée.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <span
                            class="w-[6%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Date :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >14/10/2024</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Action :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Demande de statut</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Source :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Audit Interne</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[14%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Type d'actions :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Action Corrective</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[13%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Responsable :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Directeur Générale</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[6%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Suivi :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Dominique</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[11%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Fréquence :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Mensuel</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[9%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Constat :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Non réalisé</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[8%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Mesure :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >Aucune mesure à prendre</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[6%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Mois :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >13/10/2024</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[12%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Obsérvation :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >C'est pas trop grave comme problème</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[10%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Date Suivi :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >14/12/2024</span
                        >
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[7%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Statut :
                        </span>
                        <span class="w-[50%] text-lg font-semibold"
                            >En cours</span
                        >
                    </div>
                    <div class="flex w-[61.6%] justify-center mt-5">
                        <router-link to="/user/actions/auditinterne"
                            ><button
                                class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                            >
                                Retour
                            </button></router-link
                        >
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "../../../assets/SidebarUser.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
</script>
