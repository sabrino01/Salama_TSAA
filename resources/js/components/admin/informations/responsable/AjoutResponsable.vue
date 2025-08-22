<script setup>
import Sidebar from "../../../assets/Sidebar.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";

import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    // Sauvegarde l'état dans le localStorage
    localStorage.setItem("sidebar-collapsed", collapsed);
};

const router = useRouter();

const responsable = reactive({
    code: "",
    libelle: "",
    description: "", // Valeur par défaut
    email: "",
    nom_utilisateur: "",
    mot_de_passe: "",
});
const erreurs = reactive({
    code: "",
    libelle: "",
    description: "",
    email: "",
    nom_utilisateur: "",
    mot_de_passe: "",
});

const enregistrerResponsable = async () => {
    // Réinitialiser les erreurs
    Object.keys(erreurs).forEach((key) => (erreurs[key] = ""));

    // Validation côté frontend
    if (!responsable.code)
        erreurs.code = "Le champ Code pour le Responsable est requis.";
    if (!responsable.libelle)
        erreurs.libelle = "Le champ Libelle pour le Responsable est requis.";
    if (!responsable.description)
        erreurs.description =
            "Le champ Description pour le Responsable est requis.";
    if (!responsable.email)
        erreurs.email = "Le champ Email pour le Responsable est requis.";
    if (!responsable.nom_utilisateur)
        erreurs.nom_utilisateur =
            "Le champ Nom d'utilisateur pour le Responsable est requis.";
    if (!responsable.mot_de_passe)
        erreurs.mot_de_passe =
            "Le champ du Mot de passe pour le Responsable est requis.";

    // Si des erreurs existent, arrêter l'exécution
    if (Object.values(erreurs).some((err) => err)) return;

    try {
        const userData = {
            ...responsable,
        };

        await axios.post("/api/responsable", userData).then(() => {
            router.push("/admin/informations/responsable");
            toast.success("Responsable ajouté avec succès !");
        });
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                erreurs[key] = error.response.data.errors[key][0];
            });
        }
    }
};

// Fonction pour réinitialiser l'erreur d'un champ spécifique
const resetError = (field) => {
    erreurs[field] = "";
};

onMounted(() => {
    // Récupère l'état du sidebar depuis le localStorage
    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});
</script>
<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar @sidebar-toggle="handleSidebarToggle" />

        <!-- Main Content -->
        <div
            :class="[
                'flex-1 flex flex-col transition-all duration-300',
                isSidebarCollapsed ? 'ml-16' : 'ml-64',
            ]"
        >
            <Navbar v-if="true" :isSidebarCollapsed="isSidebarCollapsed" />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-5">
                    <!-- Titre -->
                    <div class="flex w-full">
                        <div
                            class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                        >
                            Ajout Responsable
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[800px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-4 font-poppins">
                                Ajouter un responsable pour savoir le
                                responsable d'une action.
                            </p>
                        </div>

                        <!-- Formulaire d'ajout du responsable -->
                        <div class="w-full mt-5">
                            <div class="flex w-[60%] items-center">
                                <label
                                    for="code"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Code :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="code"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.code,
                                            'border-red-500': erreurs.code,
                                        }"
                                        v-model="responsable.code"
                                        @input="resetError('code')"
                                    />
                                    <p
                                        v-if="erreurs.code"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.code }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="libelle"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Libelle :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="libelle"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.libelle,
                                            'border-red-500': erreurs.libelle,
                                        }"
                                        v-model="responsable.libelle"
                                        @input="resetError('libelle')"
                                    />
                                    <p
                                        v-if="erreurs.libelle"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.libelle }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="description"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Description :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="description"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.description,
                                            'border-red-500':
                                                erreurs.description,
                                        }"
                                        v-model="responsable.description"
                                        @input="resetError('description')"
                                    />
                                    <p
                                        v-if="erreurs.description"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.description }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="email"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Email :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="email"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400': !erreurs.email,
                                            'border-red-500': erreurs.email,
                                        }"
                                        v-model="responsable.email"
                                        @input="resetError('email')"
                                    />
                                    <p
                                        v-if="erreurs.email"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.email }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="nom_utilisateur"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Nom d'utilisateur :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="text"
                                        id="nom_utilisateur"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.nom_utilisateur,
                                            'border-red-500':
                                                erreurs.nom_utilisateur,
                                        }"
                                        v-model="responsable.nom_utilisateur"
                                        @input="resetError('nom_utilisateur')"
                                    />
                                    <p
                                        v-if="erreurs.nom_utilisateur"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.nom_utilisateur }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[60%] items-center mt-5">
                                <label
                                    for="mot_de_passe"
                                    class="w-[17%] ml-4 text-lg font-semibold text-gray-800"
                                >
                                    Mot de passe :
                                </label>
                                <div class="w-[50%]">
                                    <input
                                        type="password"
                                        id="mot_de_passe"
                                        class="w-full border border-gray-400 rounded-md px-4 py-2 bg-transparent"
                                        :class="{
                                            'border-gray-400':
                                                !erreurs.mot_de_passe,
                                            'border-red-500':
                                                erreurs.mot_de_passe,
                                        }"
                                        v-model="responsable.mot_de_passe"
                                        @input="resetError('mot_de_passe')"
                                    />
                                    <p
                                        v-if="erreurs.mot_de_passe"
                                        class="flex text-red-500 text-sm mt-1"
                                    >
                                        {{ erreurs.mot_de_passe }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex w-[41%] justify-end mt-5">
                                <router-link
                                    to="/admin/informations/responsable"
                                    ><button
                                        class="w-[15%] transparent text-black font-semibold rounded-md px-4 py-2"
                                    >
                                        Retour
                                    </button></router-link
                                >
                                <button
                                    @click="enregistrerResponsable"
                                    class="w-[18%] bg-[#0062ff] text-white font-semibold rounded-md px-4 py-2"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>
