// emailService.js
import axios from "axios";

const API_BASE_URL = "/api/email"; // Ajustez selon votre configuration

const emailService = {
    async toggleNotifications(active, userId) {
        try {
            const response = await axios.post(
                `${API_BASE_URL}/toggle-notifications`,
                {
                    active,
                    user_id: userId,
                }
            );
            return response.data;
        } catch (error) {
            console.error("Erreur toggle notifications:", error);
            throw error.response?.data || error;
        }
    },

    async saveConfig(config, userId) {
        try {
            const response = await axios.post(`${API_BASE_URL}/save-config`, {
                ...config,
                user_id: userId,
            });
            return response.data;
        } catch (error) {
            console.error("Erreur sauvegarde config:", error);
            throw error.response?.data || error;
        }
    },

    async getConfig(userId) {
        try {
            const response = await axios.get(`${API_BASE_URL}/get-config`, {
                params: { user_id: userId },
            });
            return response.data.config;
        } catch (error) {
            console.error("Erreur récupération config:", error);
            throw error.response?.data || error;
        }
    },

    async sendAlert(alertData, userId) {
        try {
            const response = await axios.post(`${API_BASE_URL}/send-alert`, {
                ...alertData,
                user_id: userId,
            });
            return response.data;
        } catch (error) {
            console.error("Erreur envoi alerte:", error);
            throw error.response?.data || error;
        }
    },
};

export default emailService;
