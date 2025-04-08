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
                        Profile
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace profile, vous pouvez voir les informations
                        de votre compte, de les modifier et de faire plus.
                    </p>
                </div>

                <!-- Information et modification sur le profile -->
                <div class="w-full mt-5">
                    <div class="flex w-[60%] items-center">
                        <span
                            for="nom"
                            class="w-[27%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Nom :
                        </span>
                        <input
                            type="text"
                            id="nom"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            value="RATOVOARIVONY Aina"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            for="nom d'utilisateur"
                            class="w-[27%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Nom d'utilisateur :
                        </span>
                        <input
                            type="text"
                            id="nomutilisateur"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            value="Aina"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            for="email"
                            class="w-[27%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Email :
                        </span>
                        <input
                            type="text"
                            id="email"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            value="aina@gmail.com"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            for="departement"
                            class="w-[27%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Département :
                        </span>
                        <input
                            type="text"
                            id="departement"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                            value="RSI"
                        />
                    </div>
                    <div class="flex w-[47%] justify-end mt-5">
                        <button
                            class="w-[15%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                        >
                            Modifier
                        </button>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <span
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Changer le mot de passe
                        </span>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="mot de passe"
                            class="w-[25%] ml-10 text-lg font-semibold text-gray-800"
                        >
                            Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="mot de passe"
                            class="w-[49.5%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="confirmer mot de passe"
                            class="w-[25%] ml-10 text-lg font-semibold text-gray-800"
                        >
                            Confirmer Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="confirmer mot de passe"
                            class="w-[49.5%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[47%] justify-end mt-5">
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
import Sidebar from "../../assets/Sidebar.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";
</script>
