<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] flex flex-col"
        >
            <!-- Header du modal -->
            <div
                class="bg-blue-100 p-4 rounded-t-lg flex justify-between items-center border-b"
            >
                <h2 class="text-xl font-bold">
                    Audit Interne (AI) - {{ titre }}
                </h2>
            </div>

            <!-- Body du modal avec scrolling -->
            <div class="p-4 overflow-y-auto flex-grow">
                <div
                    v-if="loading"
                    class="flex justify-center items-center h-32"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"
                    ></div>
                </div>

                <div
                    v-else-if="actions.length === 0"
                    class="text-center py-8 text-gray-500"
                >
                    Aucune action trouvée avec le statut "{{ titre }}" pour
                    Audit Interne
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="action in actions"
                        :key="action.id"
                        class="p-3 border rounded-md flex justify-between items-start hover:bg-gray-50 space-x-4"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-700">
                                {{ action.num_actions }}
                            </p>
                            <p class="text-gray-600 break-words">
                                {{ action.description }}
                            </p>
                        </div>
                        <button
                            @click="goToAction(action.id)"
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors shrink-0"
                        >
                            Voir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer du modal -->
            <div
                class="bg-blue-100 p-4 rounded-b-lg border-t flex justify-between items-center"
            >
                <div>
                    <span class="text-sm text-gray-600"
                        >Total: {{ total }} action(s)</span
                    >
                </div>
                <button
                    @click="$emit('close')"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                >
                    Fermer
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

// Props
const props = defineProps({
    titre: {
        type: String,
        required: true,
    },
});

// Emits
const emit = defineEmits(["close"]);

// État du composant
const actions = ref([]);
const total = ref(0);
const loading = ref(true);
const router = useRouter();

// Récupération de l'utilisateur depuis localStorage
const user = computed(() => {
    try {
        return JSON.parse(localStorage.getItem("user")) || {};
    } catch (e) {
        console.error(
            "Erreur lors de la récupération des données utilisateur:",
            e
        );
        return {};
    }
});
const userId = computed(() => user.value?.id);

// Fonction pour charger les actions
async function loadActions() {
    loading.value = true;
    try {
        const response = await axios.get("/api/api/actions/ai", {
            params: {
                statut: props.titre,
            },
        });
        actions.value = response.data.actions;
        total.value = response.data.total;
    } catch (error) {
        console.error("Erreur lors du chargement des actions AI:", error);
    } finally {
        loading.value = false;
    }
}

// Fonction pour naviguer vers la page de détail d'une action
function goToAction(id) {
    const action = actions.value.find((a) => a.id === id);
    if (!action) {
        console.warn("Action non trouvée pour l'ID:", id);
        return;
    }
    if (userId.value == action.users_id) {
        router.push(`/user/actions/auditinterne/voir/${id}`);
    } else {
        router.push(`/user/actions/auditinterne/voir/other/${id}`);
    }

    emit("close");
}

// Chargement des données initiales
onMounted(() => {
    loadActions();
});
</script>
