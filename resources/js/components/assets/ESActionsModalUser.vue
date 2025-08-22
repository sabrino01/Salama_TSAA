<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] flex flex-col"
        >
            <!-- Header du modal -->
            <div
                class="bg-green-100 p-4 rounded-t-lg flex justify-between items-center border-b"
            >
                <h2 class="text-xl font-bold">
                    Enquête de Satisfaction - {{ titre }}
                </h2>
            </div>

            <!-- Body du modal avec scrolling -->
            <div class="p-4 overflow-y-auto flex-grow">
                <div
                    v-if="loading"
                    class="flex justify-center items-center h-32"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"
                    ></div>
                </div>

                <div
                    v-else-if="actions.length === 0"
                    class="text-center py-8 text-gray-500"
                >
                    Aucune action trouvée avec le statut "{{ titre }}" pour
                    Enquête de Satisfaction
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="action in actions"
                        :key="action.id"
                        class="p-3 border rounded-md flex justify-between items-start hover:bg-gray-50 space-x-4"
                    >
                        <div>
                            <p class="font-semibold text-gray-700">
                                {{ action.num_actions }}
                            </p>
                            <p class="text-gray-600 break-words">
                                {{ action.description }}
                            </p>
                        </div>
                        <button
                            @click="goToAction(action.id)"
                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition-colors shrink-0"
                        >
                            Voir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer du modal -->
            <div
                class="bg-green-100 p-4 rounded-b-lg border-t flex justify-between items-center"
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

// Router
const router = useRouter();

// État
const actions = ref([]);
const total = ref(0);
const loading = ref(true);

// 🔐 Récupération utilisateur depuis le localStorage
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

async function loadActions() {
    loading.value = true;
    try {
        const response = await axios.get("/api/api/actions/enquete", {
            params: {
                statut: props.titre,
            },
        });
        actions.value = response.data.actions;
        total.value = response.data.total;
    } catch (error) {
        console.error("Erreur lors du chargement des actions ES:", error);
    } finally {
        loading.value = false;
    }
}

// 🔁 Navigation conditionnelle
function goToAction(id) {
    const action = actions.value.find((a) => a.id === id);
    if (!action) {
        console.warn("Action non trouvée pour l'id:", id);
        return;
    }
    if (action.users_id == userId.value) {
        router.push(`/user/actions/es/voir/${id}`);
    } else {
        router.push(`/user/actions/es/voir/other/${id}`);
    }

    emit("close");
}

// Chargement des actions
onMounted(() => {
    loadActions();
});
</script>
