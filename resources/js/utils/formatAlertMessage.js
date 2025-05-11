// Fonction pour formater les messages d'alerte pour l'email
const formatAlertMessage = (alert) => {
    const itemName = alert.item?.nom_utilisateur || "Non spécifié";
    // const itemId = alert.item?.id || "";
    const dateFormatee = new Date().toLocaleString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });

    let typeAlerte = "Information";
    switch (alert.type) {
        case "debut":
            typeAlerte = "début d'action";
            break;
        case "suivi":
            typeAlerte = "suivi d'action";
            break;
        case "fin":
            typeAlerte = "fin d'action";
            break;
    }

    return `
    Une nouvelle alerte pour le "${typeAlerte}" a été déclenchée le ${dateFormatee}.

    Élément concerné: ${itemName}

    ${alert.message || ""}

    Cette alerte a été générée automatiquement par l'application Salama_TSAA.
  `;
};

export default formatAlertMessage;
