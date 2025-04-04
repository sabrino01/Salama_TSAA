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
                        class="basis-[98%] text-4xl text-center font-bold text-gray-800"
                    >
                        Bienvenue au Tableau de bord
                        <h1>Salama_tsaa</h1>
                    </div>
                    <div class="basis-[2%]">
                        <Info />
                    </div>
                </div>

                <!-- Phrase introductive -->
                <div class="w-full text-gray-600 mt-5">
                    <p class="indent-12 font-poppins">
                        Salama_tsaa est une application qui permet de bien
                        suivre les actions ou faire des améliorations et encore
                        plus. Vous pourriez aussi voir et consulter les
                        statistiques ainsi que d'autres informations.
                    </p>
                </div>

                <!-- Titre statistique -->
                <div
                    class="w-full mt-5 text-center underline underline-offset-4"
                >
                    <h2 class="text-4xl font-medium text-gray-800">
                        Statistiques des actions
                    </h2>
                </div>

                <!-- Audit Interne | PTA -->
                <div class="flex w-full mt-8">
                    <!-- Audit Interne -->
                    <div class="w-1/2 pr-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Statistique et Tableau de Constat pour Audit Interne
                        </h3>

                        <!-- Recherche -->
                        <div class="flex w-full ml-2 mt-2">
                            <div class="w-1/2 text-end">
                                <p class="text-gray-600 mt-2">Recherche :</p>
                            </div>
                            <div class="w-1/2 ml-6">
                                <select
                                    class="rounded-lg w-32 border border-gray-200 p-1.5"
                                    name="recherche1"
                                    id="recherche1"
                                >
                                    <option value="1">Jours</option>
                                    <option value="2">Mois</option>
                                    <option value="3">Ans</option>
                                </select>
                            </div>
                        </div>

                        <!-- Résultat recherche -->
                        <!-- Cette partie est contrôler avec javascript -->
                        <div class="flex w-full ml-2 mt-2">
                            <div class="w-1/2 text-center">
                                <input
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5"
                                />
                            </div>
                            <div class="w-1/2 justify-items-center">
                                <label
                                    class="relative inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        v-model="isToggled"
                                    />
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                    ></div>
                                    <span
                                        class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                    ></span>
                                </label>
                                <p class="mt-2 text-gray-600">
                                    Toggle is: {{ isToggled ? "ON" : "OFF" }}
                                </p>
                            </div>
                        </div>

                        <!-- Statistique -->
                        <div class="mt-5">
                            <BarChart class="h-[24rem]" />
                        </div>

                        <!-- Tableau de constat -->
                        <table
                            class="table-fixed h-[14rem] bg-white border rounded-lg w-full mt-5"
                        >
                            <thead class="bg-gray-200 text-lg h-[2.5rem]">
                                <tr>
                                    <th>Type de constat</th>
                                    <th>Nombres de constat</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>A.A</td>
                                    <td>25</td>
                                    <td>2.5</td>
                                </tr>
                                <tr>
                                    <td>N.R</td>
                                    <td>30</td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td>R</td>
                                    <td>40</td>
                                    <td>4</td>
                                </tr>
                                <tr>
                                    <td>R.P</td>
                                    <td>38</td>
                                    <td>3.8</td>
                                </tr>
                            </tbody>
                            <tfoot
                                class="bg-gray-200 h-[2.5rem] text-center font-bold"
                            >
                                <tr>
                                    <td class="text-lg">Total</td>
                                    <td>133</td>
                                    <td>13.3%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Ligne verticale -->
                    <div class="border-l border-gray-300"></div>

                    <!-- PTA -->
                    <div class="w-1/2 pl-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Statistique et Tableau de Constat pour PTA
                        </h3>

                        <!-- Recherche -->
                        <div class="flex w-full mt-2">
                            <div class="w-1/2 text-end">
                                <p class="text-gray-600 mt-2">Recherche :</p>
                            </div>
                            <div class="w-1/2 ml-6">
                                <select
                                    class="rounded-lg w-32 border border-gray-200 p-1.5"
                                    name="recherche2"
                                    id="recherche2"
                                >
                                    <option value="1">Jours</option>
                                    <option value="2">Mois</option>
                                    <option value="3">Ans</option>
                                </select>
                            </div>
                        </div>

                        <!-- Résultat recherche -->
                        <!-- Cette partie est contrôler avec javascript -->
                        <div class="flex w-full ml-2 mt-2">
                            <div class="w-1/2 text-center">
                                <input
                                    type="date"
                                    class="rounded-lg w-36 border border-gray-200 p-1.5"
                                />
                            </div>
                            <div class="w-1/2 justify-items-center">
                                <label
                                    class="relative inline-flex items-center cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        class="sr-only peer"
                                        v-model="isToggled"
                                    />
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                    ></div>
                                    <span
                                        class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                    ></span>
                                </label>
                                <p class="mt-2 text-gray-600">
                                    Toggle is: {{ isToggled ? "ON" : "OFF" }}
                                </p>
                            </div>
                        </div>

                        <!-- Statistique -->
                        <div class="mt-5">
                            <BarChart class="h-[24rem]" />
                        </div>

                        <!-- Tableau de constat -->
                        <table
                            class="table-fixed h-[14rem] bg-white border rounded-lg w-full mt-5"
                        >
                            <thead class="bg-gray-200 text-lg h-[2.5rem]">
                                <tr>
                                    <th>Type de constat</th>
                                    <th>Nombres de constat</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>A.A</td>
                                    <td>25</td>
                                    <td>2.5</td>
                                </tr>
                                <tr>
                                    <td>N.R</td>
                                    <td>30</td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td>R</td>
                                    <td>40</td>
                                    <td>4</td>
                                </tr>
                                <tr>
                                    <td>R.P</td>
                                    <td>38</td>
                                    <td>3.8</td>
                                </tr>
                            </tbody>
                            <tfoot
                                class="bg-gray-200 h-[2.5rem] text-center font-bold"
                            >
                                <tr>
                                    <td class="text-lg">Total</td>
                                    <td>133</td>
                                    <td>13.3%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Titre actions -->
                <div
                    class="w-full mt-10 text-center underline underline-offset-4"
                >
                    <h2 class="text-4xl font-medium text-gray-800">Actions</h2>
                </div>

                <!-- Audit Interne | PTA -->
                <div class="flex w-full mt-6">
                    <!-- Audit Interne -->
                    <div class="w-1/2 pr-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            Audit Interne
                        </h3>
                        <div
                            class="flex w-full mt-5 justify-between space-x-5 text-white"
                        >
                            <div
                                class="flex w-1/3 items-center bg-green-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">En cours</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    19
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-gray-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Clôturé</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    42
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-purple-600 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Abandonné</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    10
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne verticale -->
                    <div class="border-l border-gray-300"></div>

                    <!-- PTA -->
                    <div class="w-1/2 pl-4">
                        <h3
                            class="text-2xl text-center border-b h-10 font-semibold text-gray-800"
                        >
                            PTA
                        </h3>
                        <div
                            class="flex w-full mt-5 justify-between space-x-5 text-white"
                        >
                            <div
                                class="flex w-1/3 items-center bg-green-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">En cours</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    23
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-gray-500 p-8 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Clôturé</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    37
                                </p>
                            </div>
                            <div
                                class="flex w-1/3 items-center bg-purple-600 p-6 rounded-md shadow-lg"
                            >
                                <p class="w-[50%] justify-center">Abandonné</p>
                                <p class="w-[50%] text-end text-lg font-bold">
                                    8
                                </p>
                            </div>
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
import BarChart from "../charts/BarChart.vue";

import { ref } from "vue";

const isToggled = ref(false);

import { Info } from "lucide-vue-next";
</script>
