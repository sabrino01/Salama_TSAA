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
                            class="basis-[98%] text-4xl text-center font-bold text-gray-800"
                        >
                            Bienvenue au Tableau de bord
                            <h1>Salama_tsaa</h1>
                        </div>
                        <div class="basis-[2%]">
                            <Info />
                        </div>
                    </div>

                    <div class="min-h-[150px]">
                        <!-- Phrase introductive -->
                        <div class="w-full text-gray-600 mt-5">
                            <p class="indent-12 font-poppins">
                                Salama_tsaa est une application qui permet de
                                bien suivre les actions ou faire des
                                améliorations et encore plus. Vous pourriez
                                aussi voir et consulter les statistiques ainsi
                                que d'autres informations.
                            </p>
                        </div>

                        <!-- Audit Interne | PTA | Audit Externe | CAC | SWOT | Enquête de Satisfaction -->
                        <!-- Carousel Container -->
                        <div class="max-w-full mx-auto mt-6">
                            <!-- Carousel Header -->
                            <div class="flex justify-between items-center mb-8">
                                <h1
                                    class="text-3xl underline font-bold text-gray-800"
                                >
                                    Statistiques et Tableaux de Constats
                                </h1>

                                <!-- Navigation Buttons -->
                                <div class="flex space-x-2">
                                    <button
                                        @click="previousSlide"
                                        :disabled="currentSlide === 0"
                                        class="px-4 py-2 rounded-full border transition-all duration-200"
                                        :class="[
                                            currentSlide === 0
                                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                                : 'bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-gray-300',
                                        ]"
                                    >
                                        <ChevronLeft class="w-5 h-5" />
                                    </button>

                                    <button
                                        @click="nextSlide"
                                        :disabled="
                                            currentSlide === totalSlides - 1
                                        "
                                        class="px-4 py-2 rounded-full border transition-all duration-200"
                                        :class="[
                                            currentSlide === totalSlides - 1
                                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                                : 'bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-gray-300',
                                        ]"
                                    >
                                        <ChevronRight class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Slide Indicators -->
                            <div class="flex justify-center mb-6">
                                <div class="flex space-x-2">
                                    <div
                                        v-for="(slide, index) in totalSlides"
                                        :key="index"
                                        @click="goToSlide(index)"
                                        class="w-3 h-3 rounded-full cursor-pointer transition-all duration-200"
                                        :class="[
                                            currentSlide === index
                                                ? 'bg-blue-600'
                                                : 'bg-gray-300 hover:bg-gray-400',
                                        ]"
                                    ></div>
                                </div>
                            </div>

                            <!-- Carousel Content -->
                            <div class="relative overflow-hidden">
                                <div
                                    class="flex transition-transform duration-500 ease-in-out"
                                    :style="{
                                        transform: `translateX(-${
                                            currentSlide * 100
                                        }%)`,
                                    }"
                                >
                                    <!-- Slide 1: AI + PTA -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-8"
                                        >
                                            <!-- Audit Interne (AI) -->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour Audit Interne
                                                </h3>

                                                <!-- Search Section AI -->
                                                <div class="space-y-2 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                rechercheAI.type
                                                            "
                                                            @change="
                                                                resetSearchAI
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection AI -->
                                                    <div
                                                        v-if="rechercheAI.type"
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        rechercheAI.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheAI.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        rechercheAI.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheAI.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheAI.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        rechercheAI.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheAI.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledAI
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledAI
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledAI
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            rechercheAI.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheAI.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            rechercheAI.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheAI.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheAI.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            rechercheAI.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheAI.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        rechercheAI.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Action Buttons -->
                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-6"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherAI
                                                            "
                                                            :disabled="
                                                                isLoadingAI
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingAI
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="AI"
                                                            container-selector="#statistiques-ai-container"
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Error Message -->
                                                <div
                                                    v-if="errorMessageAI"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessageAI }}
                                                </div>

                                                <!-- Results Section -->
                                                <div
                                                    id="statistiques-ai-container"
                                                >
                                                    <!-- Chart -->
                                                    <div
                                                        v-if="
                                                            resultatsRechercheAI.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataAI
                                                            "
                                                            chart-title="Statistiques Audit Interne"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <!-- No Results Message -->
                                                    <div
                                                        v-if="
                                                            rechercheAI.type &&
                                                            resultatsRechercheAI.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <!-- Results Table -->
                                                    <div
                                                        v-if="
                                                            resultatsRechercheAI.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRechercheAI"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsAI
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentageAI.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PTA (Même structure que AI) -->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour PTA
                                                </h3>

                                                <!-- Search Section PTA (même structure que AI mais avec recherchePTA) -->
                                                <div class="space-y-4 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                recherchePTA.type
                                                            "
                                                            @change="
                                                                resetSearchPTA
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection PTA -->
                                                    <div
                                                        v-if="recherchePTA.type"
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        recherchePTA.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        recherchePTA.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        recherchePTA.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            recherchePTA.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            recherchePTA.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        recherchePTA.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        recherchePTA.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledPTA
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledPTA
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledPTA
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            recherchePTA.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            recherchePTA.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            recherchePTA.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                recherchePTA.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                recherchePTA.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            recherchePTA.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            recherchePTA.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        recherchePTA.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-4"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherPTA
                                                            "
                                                            :disabled="
                                                                isLoadingPTA
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingPTA
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="PTA"
                                                            container-selector="#statistiques-pta-container"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="errorMessagePTA"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessagePTA }}
                                                </div>

                                                <div
                                                    id="statistiques-pta-container"
                                                >
                                                    <div
                                                        v-if="
                                                            resultatsRecherchePTA.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataPTA
                                                            "
                                                            chart-title="Statistiques PTA"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="
                                                            recherchePTA.type &&
                                                            resultatsRecherchePTA.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <div
                                                        v-if="
                                                            resultatsRecherchePTA.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRecherchePTA"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsPTA
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentagePTA.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Slide 2: AE + Autre statistique -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-8"
                                        >
                                            <!-- Audit Externe (AE) -->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour Audit Externe
                                                </h3>

                                                <!-- Search Section AE (même structure) -->
                                                <div class="space-y-4 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                rechercheAE.type
                                                            "
                                                            @change="
                                                                resetSearchAE
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection AE -->
                                                    <div
                                                        v-if="rechercheAE.type"
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        rechercheAE.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheAE.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        rechercheAE.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheAE.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheAE.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        rechercheAE.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheAE.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledAE
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledAE
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledAE
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            rechercheAE.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheAE.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            rechercheAE.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheAE.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheAE.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            rechercheAE.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheAE.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        rechercheAE.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-4"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherAE
                                                            "
                                                            :disabled="
                                                                isLoadingAE
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingAE
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="AE"
                                                            container-selector="#statistiques-ae-container"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="errorMessageAE"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessageAE }}
                                                </div>

                                                <div
                                                    id="statistiques-ae-container"
                                                >
                                                    <div
                                                        v-if="
                                                            resultatsRechercheAE.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataAE
                                                            "
                                                            chart-title="Statistiques Audit Externe"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="
                                                            rechercheAE.type &&
                                                            resultatsRechercheAE.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <div
                                                        v-if="
                                                            resultatsRechercheAE.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRechercheAE"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsAE
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentageAE.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Placeholder pour le statistique CAC -->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour CAC
                                                </h3>

                                                <!-- Search Section CAC (même structure) -->
                                                <div class="space-y-4 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                rechercheCAC.type
                                                            "
                                                            @change="
                                                                resetSearchCAC
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection CAC -->
                                                    <div
                                                        v-if="rechercheCAC.type"
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        rechercheCAC.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheCAC.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        rechercheAE.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheCAC.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheCAC.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        rechercheCAC.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheAE.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledCAC
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledCAC
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledCAC
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            rechercheCAC.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheCAC.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            rechercheCAC.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheCAC.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheCAC.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            rechercheCAC.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheCAC.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        rechercheCAC.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-4"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherCAC
                                                            "
                                                            :disabled="
                                                                isLoadingCAC
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingCAC
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="CAC"
                                                            container-selector="#statistiques-cac-container"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="errorMessageCAC"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessageCAC }}
                                                </div>

                                                <div
                                                    id="statistiques-cac-container"
                                                >
                                                    <div
                                                        v-if="
                                                            resultatsRechercheCAC.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataCAC
                                                            "
                                                            chart-title="Statistiques CAC"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="
                                                            rechercheCAC.type &&
                                                            resultatsRechercheCAC.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <div
                                                        v-if="
                                                            resultatsRechercheCAC.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRechercheCAC"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsCAC
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentageCAC.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Slide 3: Statistique pour SWOT et Enquête de Satisfaction -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-8"
                                        >
                                            <!-- Statistique SWOT -->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour SWOT
                                                </h3>

                                                <!-- Search Section SWOT (même structure) -->
                                                <div class="space-y-4 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                rechercheSWOT.type
                                                            "
                                                            @change="
                                                                resetSearchSWOT
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection SWOT -->
                                                    <div
                                                        v-if="
                                                            rechercheSWOT.type
                                                        "
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        rechercheSWOT.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheSWOT.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        rechercheSWOT.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheSWOT.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheSWOT.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        rechercheSWOT.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheSWOT.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledSWOT
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledSWOT
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledSWOT
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            rechercheSWOT.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheSWOT.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            rechercheSWOT.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheSWOT.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheSWOT.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            rechercheSWOT.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheSWOT.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        rechercheSWOT.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-4"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherSWOT
                                                            "
                                                            :disabled="
                                                                isLoadingSWOT
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingSWOT
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="SWOT"
                                                            container-selector="#statistiques-swot-container"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="errorMessageSWOT"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessageSWOT }}
                                                </div>

                                                <div
                                                    id="statistiques-swot-container"
                                                >
                                                    <div
                                                        v-if="
                                                            resultatsRechercheSWOT.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataSWOT
                                                            "
                                                            chart-title="Statistiques SWOT"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="
                                                            rechercheSWOT.type &&
                                                            resultatsRechercheSWOT.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <div
                                                        v-if="
                                                            resultatsRechercheSWOT.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRechercheSWOT"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsSWOT
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentageSWOT.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Statistique 6: Enquête de Satisfaction-->
                                            <div
                                                class="bg-white rounded-lg shadow-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl font-semibold text-gray-800 text-center border-b pb-4 mb-6"
                                                >
                                                    Statistique et Tableau de
                                                    Constat pour Enquête de
                                                    Satisfaction
                                                </h3>

                                                <!-- Search Section ES (même structure) -->
                                                <div class="space-y-4 mb-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-between gap-4"
                                                    >
                                                        <label
                                                            class="text-gray-600 font-medium"
                                                            >Recherche :</label
                                                        >
                                                        <select
                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                            v-model="
                                                                rechercheES.type
                                                            "
                                                            @change="
                                                                resetSearchES
                                                            "
                                                        >
                                                            <option value="1">
                                                                Jours
                                                            </option>
                                                            <option value="2">
                                                                Mois
                                                            </option>
                                                            <option value="3">
                                                                Ans
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Date Selection ES -->
                                                    <div
                                                        v-if="rechercheES.type"
                                                        class="space-y-4"
                                                    >
                                                        <div
                                                            class="flex flex-row items-center w-full justify-between gap-6"
                                                        >
                                                            <!-- Extrême gauche : date début + toggle + date fin -->
                                                            <div
                                                                class="flex flex-row items-center flex-none gap-2"
                                                            >
                                                                <!-- Date début -->
                                                                <input
                                                                    v-if="
                                                                        rechercheES.type ===
                                                                        '1'
                                                                    "
                                                                    type="date"
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheES.dateDebut
                                                                    "
                                                                />
                                                                <div
                                                                    v-else-if="
                                                                        rechercheES.type ===
                                                                        '2'
                                                                    "
                                                                    class="flex gap-2"
                                                                >
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheES.mois
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in 12"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                                    .toString()
                                                                                    .padStart(
                                                                                        2,
                                                                                        "0"
                                                                                    )
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                    <select
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                        v-model="
                                                                            rechercheES.annee
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <select
                                                                    v-else-if="
                                                                        rechercheES.type ===
                                                                        '3'
                                                                    "
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-[140px]"
                                                                    v-model="
                                                                        rechercheES.annee
                                                                    "
                                                                >
                                                                    <option
                                                                        v-for="i in anneesList"
                                                                        :key="i"
                                                                        :value="
                                                                            i
                                                                        "
                                                                    >
                                                                        {{ i }}
                                                                    </option>
                                                                </select>

                                                                <!-- Toggle switch -->
                                                                <div
                                                                    class="flex flex-col items-center ml-2"
                                                                >
                                                                    <label
                                                                        class="relative inline-flex items-center cursor-pointer"
                                                                    >
                                                                        <input
                                                                            type="checkbox"
                                                                            class="sr-only peer"
                                                                            v-model="
                                                                                isToggledES
                                                                            "
                                                                        />
                                                                        <div
                                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600"
                                                                        ></div>
                                                                        <span
                                                                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"
                                                                        ></span>
                                                                    </label>
                                                                    <p
                                                                        class="text-sm text-gray-600"
                                                                    >
                                                                        {{
                                                                            isToggledES
                                                                                ? "Entre deux"
                                                                                : "Une seule"
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <!-- Date fin juste à droite du toggle -->
                                                                <template
                                                                    v-if="
                                                                        isToggledES
                                                                    "
                                                                >
                                                                    <input
                                                                        v-if="
                                                                            rechercheES.type ===
                                                                            '1'
                                                                        "
                                                                        type="date"
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheES.dateFin
                                                                        "
                                                                    />
                                                                    <div
                                                                        v-else-if="
                                                                            rechercheES.type ===
                                                                            '2'
                                                                        "
                                                                        class="flex gap-2 ml-2"
                                                                    >
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheES.moisFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in 12"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                        .toString()
                                                                                        .padStart(
                                                                                            2,
                                                                                            "0"
                                                                                        )
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                        <select
                                                                            class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500"
                                                                            v-model="
                                                                                rechercheES.anneeFin
                                                                            "
                                                                        >
                                                                            <option
                                                                                v-for="i in anneesList"
                                                                                :key="
                                                                                    i
                                                                                "
                                                                                :value="
                                                                                    i
                                                                                "
                                                                            >
                                                                                {{
                                                                                    i
                                                                                }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <select
                                                                        v-else-if="
                                                                            rechercheES.type ===
                                                                            '3'
                                                                        "
                                                                        class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 ml-2 w-[140px]"
                                                                        v-model="
                                                                            rechercheES.anneeFin
                                                                        "
                                                                    >
                                                                        <option
                                                                            v-for="i in anneesList"
                                                                            :key="
                                                                                i
                                                                            "
                                                                            :value="
                                                                                i
                                                                            "
                                                                        >
                                                                            {{
                                                                                i
                                                                            }}
                                                                        </option>
                                                                    </select>
                                                                </template>
                                                            </div>

                                                            <!-- Extrême droite : select users -->
                                                            <div
                                                                class="flex items-center flex-none justify-end min-w-[220px]"
                                                            >
                                                                <select
                                                                    class="rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-500 w-full max-w-xs"
                                                                    v-model="
                                                                        rechercheES.userId
                                                                    "
                                                                >
                                                                    <option
                                                                        value=""
                                                                    >
                                                                        Tous les
                                                                        utilisateurs
                                                                    </option>
                                                                    <option
                                                                        v-for="user in users"
                                                                        :key="
                                                                            user.id
                                                                        "
                                                                        :value="
                                                                            user.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            user.nom_utilisateur
                                                                        }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex flex-col sm:flex-row justify-center gap-4"
                                                    >
                                                        <button
                                                            @click="
                                                                rechercherES
                                                            "
                                                            :disabled="
                                                                isLoadingES
                                                            "
                                                            class="bg-blue-600 text-white rounded-lg px-6 py-2 hover:bg-blue-700 transition disabled:bg-blue-300"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isLoadingES
                                                                "
                                                                >Chargement...</span
                                                            >
                                                            <span v-else
                                                                >Rechercher</span
                                                            >
                                                        </button>

                                                        <ExportPDF
                                                            type="ES"
                                                            container-selector="#statistiques-es-container"
                                                        />
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="errorMessageES"
                                                    class="text-center text-red-500 mb-4"
                                                >
                                                    {{ errorMessageES }}
                                                </div>

                                                <div
                                                    id="statistiques-es-container"
                                                >
                                                    <div
                                                        v-if="
                                                            resultatsRechercheES.length >
                                                            0
                                                        "
                                                        class="mb-6"
                                                    >
                                                        <BarChart
                                                            :chart-data="
                                                                chartDataES
                                                            "
                                                            chart-title="Statistiques Enquête de Satisfaction"
                                                            class="h-64 lg:h-80"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="
                                                            rechercheES.type &&
                                                            resultatsRechercheES.length ===
                                                                0
                                                        "
                                                        class="text-center text-gray-500 py-8"
                                                    >
                                                        Aucun résultat trouvé
                                                        pour votre recherche.
                                                    </div>

                                                    <div
                                                        v-if="
                                                            resultatsRechercheES.length >
                                                            0
                                                        "
                                                        class="overflow-x-auto"
                                                    >
                                                        <table
                                                            class="w-full bg-white border rounded-lg"
                                                        >
                                                            <thead
                                                                class="bg-gray-200"
                                                            >
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left"
                                                                    >
                                                                        Type de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        Nombres
                                                                        de
                                                                        constat
                                                                    </th>
                                                                    <th
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        %
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        resultat,
                                                                        index
                                                                    ) in resultatsRechercheES"
                                                                    :key="index"
                                                                    class="border-t"
                                                                >
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        {{
                                                                            resultat.libelle
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.nombre
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            resultat.pourcentage.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot
                                                                class="bg-gray-200 font-bold"
                                                            >
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-3"
                                                                    >
                                                                        Total
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalConstatsES
                                                                        }}
                                                                    </td>
                                                                    <td
                                                                        class="px-4 py-3 text-center"
                                                                    >
                                                                        {{
                                                                            totalPourcentageES.toFixed(
                                                                                1
                                                                            )
                                                                        }}%
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Titre actions -->
                    <div
                        class="w-full mt-10 text-center underline underline-offset-4"
                    >
                        <h2 class="text-4xl font-medium text-gray-800">
                            Actions
                        </h2>
                    </div>

                    <!-- Audit Interne | PTA | Audit Externe | CAC | SWOT | ES -->
                    <div class="max-w-full mx-auto mt-4">
                        <!-- Carousel Container -->
                        <div class="relative bg-white rounded-lg shadow-lg p-6">
                            <!-- Carousel Header -->
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">
                                    Statut des Actions
                                </h2>

                                <!-- Navigation Indicators -->
                                <div class="flex space-x-2">
                                    <button
                                        v-for="(
                                            slide, index
                                        ) in totalSlidesCard"
                                        :key="index"
                                        @click="goToSlideCard(index)"
                                        :class="[
                                            'w-3 h-3 rounded-full transition-all',
                                            currentSlideCard === index
                                                ? 'bg-blue-600'
                                                : 'bg-gray-300',
                                        ]"
                                    ></button>
                                </div>
                            </div>

                            <!-- Carousel Content -->
                            <div class="relative overflow-hidden">
                                <div
                                    :style="{
                                        transform: `translateX(-${
                                            currentSlideCard * 100
                                        }%)`,
                                    }"
                                    class="flex transition-transform duration-300 ease-in-out"
                                >
                                    <!-- Slide 1: AI + PTA -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                        >
                                            <!-- Audit Interne (AI) -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    Audit Interne
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalAI(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAI.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAI(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAI.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAI('Réglé')
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAI.regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAI(
                                                                'Non Réglé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAI.non_regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PTA -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    PTA
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'Clôturé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Clôturé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.cloture ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'Abandonné'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Abandonné
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.abandonne ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'Non Réalisé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-white text-black p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réalisé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.non_realise ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalPTA(
                                                                'A Reporter'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-purple-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            A Reporter
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsPTA.a_reporter ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Slide 2: AE + CAC -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                        >
                                            <!-- Audit Externe (AE) -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    Audit Externe
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalAE(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAE.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAE(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAE.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAE('Réglé')
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAE.regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalAE(
                                                                'Non Réglé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsAE.non_regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CAC -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    CAC
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalCAC(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsCAC.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalCAC(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsCAC.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalCAC(
                                                                'Réglé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsCAC.regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalCAC(
                                                                'Non Réglé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsCAC.non_regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Slide 3: SWOT + ES -->
                                    <div class="w-full flex-shrink-0">
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                        >
                                            <!-- SWOT -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    SWOT
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'Clôturé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Clôturé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.cloture ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'Abandonné'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Abandonné
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.abandonne ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'Non Réalisé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-white text-black p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réalisé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.non_realise ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalSWOT(
                                                                'A Reporter'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-purple-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            A Reporter
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsSWOT.a_reporter ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Enquête de Satisfaction (ES) -->
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-6"
                                            >
                                                <h3
                                                    class="text-2xl text-center border-b border-gray-300 pb-3 font-semibold text-gray-800 mb-6"
                                                >
                                                    Enquête de Satisfaction
                                                </h3>
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white"
                                                >
                                                    <div
                                                        @click="
                                                            openModalES(
                                                                'En cours'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-blue-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En cours
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsES.en_cours ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalES(
                                                                'En retard'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-red-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            En retard
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsES.en_retard ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalES('Réglé')
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-green-500 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsES.regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        @click="
                                                            openModalES(
                                                                'Non Réglé'
                                                            )
                                                        "
                                                        class="flex flex-col lg:flex-row lg:items-center bg-gray-600 p-4 lg:p-6 rounded-md shadow-lg cursor-pointer hover:shadow-xl transition-all"
                                                    >
                                                        <p
                                                            class="text-center lg:text-left lg:w-1/2 text-sm lg:text-base"
                                                        >
                                                            Non Réglé
                                                        </p>
                                                        <p
                                                            class="text-center lg:text-right lg:w-1/2 text-lg font-bold mt-2 lg:mt-0"
                                                        >
                                                            {{
                                                                statsES.non_regle ||
                                                                0
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex justify-between items-center mt-6">
                                <button
                                    @click="previousSlideCard"
                                    :disabled="currentSlideCard === 0"
                                    :class="[
                                        'flex items-center px-4 py-2 rounded-lg transition-all',
                                        currentSlideCard === 0
                                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                            : 'bg-blue-600 text-white hover:bg-blue-700',
                                    ]"
                                >
                                    <ChevronLeft class="w-5 h-5 mr-2" />
                                    Précédent
                                </button>

                                <div class="text-sm text-gray-500">
                                    {{ currentSlideCard + 1 }} sur
                                    {{ totalSlidesCard }}
                                </div>

                                <button
                                    @click="nextSlideCard"
                                    :disabled="
                                        currentSlideCard === totalSlidesCard - 1
                                    "
                                    :class="[
                                        'flex items-center px-4 py-2 rounded-lg transition-all',
                                        currentSlideCard === totalSlidesCard - 1
                                            ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                            : 'bg-blue-600 text-white hover:bg-blue-700',
                                    ]"
                                >
                                    Suivant
                                    <ChevronRight class="w-5 h-5 ml-2" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal pour Audit Interne -->
                <AIActionModal
                    v-if="showModalAI"
                    :titre="selectedStatusAI"
                    @close="closeModalAI"
                />

                <!-- Modal pour PTA -->
                <PTAActionModal
                    v-if="showModalPTA"
                    :titre="selectedStatusPTA"
                    @close="closeModalPTA"
                />

                <!-- Modal pour AE -->
                <AEActionModal
                    v-if="showModalAE"
                    :titre="selectedStatusAE"
                    @close="closeModalAE"
                />

                <!-- Modal pour CAC -->
                <CACActionModal
                    v-if="showModalCAC"
                    :titre="selectedStatusCAC"
                    @close="closeModalCAC"
                />

                <!-- Modal pour SWOT -->
                <SWOTActionModal
                    v-if="showModalSWOT"
                    :titre="selectedStatusSWOT"
                    @close="closeModalSWOT"
                />

                <!-- Modal pour ES -->
                <ESActionModal
                    v-if="showModalES"
                    :titre="selectedStatusES"
                    @close="closeModalES"
                />

                <!-- Footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Info, ChevronLeft, ChevronRight } from "lucide-vue-next";
import axios from "axios";
import Sidebar from "../assets/SidebarUser.vue";
import Navbar from "../assets/Navbar.vue";
import Footer from "../assets/Footer.vue";
import BarChart from "../charts/BarChart.vue";
import ExportPDF from "../charts/ExportBarChartPDF.vue";
import AIActionModal from "../assets/AIActionsModalUser.vue";
import PTAActionModal from "../assets/PTAActionsModalUser.vue";
import AEActionModal from "../assets/AEActionsModalUser.vue";
import CACActionModal from "../assets/CACActionsModalUser.vue";
import SWOTActionModal from "../assets/SWOTActionsModalUser.vue";
import ESActionModal from "../assets/ESActionsModalUser.vue";

// Carousel statistique
const currentSlide = ref(0);
const totalSlides = ref(3); // AI+PTA, AE+CAC, SWOT+ES

// Carousel card
const currentSlideCard = ref(0);
const totalSlidesCard = ref(3); // AI+PTA, AE+CAC, SWOT+ES

// Navigation functions
const nextSlide = () => {
    if (currentSlide.value < totalSlides.value - 1) {
        currentSlide.value++;
    }
};

const previousSlide = () => {
    if (currentSlide.value > 0) {
        currentSlide.value--;
    }
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

// Navigation functions Card
const nextSlideCard = () => {
    if (currentSlideCard.value < totalSlidesCard.value - 1) {
        currentSlideCard.value++;
    }
};

const previousSlideCard = () => {
    if (currentSlideCard.value > 0) {
        currentSlideCard.value--;
    }
};

const goToSlideCard = (index) => {
    currentSlideCard.value = index;
};

// État pour suivre si le sidebar est réduit
const isSidebarCollapsed = ref(false);

// Fonction appelée quand le sidebar change d'état
const handleSidebarToggle = (collapsed) => {
    isSidebarCollapsed.value = collapsed;
    localStorage.setItem("sidebar-collapsed", collapsed);
};

// Générer la liste des années (de l'année en cours à 5 ans en arrière)
const anneeActuelle = new Date().getFullYear();
const anneesList = Array.from({ length: 6 }, (_, i) => anneeActuelle - i);

// Utilisateurs pour le filtre
const users = ref([]);

// Options de recherche pour AI
const rechercheAI = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledAI = ref(false);
const resultatsRechercheAI = ref([]);
const isLoadingAI = ref(false);
const errorMessageAI = ref("");

const totalConstatsAI = computed(() => {
    return resultatsRechercheAI.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentageAI = computed(() => {
    return resultatsRechercheAI.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataAI = computed(() => {
    if (resultatsRechercheAI.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats AI",
                    data: [],
                    backgroundColor: [
                        "#0062ff",
                        "#ff6384",
                        "#36a2eb",
                        "#ffcd56",
                        "#4bc0c0",
                        "#9966ff",
                        "#ff9f40",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheAI.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats AI",
                data: resultatsRechercheAI.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#0062ff",
                    "#ff6384",
                    "#36a2eb",
                    "#ffcd56",
                    "#4bc0c0",
                    "#9966ff",
                    "#ff9f40",
                ],
            },
        ],
    };
});

// Options de recherche pour PTA
const recherchePTA = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledPTA = ref(false);
const resultatsRecherchePTA = ref([]);
const isLoadingPTA = ref(false);
const errorMessagePTA = ref("");

const totalConstatsPTA = computed(() => {
    return resultatsRecherchePTA.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentagePTA = computed(() => {
    return resultatsRecherchePTA.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataPTA = computed(() => {
    if (resultatsRecherchePTA.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats PTA",
                    data: [],
                    backgroundColor: [
                        "#34d399",
                        "#a78bfa",
                        "#fbbf24",
                        "#ec4899",
                        "#10b981",
                        "#8b5cf6",
                        "#f59e0b",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRecherchePTA.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats PTA",
                data: resultatsRecherchePTA.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#34d399",
                    "#a78bfa",
                    "#fbbf24",
                    "#ec4899",
                    "#10b981",
                    "#8b5cf6",
                    "#f59e0b",
                ],
            },
        ],
    };
});

// Options de recherche pour AE
const rechercheAE = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledAE = ref(false);
const resultatsRechercheAE = ref([]);
const isLoadingAE = ref(false);
const errorMessageAE = ref("");

const totalConstatsAE = computed(() => {
    return resultatsRechercheAE.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentageAE = computed(() => {
    return resultatsRechercheAE.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataAE = computed(() => {
    if (resultatsRechercheAE.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats Audit Externe",
                    data: [],
                    backgroundColor: [
                        "#ef4444",
                        "#f97316",
                        "#eab308",
                        "#22c55e",
                        "#06b6d4",
                        "#3b82f6",
                        "#8b5cf6",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheAE.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats Audit Externe",
                data: resultatsRechercheAE.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#ef4444",
                    "#f97316",
                    "#eab308",
                    "#22c55e",
                    "#06b6d4",
                    "#3b82f6",
                    "#8b5cf6",
                ],
            },
        ],
    };
});

// Options de recherche pour CAC
const rechercheCAC = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledCAC = ref(false);
const resultatsRechercheCAC = ref([]);
const isLoadingCAC = ref(false);
const errorMessageCAC = ref("");

const totalConstatsCAC = computed(() => {
    return resultatsRechercheCAC.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentageCAC = computed(() => {
    return resultatsRechercheCAC.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataCAC = computed(() => {
    if (resultatsRechercheCAC.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats CAC",
                    data: [],
                    backgroundColor: [
                        "#ef4444",
                        "#f97316",
                        "#eab308",
                        "#22c55e",
                        "#06b6d4",
                        "#3b82f6",
                        "#8b5cf6",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheCAC.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats CAC",
                data: resultatsRechercheCAC.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#ef4444",
                    "#f97316",
                    "#eab308",
                    "#22c55e",
                    "#06b6d4",
                    "#3b82f6",
                    "#8b5cf6",
                ],
            },
        ],
    };
});

// Options de recherche pour SWOT
const rechercheSWOT = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledSWOT = ref(false);
const resultatsRechercheSWOT = ref([]);
const isLoadingSWOT = ref(false);
const errorMessageSWOT = ref("");

const totalConstatsSWOT = computed(() => {
    return resultatsRechercheSWOT.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentageSWOT = computed(() => {
    return resultatsRechercheSWOT.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataSWOT = computed(() => {
    if (resultatsRechercheSWOT.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats SWOT",
                    data: [],
                    backgroundColor: [
                        "#ef4444",
                        "#f97316",
                        "#eab308",
                        "#22c55e",
                        "#06b6d4",
                        "#3b82f6",
                        "#8b5cf6",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheSWOT.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats SWOT",
                data: resultatsRechercheSWOT.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#ef4444",
                    "#f97316",
                    "#eab308",
                    "#22c55e",
                    "#06b6d4",
                    "#3b82f6",
                    "#8b5cf6",
                ],
            },
        ],
    };
});

// Options de recherche pour ES
const rechercheES = ref({
    type: "1",
    dateDebut: new Date().toISOString().split("T")[0],
    dateFin: new Date().toISOString().split("T")[0],
    mois: new Date().getMonth() + 1,
    moisFin: new Date().getMonth() + 1,
    annee: new Date().getFullYear(),
    anneeFin: new Date().getFullYear(),
    userId: "",
});

const isToggledES = ref(false);
const resultatsRechercheES = ref([]);
const isLoadingES = ref(false);
const errorMessageES = ref("");

const totalConstatsES = computed(() => {
    return resultatsRechercheES.value.reduce(
        (total, resultat) => total + resultat.nombre,
        0
    );
});

const totalPourcentageES = computed(() => {
    return resultatsRechercheES.value.reduce(
        (total, resultat) => total + resultat.pourcentage,
        0
    );
});

const chartDataES = computed(() => {
    if (resultatsRechercheES.value.length === 0) {
        return {
            labels: [],
            datasets: [
                {
                    label: "Constats Enquête de Satisfaction",
                    data: [],
                    backgroundColor: [
                        "#ef4444",
                        "#f97316",
                        "#eab308",
                        "#22c55e",
                        "#06b6d4",
                        "#3b82f6",
                        "#8b5cf6",
                    ],
                },
            ],
        };
    }

    return {
        labels: resultatsRechercheES.value.map((r) => r.libelle),
        datasets: [
            {
                label: "Constats Enquête de Satisfaction",
                data: resultatsRechercheES.value.map((r) => r.pourcentage),
                backgroundColor: [
                    "#ef4444",
                    "#f97316",
                    "#eab308",
                    "#22c55e",
                    "#06b6d4",
                    "#3b82f6",
                    "#8b5cf6",
                ],
            },
        ],
    };
});

// Modal states
const showModalAI = ref(false);
const selectedStatusAI = ref("");
const statsAI = ref({
    en_cours: 0,
    en_retard: 0,
    regle: 0,
    non_regle: 0,
});

const showModalPTA = ref(false);
const selectedStatusPTA = ref("");
const statsPTA = ref({
    en_cours: 0,
    en_retard: 0,
    cloture: 0,
    abandonne: 0,
    non_realise: 0,
    a_reporter: 0,
});

const showModalAE = ref(false);
const selectedStatusAE = ref("");
const statsAE = ref({
    en_cours: 0,
    en_retard: 0,
    regle: 0,
    non_regle: 0,
});

const showModalCAC = ref(false);
const selectedStatusCAC = ref("");
const statsCAC = ref({
    en_cours: 0,
    en_retard: 0,
    regle: 0,
    non_regle: 0,
});

const showModalSWOT = ref(false);
const selectedStatusSWOT = ref("");
const statsSWOT = ref({
    en_cours: 0,
    en_retard: 0,
    cloture: 0,
    abandonne: 0,
    non_realise: 0,
    a_reporter: 0,
});

const showModalES = ref(false);
const selectedStatusES = ref("");
const statsES = ref({
    en_cours: 0,
    en_retard: 0,
    regle: 0,
    non_regle: 0,
});

// Modal functions
const openModalAI = (status) => {
    selectedStatusAI.value = status;
    showModalAI.value = true;
};

const closeModalAI = () => {
    showModalAI.value = false;
};

const openModalPTA = (status) => {
    selectedStatusPTA.value = status;
    showModalPTA.value = true;
};

const closeModalPTA = () => {
    showModalPTA.value = false;
};

const openModalAE = (status) => {
    selectedStatusAE.value = status;
    showModalAE.value = true;
};

const closeModalAE = () => {
    showModalAE.value = false;
};

const openModalCAC = (status) => {
    selectedStatusCAC.value = status;
    showModalCAC.value = true;
};

const closeModalCAC = () => {
    showModalCAC.value = false;
};

const openModalSWOT = (status) => {
    selectedStatusSWOT.value = status;
    showModalSWOT.value = true;
};

const closeModalSWOT = () => {
    showModalSWOT.value = false;
};

const openModalES = (status) => {
    selectedStatusES.value = status;
    showModalES.value = true;
};

const closeModalES = () => {
    showModalES.value = false;
};

// Load data on component mount
onMounted(async () => {
    try {
        const [
            usersResponse,
            statsAIResponse,
            statsPTAResponse,
            statsAEResponse,
            statsCACResponse,
            statsSWOTResponse,
            statsESResponse,
        ] = await Promise.all([
            axios.get("/api/users"),
            axios.get("/api/api/actions/stats", { params: { prefix: "AI" } }),
            axios.get("/api/api/actions/stats", { params: { prefix: "PTA" } }),
            axios.get("/api/api/actions/stats", { params: { prefix: "AE" } }),
            axios.get("/api/api/actions/stats", { params: { prefix: "CAC" } }),
            axios.get("/api/api/actions/stats", { params: { prefix: "SWOT" } }),
            axios.get("/api/api/actions/stats", { params: { prefix: "ES" } }),
        ]);

        users.value = usersResponse.data;
        statsAI.value = statsAIResponse.data;
        statsPTA.value = statsPTAResponse.data;
        statsAE.value = statsAEResponse.data;
        statsCAC.value = statsCACResponse.data;
        statsSWOT.value = statsSWOTResponse.data;
        statsES.value = statsESResponse.data;
    } catch (error) {
        console.error("Erreur lors du chargement des données:", error);
    }

    const saved = localStorage.getItem("sidebar-collapsed");
    if (saved !== null) {
        isSidebarCollapsed.value = saved === "true";
    }
});

// Reset search functions
const resetSearchAI = () => {
    resultatsRechercheAI.value = [];
    isToggledAI.value = false;
};

const resetSearchPTA = () => {
    resultatsRecherchePTA.value = [];
    isToggledPTA.value = false;
};

const resetSearchAE = () => {
    resultatsRechercheAE.value = [];
    isToggledAE.value = false;
};

const resetSearchCAC = () => {
    resultatsRechercheCAC.value = [];
    isToggledCAC.value = false;
};

const resetSearchSWOT = () => {
    resultatsRechercheSWOT.value = [];
    isToggledSWOT.value = false;
};

const resetSearchES = () => {
    resultatsRechercheES.value = [];
    isToggledES.value = false;
};
// Search parameter functions
const getSearchParamsAI = () => {
    const params = { type: rechercheAI.value.type };
    if (rechercheAI.value.userId) params.user_id = rechercheAI.value.userId;

    if (rechercheAI.value.type === "1") {
        params.date_debut = rechercheAI.value.dateDebut;
        if (isToggledAI.value) params.date_fin = rechercheAI.value.dateFin;
    } else if (rechercheAI.value.type === "2") {
        params.mois = rechercheAI.value.mois;
        params.annee = rechercheAI.value.annee;
        if (isToggledAI.value) {
            params.mois_fin = rechercheAI.value.moisFin;
            params.annee_fin = rechercheAI.value.anneeFin;
        }
    } else if (rechercheAI.value.type === "3") {
        params.annee = rechercheAI.value.annee;
        if (isToggledAI.value) params.annee_fin = rechercheAI.value.anneeFin;
    }
    return params;
};

const getSearchParamsPTA = () => {
    const params = { type: recherchePTA.value.type };
    if (recherchePTA.value.userId) params.user_id = recherchePTA.value.userId;

    if (recherchePTA.value.type === "1") {
        params.date_debut = recherchePTA.value.dateDebut;
        if (isToggledPTA.value) params.date_fin = recherchePTA.value.dateFin;
    } else if (recherchePTA.value.type === "2") {
        params.mois = recherchePTA.value.mois;
        params.annee = recherchePTA.value.annee;
        if (isToggledPTA.value) {
            params.mois_fin = recherchePTA.value.moisFin;
            params.annee_fin = recherchePTA.value.anneeFin;
        }
    } else if (recherchePTA.value.type === "3") {
        params.annee = recherchePTA.value.annee;
        if (isToggledPTA.value) params.annee_fin = recherchePTA.value.anneeFin;
    }
    return params;
};

const getSearchParamsAE = () => {
    const params = { type: rechercheAE.value.type };
    if (rechercheAE.value.userId) params.user_id = rechercheAE.value.userId;

    if (rechercheAE.value.type === "1") {
        params.date_debut = rechercheAE.value.dateDebut;
        if (isToggledAE.value) params.date_fin = rechercheAE.value.dateFin;
    } else if (rechercheAE.value.type === "2") {
        params.mois = rechercheAE.value.mois;
        params.annee = rechercheAE.value.annee;
        if (isToggledAE.value) {
            params.mois_fin = rechercheAE.value.moisFin;
            params.annee_fin = rechercheAE.value.anneeFin;
        }
    } else if (rechercheAE.value.type === "3") {
        params.annee = rechercheAE.value.annee;
        if (isToggledAE.value) params.annee_fin = rechercheAE.value.anneeFin;
    }
    return params;
};

const getSearchParamsCAC = () => {
    const params = { type: rechercheCAC.value.type };
    if (rechercheCAC.value.userId) params.user_id = rechercheCAC.value.userId;

    if (rechercheCAC.value.type === "1") {
        params.date_debut = rechercheCAC.value.dateDebut;
        if (isToggledCAC.value) params.date_fin = rechercheCAC.value.dateFin;
    } else if (rechercheCAC.value.type === "2") {
        params.mois = rechercheCAC.value.mois;
        params.annee = rechercheCAC.value.annee;
        if (isToggledCAC.value) {
            params.mois_fin = rechercheCAC.value.moisFin;
            params.annee_fin = rechercheCAC.value.anneeFin;
        }
    } else if (rechercheCAC.value.type === "3") {
        params.annee = rechercheCAC.value.annee;
        if (isToggledCAC.value) params.annee_fin = rechercheCAC.value.anneeFin;
    }
    return params;
};

const getSearchParamsSWOT = () => {
    const params = { type: rechercheSWOT.value.type };
    if (rechercheSWOT.value.userId) params.user_id = rechercheSWOT.value.userId;

    if (rechercheSWOT.value.type === "1") {
        params.date_debut = rechercheSWOT.value.dateDebut;
        if (isToggledSWOT.value) params.date_fin = rechercheSWOT.value.dateFin;
    } else if (rechercheSWOT.value.type === "2") {
        params.mois = rechercheSWOT.value.mois;
        params.annee = rechercheSWOT.value.annee;
        if (isToggledSWOT.value) {
            params.mois_fin = rechercheSWOT.value.moisFin;
            params.annee_fin = rechercheSWOT.value.anneeFin;
        }
    } else if (rechercheSWOT.value.type === "3") {
        params.annee = rechercheSWOT.value.annee;
        if (isToggledSWOT.value)
            params.annee_fin = rechercheSWOT.value.anneeFin;
    }
    return params;
};

const getSearchParamsES = () => {
    const params = { type: rechercheES.value.type };
    if (rechercheES.value.userId) params.user_id = rechercheES.value.userId;

    if (rechercheES.value.type === "1") {
        params.date_debut = rechercheES.value.dateDebut;
        if (isToggledES.value) params.date_fin = rechercheES.value.dateFin;
    } else if (rechercheES.value.type === "2") {
        params.mois = rechercheES.value.mois;
        params.annee = rechercheES.value.annee;
        if (isToggledES.value) {
            params.mois_fin = rechercheES.value.moisFin;
            params.annee_fin = rechercheES.value.anneeFin;
        }
    } else if (rechercheES.value.type === "3") {
        params.annee = rechercheES.value.annee;
        if (isToggledES.value) params.annee_fin = rechercheES.value.anneeFin;
    }
    return params;
};

// Search functions
const rechercherAI = async () => {
    try {
        isLoadingAI.value = true;
        errorMessageAI.value = "";
        const params = getSearchParamsAI();
        const response = await axios.get("/api/constats/statistiques/AI", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRechercheAI.value = response.data;
            if (response.data.length === 0) {
                errorMessageAI.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheAI.value = [];
            errorMessageAI.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche AI:", error);
        resultatsRechercheAI.value = [];
        errorMessageAI.value = "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingAI.value = false;
    }
};

const rechercherPTA = async () => {
    try {
        isLoadingPTA.value = true;
        errorMessagePTA.value = "";
        const params = getSearchParamsPTA();
        const response = await axios.get("/api/constats/statistiques/PTA", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRecherchePTA.value = response.data;
            if (response.data.length === 0) {
                errorMessagePTA.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRecherchePTA.value = [];
            errorMessagePTA.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche PTA:", error);
        resultatsRecherchePTA.value = [];
        errorMessagePTA.value = "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingPTA.value = false;
    }
};

const rechercherAE = async () => {
    try {
        isLoadingAE.value = true;
        errorMessageAE.value = "";
        const params = getSearchParamsAE();
        const response = await axios.get("/api/constats/statistiques/AE", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRechercheAE.value = response.data;
            if (response.data.length === 0) {
                errorMessageAE.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheAE.value = [];
            errorMessageAE.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche Audit Externe:", error);
        resultatsRechercheAE.value = [];
        errorMessageAE.value = "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingAE.value = false;
    }
};

const rechercherCAC = async () => {
    try {
        isLoadingCAC.value = true;
        errorMessageCAC.value = "";
        const params = getSearchParamsCAC();
        const response = await axios.get("/api/constats/statistiques/CAC", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRechercheCAC.value = response.data;
            if (response.data.length === 0) {
                errorMessageCAC.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheCAC.value = [];
            errorMessageCAC.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche CAC:", error);
        resultatsRechercheCAC.value = [];
        errorMessageCAC.value = "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingCAC.value = false;
    }
};

const rechercherSWOT = async () => {
    try {
        isLoadingSWOT.value = true;
        errorMessageSWOT.value = "";
        const params = getSearchParamsSWOT();
        const response = await axios.get("/api/constats/statistiques/SWOT", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRechercheSWOT.value = response.data;
            if (response.data.length === 0) {
                errorMessageSWOT.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheSWOT.value = [];
            errorMessageSWOT.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error("Erreur lors de la recherche SWOT:", error);
        resultatsRechercheSWOT.value = [];
        errorMessageSWOT.value =
            "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingSWOT.value = false;
    }
};

const rechercherES = async () => {
    try {
        isLoadingES.value = true;
        errorMessageES.value = "";
        const params = getSearchParamsES();
        const response = await axios.get("/api/constats/statistiques/ES", {
            params,
        });

        if (Array.isArray(response.data)) {
            resultatsRechercheES.value = response.data;
            if (response.data.length === 0) {
                errorMessageES.value =
                    "Aucun résultat trouvé pour ces recherches.";
            }
        } else {
            resultatsRechercheES.value = [];
            errorMessageES.value = "Format de réponse inattendu du serveur.";
        }
    } catch (error) {
        console.error(
            "Erreur lors de la recherche Enquête de Satisfaction:",
            error
        );
        resultatsRechercheES.value = [];
        errorMessageES.value = "Une erreur est survenue lors de la recherche.";
    } finally {
        isLoadingES.value = false;
    }
};
</script>
