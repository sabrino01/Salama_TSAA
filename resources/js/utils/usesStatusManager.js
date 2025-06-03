import { ref } from "vue";
import axios from "axios";

export function useStatusManager() {
    const isCheckingStatus = ref(false);
    const statusMessage = ref("");

    // Vérifier le statut d'une action spécifique
    const checkSingleActionStatus = async (actionId) => {
        isCheckingStatus.value = true;
        statusMessage.value = "";

        try {
            const response = await axios.get(
                `/api/actions/${actionId}/check-status`
            );
            const result = response.data;

            if (result.success) {
                statusMessage.value = result.message;
                return {
                    success: true,
                    updated: result.updated,
                    newStatus: result.status,
                    message: result.message,
                };
            } else {
                statusMessage.value = result.message;
                return { success: false, message: result.message };
            }
        } catch (error) {
            const errorMsg = "Erreur lors de la vérification du statut";
            statusMessage.value = errorMsg;
            console.error("Erreur API:", error);
            return { success: false, message: errorMsg };
        } finally {
            isCheckingStatus.value = false;
        }
    };

    // Vérifier toutes les actions
    const checkAllActionsStatus = async () => {
        isCheckingStatus.value = true;
        statusMessage.value = "";

        try {
            const response = await axios.post(
                "/api/actions/check-all-statuses"
            );
            const result = response.data;

            if (result.success) {
                statusMessage.value = `${result.updated_count} actions mises à jour sur ${result.total_checked} vérifiées`;
                return result;
            } else {
                statusMessage.value = result.message;
                return { success: false, message: result.message };
            }
        } catch (error) {
            const errorMsg = "Erreur lors de la vérification globale";
            statusMessage.value = errorMsg;
            console.error("Erreur API:", error);
            return { success: false, message: errorMsg };
        } finally {
            isCheckingStatus.value = false;
        }
    };

    // Obtenir les actions actives
    const getActiveActions = async () => {
        try {
            const response = await axios.get("/api/actions/active");
            return response.data;
        } catch (error) {
            console.error(
                "Erreur lors de la récupération des actions actives:",
                error
            );
            return [];
        }
    };

    return {
        isCheckingStatus,
        statusMessage,
        checkSingleActionStatus,
        checkAllActionsStatus,
        getActiveActions,
    };
}
