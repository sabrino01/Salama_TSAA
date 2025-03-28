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
                        Ajout Membre
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Ajouter un nouveau membre à votre association en
                        remplissant le formulaire ci-dessous pour qu'il puisse
                        ou elle y accéder à l'application.
                    </p>
                </div>

                <!-- Formulaire d'ajout de membre -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <label
                            for="nom"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Nom :
                        </label>
                        <input
                            type="text"
                            id="nom"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="nom d'utilisateur"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Nom d'utilisateur :
                        </label>
                        <input
                            type="text"
                            id="nomutilisateur"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="departement"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Département :
                        </label>
                        <input
                            type="text"
                            id="departement"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="mot de passe"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="mot de passe"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="confirmer mot de passe"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Confirmer Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="confirmer mot de passe"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[46.6%] justify-end mt-5">
                        <router-link to="/admin/utilisateurs/membres"
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
import Sidebar from "../assets/Sidebar.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
</script>
