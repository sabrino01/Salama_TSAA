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
                            v-model="userData.nom"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
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
                            v-model="userData.nom_utilisateur"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
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
                            v-model="userData.email"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
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
                            v-model="userData.departement"
                            class="w-[50%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[47%] justify-end mt-5">
                        <button
                            @click="updateProfile"
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
                            for="mot_de_passe"
                            class="w-[25%] ml-10 text-lg font-semibold text-gray-800"
                        >
                            Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="mot_de_passe"
                            v-model="passwordData.mot_de_passe"
                            class="w-[49.5%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[60%] items-center mt-5">
                        <label
                            for="confirmer_mot_de_passe"
                            class="w-[25%] ml-10 text-lg font-semibold text-gray-800"
                        >
                            Confirmer Mot de passe :
                        </label>
                        <input
                            type="password"
                            id="confirmer_mot_de_passe"
                            v-model="passwordData.confirmer_mot_de_passe"
                            class="w-[49.5%] border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                        />
                    </div>
                    <div class="flex w-[47%] justify-end mt-5">
                        <button
                            @click="updatePassword"
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
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import Sidebar from "../../assets/Sidebar.vue";
import Navbar from "../../assets/Navbar.vue";
import Footer from "../../assets/Footer.vue";
import emitter from "../../../utils/eventBus";

const route = useRoute();
const userData = ref({
    nom: "",
    nom_utilisateur: "",
    email: "",
    departement: "",
});

// Ajouter un nouvel état pour les mots de passe
const passwordData = ref({
    mot_de_passe: "",
    confirmer_mot_de_passe: "",
});

const loadUserData = async () => {
    try {
        const response = await axios.get(`/api/users/${route.params.id}`);
        userData.value = response.data;
    } catch (error) {
        toast.error("Erreur lors du chargement des données:", error);
    }
};

const updateProfile = async () => {
    try {
        const response = await axios.put(
            `/api/users/${route.params.id}`,
            userData.value
        );

        // Mettre à jour le localStorage
        const currentUser = JSON.parse(localStorage.getItem("user"));
        const updatedUser = { ...currentUser, ...userData.value };
        localStorage.setItem("user", JSON.stringify(updatedUser));

        // Émettre l'événement de mise à jour
        emitter.emit("user-updated", updatedUser);
    } catch (error) {
        toast.error("Erreur lors de la mise à jour", error);
    }
};

const updatePassword = async () => {
    try {
        // Vérifier si les mots de passe correspondent
        if (
            passwordData.value.mot_de_passe !==
            passwordData.value.confirmer_mot_de_passe
        ) {
            toast.error("Les mots de passe ne correspondent pas");
            return;
        }

        // Vérifier si le mot de passe n'est pas vide
        if (!passwordData.value.mot_de_passe) {
            toast.error("Le mot de passe ne peut pas être vide");
            return;
        }

        const response = await axios.put(
            `/api/users/${route.params.id}/password`,
            {
                mot_de_passe: passwordData.value.mot_de_passe,
            }
        );

        // Réinitialiser les champs après la mise à jour
        passwordData.value.mot_de_passe = "";
        passwordData.value.confirmer_mot_de_passe = "";

        // Afficher un message de succès
        toast.success("Mot de passe mis à jour avec succès");
    } catch (error) {
        toast.error("Erreur lors de la mise à jour du mot de passe", error);
    }
};

onMounted(() => {
    loadUserData();
});
</script>
