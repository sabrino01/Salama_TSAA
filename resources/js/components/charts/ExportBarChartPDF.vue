<!-- Composant d'export PDF -->
<template>
    <button
        @click="exportToPDF"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition flex items-center"
        :class="buttonClass"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
            />
        </svg>
        Exporter
    </button>
</template>

<script setup>
import { ref, onMounted } from "vue";
import html2canvas from "html2canvas";
import jsPDF from "jspdf";

// Props pour personnaliser l'export
const props = defineProps({
    // Type de statistiques (AI ou PTA)
    type: {
        type: String,
        validator: (value) => ["AI", "PTA"].includes(value),
        required: true,
    },
    // Sélecteur du conteneur à capturer
    containerSelector: {
        type: String,
        required: true,
    },
});

// Classe du bouton basée sur le type
const buttonClass = ref("");
onMounted(() => {
    buttonClass.value =
        props.type === "AI"
            ? "bg-blue-500 hover:bg-blue-600"
            : "bg-blue-500 hover:bg-blue-600";
});

// Fonction pour exporter en PDF
const exportToPDF = async () => {
    try {
        // Sélectionner le conteneur à capturer
        const container = document.querySelector(props.containerSelector);

        if (!container) {
            console.error("Conteneur non trouvé");
            return;
        }

        // Désactiver certains éléments avant la capture
        const buttons = container.querySelectorAll("button");
        buttons.forEach((button) => (button.style.display = "none"));

        // Capture du contenu avec html2canvas
        const canvas = await html2canvas(container, {
            scale: 2, // Améliorer la qualité
            useCORS: true, // Gérer les ressources cross-origin
            logging: false, // Désactiver les logs
        });

        // Restaurer l'affichage des boutons
        buttons.forEach((button) => (button.style.display = ""));

        // Créer un PDF
        const pdf = new jsPDF("p", "mm", "a4");

        // Dimensions de la page A4
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        // Dimensions de l'image
        const imageWidth = canvas.width;
        const imageHeight = canvas.height;

        // Calculer le ratio pour ajuster l'image
        const ratio = imageWidth / imageHeight;
        let width = pdfWidth;
        let height = width / ratio;

        // Ajuster si l'image est trop haute
        if (height > pdfHeight) {
            height = pdfHeight;
            width = height * ratio;
        }

        // Ajouter l'image au PDF
        const imageData = canvas.toDataURL("image/jpeg", 1.0);
        pdf.addImage(
            imageData,
            "JPEG",
            (pdfWidth - width) / 2, // Centrer horizontalement
            10, // Marge supérieure
            width,
            height
        );

        // Ajouter un en-tête avec la date
        const currentDate = new Date().toLocaleDateString("fr-FR");
        pdf.setFontSize(10);
        pdf.text(
            `Rapport ${props.type} généré le ${currentDate}`,
            10,
            pdf.internal.pageSize.height - 10
        );

        // Enregistrer le PDF
        pdf.save(`Statistiques_${props.type}.pdf`);
    } catch (error) {
        console.error("Erreur lors de l'export PDF:", error);
        alert("Une erreur est survenue lors de l'export PDF.");
    }
};
</script>
