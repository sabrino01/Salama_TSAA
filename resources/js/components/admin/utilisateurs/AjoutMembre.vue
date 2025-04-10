<script setup>
import Sidebar from "../../assets/Sidebar.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";

import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const membre = reactive({
    nom: "",
    nom_utilisateur: "",
    email: "",
    departement: "",
    mot_de_passe: "",
    confirmer_mot_de_passe: "",
});
const erreurs = reactive({
    nom: "",
    nom_utilisateur: "",
    email: "",
    departement: "",
    mot_de_passe: "",
    confirmer_mot_de_passe: "",
});

const enregistrerMembre = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!membre.nom) erreurs.nom = "Le champ Nom est requis.";
    if (!membre.nom_utilisateur)
        erreurs.nom_utilisateur = "Le champ Nom d'utilisateur est requis.";
    if (!membre.email) erreurs.email = "Le champ Email est requis.";
    if (!membre.departement)
        erreurs.departement = "Le champ Département est requis.";
    if (!membre.mot_de_passe)
        erreurs.mot_de_passe = "Le champ Mot de passe est requis.";
    if (membre.mot_de_passe !== membre.confirmer_mot_de_passe) {
        erreurs.confirmer_mot_de_passe =
            "Les mots de passe ne correspondent pas.";
    }

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const userData = {
            ...membre,
        };

        await axios.post("/api/users", userData).then(() => {
            router.push("/admin/utilisateurs/membres");
            toast.success("Membre ajouté avec succès !");
        });
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                erreurs[key] = error.response.data.errors[key][0];
            });
        }
    }
};
</script>

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
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="nom"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.nom,
                                    'border-red-500': erreurs.nom,
                                }"
                                v-model="membre.nom"
                            />
                            <p
                                v-if="erreurs.nom"
                                class="flex text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.nom }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="nomutilisateur"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Nom d'utilisateur :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="nomutilisateur"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.nom_utilisateur,
                                    'border-red-500': erreurs.nom_utilisateur,
                                }"
                                v-model="membre.nom_utilisateur"
                            />
                            <p
                                v-if="erreurs.nom_utilisateur"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.nom_utilisateur }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="email"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Email :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="email"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.email,
                                    'border-red-500': erreurs.email,
                                }"
                                v-model="membre.email"
                            />
                            <p
                                v-if="erreurs.email"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.email }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="departement"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Département :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="text"
                                id="departement"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.departement,
                                    'border-red-500': erreurs.departement,
                                }"
                                v-model="membre.departement"
                            />
                            <p
                                v-if="erreurs.departement"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.departement }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="mot de passe"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Mot de passe :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="password"
                                id="mot de passe"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400': !erreurs.mot_de_passe,
                                    'border-red-500': erreurs.mot_de_passe,
                                }"
                                v-model="membre.mot_de_passe"
                                required
                            />
                            <p
                                v-if="erreurs.mot_de_passe"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.mot_de_passe }}
                            </p>
                        </div>
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="confirmer mot de passe"
                            class="w-[26%] ml-4 text-lg font-semibold text-gray-800"
                        >
                            Confirmer Mot de passe :
                        </label>
                        <div class="w-[50%]">
                            <input
                                type="password"
                                id="confirmer mot de passe"
                                class="w-full border rounded-md px-4 py-2 bg-transparent"
                                :class="{
                                    'border-gray-400':
                                        !erreurs.confirmer_mot_de_passe,
                                    'border-red-500':
                                        erreurs.confirmer_mot_de_passe,
                                }"
                                v-model="membre.confirmer_mot_de_passe"
                            />
                            <p
                                v-if="erreurs.confirmer_mot_de_passe"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ erreurs.confirmer_mot_de_passe }}
                            </p>
                        </div>
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
                            @click="enregistrerMembre"
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
