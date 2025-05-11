import axios from "../utils/axiosSetup";

const emailService = {
    /**
     * Récupère la configuration email pour un utilisateur
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Configuration email
     */
    async getConfig(userId) {
        try {
            const response = await axios.get(`/api/email-config/${userId}`);
            return response.data;
        } catch (error) {
            console.error("Erreur getConfig:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Sauvegarde la configuration email pour un utilisateur
     * @param {Object} config - Configuration (host, port, username, password)
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Réponse de l'API
     */
    async saveConfig(config, userId) {
        try {
            const response = await axios.post(
                `/api/email-config/${userId}`,
                config
            );
            return response.data;
        } catch (error) {
            console.error("Erreur saveConfig:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Active ou désactive les notifications email pour un utilisateur
     * @param {boolean} active - État d'activation des notifications
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Réponse de l'API
     */
    async toggleNotifications(active, userId) {
        try {
            const response = await axios.post(
                `/api/email-notifications/${userId}/toggle`,
                { active }
            );
            return response.data;
        } catch (error) {
            console.error("Erreur toggleNotifications:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Récupère la liste des membres email pour un utilisateur
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Array>} - Liste des emails
     */
    async getMembers(userId) {
        try {
            const response = await axios.get(`/api/email-members/${userId}`);
            return response.data;
        } catch (error) {
            console.error("Erreur getMembers:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Ajoute un membre à la liste des destinataires d'email
     * @param {string} email - Email à ajouter
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Réponse de l'API
     */
    async addMember(email, userId) {
        try {
            const response = await axios.post(
                `/api/email-members/${userId}/add`,
                { email }
            );
            return response.data;
        } catch (error) {
            console.error("Erreur addMember:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Supprime un membre de la liste des destinataires d'email
     * @param {string} email - Email à supprimer
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Réponse de l'API
     */
    async removeMember(email, userId) {
        try {
            const response = await axios.post(
                `/api/email-members/${userId}/remove`,
                { email }
            );
            return response.data;
        } catch (error) {
            console.error("Erreur removeMember:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Envoie une alerte par email
     * @param {Object} alertData - Données de l'alerte
     * @param {number|string} userId - ID de l'utilisateur
     * @returns {Promise<Object>} - Réponse de l'API
     */
    async sendAlert(alertData, userId) {
        try {
            const response = await axios.post(
                `/api/email-alert/${userId}`,
                alertData
            );
            return response.data;
        } catch (error) {
            console.error("Erreur sendAlert:", error);
            throw error.response?.data || error;
        }
    },

    /**
     * Teste la configuration email
     * @param {Object} config - Configuration email à tester
     * @returns {Promise<Object>} - Résultat du test
     */
    async testConfig(config) {
        try {
            const response = await axios.post("/api/email-config/test", config);
            return response.data;
        } catch (error) {
            console.error("Erreur testConfig:", error);
            throw error.response?.data || error;
        }
    },
};

export default emailService;
