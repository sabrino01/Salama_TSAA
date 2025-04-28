<!-- FrequenceToutAnnee.vue -->
<template>
    <div class="relative full">
        <!-- Résumé de la fréquence -->
        <div class="ml-4 text-sm text-gray-700">
            <span class="font-medium">Type:</span> Tout l'année
        </div>
    </div>
</template>

<script>
export default {
    name: "FrequenceToutAnnee",
    emits: ["update:modelValue"],
    mounted() {
        // Émettre directement les données JSON pour "Tout l'année"
        const data = {
            type: "Tout l'année",
        };
        this.$emit("update:modelValue", JSON.stringify(data));
    },
};
</script>
