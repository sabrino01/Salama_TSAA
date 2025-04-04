<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar class="w-64 bg-[#0062ff] text-white fixed h-full" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Navbar -->
            <Navbar />

            <!-- Contenu principal avec padding en bas -->
            <div class="flex-1 p-5 bg-gray-100 pb-16">
                <!-- Titre -->
                <div class="flex w-full">
                    <div
                        class="basis-[98%] text-4xl indent-4 font-bold text-gray-800"
                    >
                        Notifications
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace Notifications, vous pouvez voir et gérer
                        les membres de votre association, ajouter des membres et
                        de les supprimer.
                    </p>
                </div>

                <!-- Tableau notifications par récente-->
                <div class="mt-5 ml-4 flex w-full">
                    <table class="table-fixed w-[80%] h-[11.5rem]">
                        <thead class="bg-gray-200 text-lg h-[2.5rem]">
                            <tr>
                                <th class="text-start">
                                    <button
                                        class="bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                    >
                                        <ChevronLeft class="w-5 h-5" />
                                    </button>
                                </th>
                                <th>
                                    <div
                                        class="items-center text-gray-500 justify-start px-4 space-x-2"
                                    >
                                        <strong>1-10</strong>
                                        <span>sur</span>
                                        <strong>50</strong>
                                    </div>
                                </th>
                                <th class="text-end">
                                    <button
                                        class="items-center bg-white text-black px-4 py-2 rounded-md border border-gray-300 shadow-sm"
                                    >
                                        <ChevronRight class="w-5 h-5" />
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="font-poppins text-center">
                            <tr
                                v-for="(item, index) in tableData"
                                :key="index"
                                :class="{
                                    'border-b-4 border-green-500 bg-white text-black':
                                        isFutureDate(item.datesuivi),
                                    'border-b-4 border-red-500 bg-white text-black':
                                        !isFutureDate(item.datesuivi),
                                }"
                                class="shadow-md rounded-md transform transition-transform duration-200 hover:scale-105 mb-2"
                            >
                                <td class="px-4 py-2">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{ item.sources }}
                                    </router-link>
                                </td>
                                <td class="truncate px-4 py-4">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{
                                            item.sources === "Audit Interne"
                                                ? item.action
                                                : item.dns
                                        }}
                                    </router-link>
                                </td>
                                <td class="px-4 py-2">
                                    <router-link
                                        :to="
                                            getSourceLink(
                                                item.sources,
                                                item.sources === 'Audit Interne'
                                                    ? item.action
                                                    : item.dns,
                                                item.datesuivi
                                            )
                                        "
                                    >
                                        {{ item.datesuivi }}
                                        <span
                                            :class="
                                                getDaysDifferenceClass(
                                                    item.datesuivi
                                                )
                                            "
                                            class="ml-2 text-xs font-medium px-2 py-1 rounded"
                                        >
                                            {{
                                                getDaysDifference(
                                                    item.datesuivi
                                                )
                                            }}
                                        </span>
                                    </router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Activer notification -->
                    <div class="flex w-[20%] items-center flex-col space-y-4">
                        <!-- Toggle pour l'envoi d'email -->
                        <div class="flex items-center space-x-2">
                            <label
                                for="emailNotification"
                                class="text-gray-700 font-medium"
                            >
                                Notification par Email
                            </label>
                            <input
                                id="emailNotification"
                                type="checkbox"
                                v-model="emailNotification"
                                class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Toggle pour l'alerte dans l'application -->
                        <div class="flex items-center space-x-2">
                            <label
                                for="appAlert"
                                class="text-gray-700 font-medium"
                            >
                                Alerte dans l'application
                            </label>
                            <input
                                id="appAlert"
                                type="checkbox"
                                v-model="appAlert"
                                class="form-toggle h-5 w-10 rounded-full bg-gray-300 focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>

<script setup>
import Sidebar from "../assets/SidebarUser.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
import { Info, ChevronLeft, ChevronRight } from "lucide-vue-next";

import { ref, watch } from "vue";

import { ptaData } from "./actions/pta/Pta.vue"; // Import des données depuis Pta.vue
import { auditInterneData } from "./actions/auditinterne/AuditInterne.vue"; // Import des données depuis AuditInterne.vue

const tableData = ref([
    {
        sources: "Audit Interne",
        action: "Demande le statut",
        datesuivi: "14/04/2025",
    },
    {
        sources: "Audit Interne",
        action: "Exploiter les informations",
        datesuivi: "13/10/2025",
    },
    {
        sources: "PTA",
        dns: "Planifier les réunions",
        datesuivi: "15/11/2024",
    },
]);

// Fonction pour convertir une date au format JJ/MM/YYYY en un objet Date
const parseDate = (dateString) => {
    const [day, month, year] = dateString.split("/").map(Number); // Sépare JJ/MM/YYYY
    return new Date(year, month - 1, day); // Crée un objet Date (mois commence à 0)
};

// Fonction pour vérifier si une date est dans le futur
const isFutureDate = (dateString) => {
    const date = parseDate(dateString); // Convertit la date
    return date > new Date(); // Compare avec la date actuelle
};

// Fonction pour calculer la différence en jours entre la date de suivi et aujourd'hui
const getDaysDifference = (dateString) => {
    const today = new Date();
    // Réinitialiser l'heure pour une comparaison précise des jours
    today.setHours(0, 0, 0, 0);

    const targetDate = parseDate(dateString);

    // Calcul de la différence en millisecondes
    const differenceMs = targetDate - today;
    // Conversion en jours (1000ms * 60s * 60min * 24h)
    const differenceDays = Math.round(differenceMs / (1000 * 60 * 60 * 24));

    if (differenceDays > 0) {
        return `+${differenceDays} jour${differenceDays > 1 ? "s" : ""}`;
    } else if (differenceDays < 0) {
        return `${differenceDays} jour${differenceDays < -1 ? "s" : ""}`;
    } else {
        return "Aujourd'hui";
    }
};

// Fonction pour définir la classe CSS en fonction de la différence de jours
const getDaysDifferenceClass = (dateString) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const targetDate = parseDate(dateString);
    const differenceMs = targetDate - today;
    const differenceDays = Math.round(differenceMs / (1000 * 60 * 60 * 24));

    // Classes pour différents états
    if (differenceDays > 7) {
        return "bg-green-100 text-green-800"; // Plus d'une semaine - vert
    } else if (differenceDays > 0) {
        return "bg-yellow-100 text-yellow-800"; // Entre 1-7 jours - jaune
    } else if (differenceDays === 0) {
        return "bg-blue-100 text-blue-800"; // Aujourd'hui - bleu
    } else if (differenceDays > -7) {
        return "bg-orange-100 text-orange-800"; // Retard de moins d'une semaine - orange
    } else {
        return "bg-red-100 text-red-800"; // Retard important - rouge
    }
};

// Fonction pour vérifier si les données existent et générer le lien
const getSourceLink = (sources, actionOrDns, datesuivi) => {
    // Vérifie dans audit interne
    const existsInAuditInterne = auditInterneData.value.some(
        (auditItem) =>
            auditItem.sources === sources &&
            auditItem.action === actionOrDns &&
            auditItem.datesuivi === datesuivi
    );

    if (existsInAuditInterne) {
        return "/user/actions/auditinterne";
    }

    // Vérifie dans PTA
    const existsInPta = ptaData.value.some(
        (ptaItem) =>
            ptaItem.sources === sources &&
            ptaItem.dns === actionOrDns &&
            ptaItem.datesuivi === datesuivi
    );

    if (existsInPta) {
        return "/user/actions/pta";
    }

    // Redirection par défaut si aucune correspondance
    return "/";
};

// Variables pour les états des notifications
const emailNotification = ref(false); // État pour l'envoi d'email
const appAlert = ref(false); // État pour l'alerte dans l'application

// Watchers pour surveiller les changements d'état
watch(emailNotification, (newValue) => {
    if (newValue) {
        console.log("Notification Email activée");
        // Ajoutez ici la logique pour activer l'envoi d'email
    } else {
        console.log("Notification Email désactivée");
        // Ajoutez ici la logique pour désactiver l'envoi d'email
    }
});

watch(appAlert, (newValue) => {
    if (newValue) {
        console.log("Alerte Application activée");
        // Ajoutez ici la logique pour activer l'alerte dans l'application
    } else {
        console.log("Alerte Application désactivée");
        // Ajoutez ici la logique pour désactiver l'alerte dans l'application
    }
});
</script>

<style scoped>
.form-toggle {
    appearance: none;
    width: 2.5rem;
    height: 1.25rem;
    background-color: #d1d5db; /* Couleur grise par défaut */
    border-radius: 9999px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.form-toggle:checked {
    background-color: #3b82f6; /* Couleur bleue quand activé */
}

.form-toggle::before {
    content: "";
    position: absolute;
    top: 0.125rem;
    left: 0.125rem;
    width: 1rem;
    height: 1rem;
    background-color: white;
    border-radius: 9999px;
    transition: transform 0.2s ease-in-out;
}

.form-toggle:checked::before {
    transform: translateX(1.25rem); /* Déplace le cercle à droite */
}
</style>
