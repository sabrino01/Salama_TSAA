<script setup>
import Sidebar from "../../../assets/SidebarSuivi.vue";
import Navbar from "../../../assets/Navbar.vue";
import Footer from "../../../assets/Footer.vue";
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
                        Actions
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-4 font-poppins">
                        Dans l'espace actions pour le Responsable du Suivi, vous
                        pourriez voir et éditer les informations sur les
                        actions, que ce soit Audit Interne ou PTA.
                    </p>
                </div>

                <!-- Choix d'action à faire(A.I ou PTA) -->
                <div class="flex w-full mt-5 ml-4 justify-center space-x-4">
                    <!-- Lien Audit Interne -->
                    <router-link
                        to="/suivi/actions/auditinterne"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/suivi/actions/auditinterne',
                        }"
                    >
                        Audit Interne
                    </router-link>

                    <div class="border-r border-gray-300"></div>

                    <!-- Lien PTA -->
                    <router-link
                        to="/suivi/actions/pta"
                        class="flex items-center justify-center text-black text-2xl font-bold px-4 py-2 rounded-md w-38"
                        :class="{
                            'border-b-4 border-blue-600':
                                $route.path === '/suivi/actions/pta',
                        }"
                    >
                        PTA
                    </router-link>
                </div>

                <!-- Ajout et barre de recherche -->
                <div class="flex w-full mt-5 ml-4">
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

                    <!-- Toggle de pagination -->
                    <!-- <div class="relative ml-4">
                        <button
                            @click="togglePagination"
                            class="flex items-center justify-center border border-gray-400 text-black px-4 py-2 rounded-md"
                            :class="{
                                'bg-blue-100': !paginationEnabled,
                                'bg-white': paginationEnabled,
                            }"
                        >
                            {{
                                paginationEnabled
                                    ? "Désactiver Pagination"
                                    : "Activer Pagination"
                            }}
                        </button>
                    </div> -->
                </div>

                <!-- Tableau pour afficher les données d'audit interne -->
                <div class="mt-5 ml-4">
                    <!-- <Table
                        ref="tableRef"
                        :columns="columns"
                        :data="formattedActions"
                        :actions="actions"
                        :filterActions="filterActions"
                    /> -->
                </div>

                <!-- Footer -->
                <div class="flex w-full mt-5 justify-between">
                    <!-- Résultat -->
                    <!-- <div
                        class="flex items-center text-gray-500 justify-start px-4 space-x-2"
                    >
                        <span>Résultat</span>
                        <strong>{{
                            totalActions === 0
                                ? 0
                                : paginationEnabled
                                ? (currentPage - 1) * perPage + 1
                                : 1
                        }}</strong>
                        <span>à</span>
                        <strong>
                            {{
                                totalActions === 0
                                    ? 0
                                    : paginationEnabled
                                    ? Math.min(
                                          currentPage * perPage,
                                          totalActions
                                      )
                                    : totalActions
                            }}
                        </strong>
                        <span>sur</span>
                        <strong>{{ totalActions }}</strong>
                    </div> -->

                    <!-- Pagination - masquer quand elle est désactivée -->
                    <!-- <div
                        v-if="paginationEnabled"
                        class="flex items-center justify-end space-x-2"
                    >
                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="currentPage <= 1 || totalActions === 0"
                            @click="changerPage(currentPage - 1)"
                        >
                            <ChevronLeft class="w-4 h-4" /> Préc.
                        </button> -->

                    <!-- Numéros de page -->
                    <!-- <div class="flex space-x-1">
                            <button
                                v-for="page in pages"
                                :key="page"
                                class="flex items-center justify-center w-8 h-8 rounded-md border"
                                :class="
                                    page === currentPage
                                        ? 'bg-[#0062ff] text-white'
                                        : 'bg-white text-black border-gray-300'
                                "
                                @click="changerPage(page)"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            class="flex items-center bg-white text-black px-3 py-2 rounded-md border border-gray-300 shadow-sm"
                            :disabled="
                                currentPage >= lastPage || totalActions === 0
                            "
                            @click="changerPage(currentPage + 1)"
                        >
                            Suiv. <ChevronRight class="w-4 h-4" />
                        </button> -->
                    <!-- </div> -->
                </div>
            </div>

            <!-- Footer -->
            <Footer />
        </div>
    </div>
</template>
