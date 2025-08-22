import { createRouter, createWebHistory } from "vue-router";
import auth from "../middleware/auth";

import Login from "../components/Login.vue";
import Dashboard from "../components/admin/Dashboard.vue";
import NotFound from "../notFound.vue";
import Membre from "../components/admin/utilisateurs/Membre.vue";
import AjoutMembre from "../components/admin/utilisateurs/AjoutMembre.vue";
import Profile from "../components/admin/utilisateurs/Profile.vue";
import Sources from "../components/admin/informations/sources/Sources.vue";
import AjoutSources from "../components/admin/informations/sources/AjoutSources.vue";
import VoirSources from "../components/admin/informations/sources/VoirSources.vue";
import EditSources from "../components/admin/informations/sources/EditSources.vue";
import TypeActions from "../components/admin/informations/typeactions/TypeActions.vue";
import AjoutTypeActions from "../components/admin/informations/typeactions/AjoutTypeActions.vue";
import VoirTypeActions from "../components/admin/informations/typeactions/VoirTypeActions.vue";
import EditTypeActions from "../components/admin/informations/typeactions/EditTypeActions.vue";
import Responsable from "../components/admin/informations/responsable/Responsable.vue";
import AjoutResponsable from "../components/admin/informations/responsable/AjoutResponsable.vue";
import VoirResponsable from "../components/admin/informations/responsable/VoirResponsable.vue";
import EditResponsable from "../components/admin/informations/responsable/EditResponsable.vue";
import Suivi from "../components/admin/informations/suivi/Suivi.vue";
import AjoutSuivi from "../components/admin/informations/suivi/AjoutSuivi.vue";
import VoirSuivi from "../components/admin/informations/suivi/VoirSuivi.vue";
import EditSuivi from "../components/admin/informations/suivi/EditSuivi.vue";
import Constat from "../components/admin/informations/constat/Constat.vue";
import AjoutConstat from "../components/admin/informations/constat/AjoutConstat.vue";
import VoirConstat from "../components/admin/informations/constat/VoirConstat.vue";
import EditConstat from "../components/admin/informations/constat/EditConstat.vue";
import AuditInterne from "../components/admin/actions/auditinterne/AuditInterne.vue";
import AjoutAuditInterne from "../components/admin/actions/auditinterne/AjoutAuditInterne.vue";
import VoirAuditInterne from "../components/admin/actions/auditinterne/VoirAuditInterne.vue";
import EditAuditInterne from "../components/admin/actions/auditinterne/EditAuditInterne.vue";
import Pta from "../components/admin/actions/pta/Pta.vue";
import AjoutPta from "../components/admin/actions/pta/AjoutPta.vue";
import VoirPta from "../components/admin/actions/pta/VoirPta.vue";
import EditPta from "../components/admin/actions/pta/EditPta.vue";
import Notifications from "../components/admin/Notifications.vue";
import DashboardUser from "../components/user/Dashboard.vue";
import NotificationsUser from "../components/user/Notifications.vue";
import ProfileUser from "../components/user/Profile.vue";
import ConstatUser from "../components/user/informations/constat/Constat.vue";
import AjoutConstatUser from "../components/user/informations/constat/AjoutConstat.vue";
import EditConstatUser from "../components/user/informations/constat/EditConstat.vue";
import VoirConstatUser from "../components/user/informations/constat/VoirConstat.vue";
import ResponsableUser from "../components/user/informations/responsable/Responsable.vue";
import AjoutResponsableUser from "../components/user/informations/responsable/AjoutResponsable.vue";
import VoirResponsableUser from "../components/user/informations/responsable/VoirResponsable.vue";
import EditResponsableUser from "../components/user/informations/responsable/EditResponsable.vue";
import SourcesUser from "../components/user/informations/sources/Sources.vue";
import AjoutSourcesUser from "../components/user/informations/sources/AjoutSources.vue";
import VoirSourcesUser from "../components/user/informations/sources/VoirSources.vue";
import EditSourcesUser from "../components/user/informations/sources/EditSources.vue";
import SuiviUser from "../components/user/informations/suivi/Suivi.vue";
import AjoutSuiviUser from "../components/user/informations/suivi/AjoutSuivi.vue";
import VoirSuiviUser from "../components/user/informations/suivi/VoirSuivi.vue";
import EditSuiviUser from "../components/user/informations/suivi/EditSuivi.vue";
import AjoutTypeActionsUser from "../components/user/informations/typeactions/AjoutTypeActions.vue";
import VoirTypeActionsUser from "../components/user/informations/typeactions/VoirTypeActions.vue";
import EditTypeActionsUser from "../components/user/informations/typeactions/EditTypeActions.vue";
import TypeActionsUser from "../components/user/informations/typeactions/TypeActions.vue";
import AuditInterneUser from "../components/user/actions/auditinterne/AuditInterne.vue";
import AjoutAuditInterneUser from "../components/user/actions/auditinterne/AjoutAuditInterne.vue";
import VoirAuditInterneUser from "../components/user/actions/auditinterne/VoirAuditInterne.vue";
import EditAuditInterneUser from "../components/user/actions/auditinterne/EditAuditInterne.vue";
import PtaUser from "../components/user/actions/pta/Pta.vue";
import AjoutPtaUser from "../components/user/actions/pta/AjoutPta.vue";
import VoirPtaUser from "../components/user/actions/pta/VoirPta.vue";
import EditPtaUser from "../components/user/actions/pta/EditPta.vue";
import VoirOtherAuditInterne from "../components/admin/actions/auditinterne/VoirOtherAuditInterne.vue";
import VoirOtherAuditInterneUser from "../components/user/actions/auditinterne/VoirOtherAuditInterne.vue";
import VoirOtherPta from "../components/admin/actions/pta/VoirOtherPta.vue";
import VoirOtherPtaUser from "../components/user/actions/pta/VoirOtherPta.vue";
import AuditInterneResponsable from "../components/responsable/actions/auditinterne/AuditInterne.vue";
import VoirAuditInterneResponsable from "../components/responsable/actions/auditinterne/VoirAuditInterne.vue";
import EditAuditInterneResponsable from "../components/responsable/actions/auditinterne/EditAuditInterne.vue";
import PtaResponsable from "../components/responsable/actions/pta/Pta.vue";
import VoirPtaResponsable from "../components/responsable/actions/pta/VoirPta.vue";
import EditPtaResponsable from "../components/responsable/actions/pta/EditPta.vue";
import AuditInterneSuivi from "../components/suivi/actions/auditinterne/AuditInterne.vue";
import VoirAuditInterneSuivi from "../components/suivi/actions/auditinterne/VoirAuditInterne.vue";
import EditAuditInterneSuivi from "../components/suivi/actions/auditinterne/EditAuditInterne.vue";
import PtaSuivi from "../components/suivi/actions/pta/Pta.vue";
import VoirPtaSuivi from "../components/suivi/actions/pta/VoirPta.vue";
import EditPtaSuivi from "../components/suivi/actions/pta/EditPta.vue";
import Swot from "../components/admin/actions/swot/Swot.vue";
import AjoutSwot from "../components/admin/actions/swot/AjoutSwot.vue";
import VoirSwot from "../components/admin/actions/swot/VoirSwot.vue";
import EditSwot from "../components/admin/actions/swot/EditSwot.vue";
import VoirOtherSwot from "../components/admin/actions/swot/VoirOtherSwot.vue";
import SwotUser from "../components/user/actions/swot/Swot.vue";
import AjoutSwotUser from "../components/user/actions/swot/AjoutSwot.vue";
import VoirSwotUser from "../components/user/actions/swot/VoirSwot.vue";
import EditSwotUser from "../components/user/actions/swot/EditSwot.vue";
import VoirOtherSwotUser from "../components/user/actions/swot/VoirOtherSwot.vue";
import SwotResponsable from "../components/responsable/actions/swot/Swot.vue";
import VoirSwotResponsable from "../components/responsable/actions/swot/VoirSwot.vue";
import EditSwotResponsable from "../components/responsable/actions/swot/EditSwot.vue";
import SwotSuivi from "../components/suivi/actions/swot/Swot.vue";
import VoirSwotSuivi from "../components/suivi/actions/swot/VoirSwot.vue";
import EditSwotSuivi from "../components/suivi/actions/swot/EditSwot.vue";
import Aii from "../components/admin/actions/aii/Aii.vue";
import AjoutAii from "../components/admin/actions/aii/AjoutAii.vue";
import VoirAii from "../components/admin/actions/aii/VoirAii.vue";
import EditAii from "../components/admin/actions/aii/EditAii.vue";
import VoirOtherAii from "../components/admin/actions/aii/VoirOtherAii.vue";
import AiiUser from "../components/user/actions/aii/Aii.vue";
import AjoutAiiUser from "../components/user/actions/aii/AjoutAii.vue";
import VoirAiiUser from "../components/user/actions/aii/VoirAii.vue";
import EditAiiUser from "../components/user/actions/aii/EditAii.vue";
import VoirOtherAiiUser from "../components/user/actions/aii/VoirOtherAii.vue";
import AiiResponsable from "../components/responsable/actions/aii/Aii.vue";
import VoirAiiResponsable from "../components/responsable/actions/aii/VoirAii.vue";
import EditAiiResponsable from "../components/responsable/actions/aii/EditAii.vue";
import AiiSuivi from "../components/suivi/actions/aii/Aii.vue";
import VoirAiiSuivi from "../components/suivi/actions/aii/VoirAii.vue";
import EditAiiSuivi from "../components/suivi/actions/aii/EditAii.vue";
import Ae from "../components/admin/actions/ae/Ae.vue";
import AjoutAe from "../components/admin/actions/ae/AjoutAe.vue";
import VoirAe from "../components/admin/actions/ae/VoirAe.vue";
import EditAe from "../components/admin/actions/ae/EditAe.vue";
import VoirOtherAe from "../components/admin/actions/ae/VoirOtherAe.vue";
import AeUser from "../components/user/actions/ae/Ae.vue";
import AjoutAeUser from "../components/user/actions/ae/AjoutAe.vue";
import VoirAeUser from "../components/user/actions/ae/VoirAe.vue";
import EditAeUser from "../components/user/actions/ae/EditAe.vue";
import VoirOtherAeUser from "../components/user/actions/ae/VoirOtherAe.vue";
import AeResponsable from "../components/responsable/actions/ae/Ae.vue";
import VoirAeResponsable from "../components/responsable/actions/ae/VoirAe.vue";
import EditAeResponsable from "../components/responsable/actions/ae/EditAe.vue";
import AeSuivi from "../components/suivi/actions/ae/Ae.vue";
import VoirAeSuivi from "../components/suivi/actions/ae/VoirAe.vue";
import EditAeSuivi from "../components/suivi/actions/ae/EditAe.vue";
import Enquete from "../components/admin/actions/enquete/Enquete.vue";
import AjoutEnquete from "../components/admin/actions/enquete/AjoutEnquete.vue";
import VoirEnquete from "../components/admin/actions/enquete/VoirEnquete.vue";
import EditEnquete from "../components/admin/actions/enquete/EditEnquete.vue";
import VoirOtherEnquete from "../components/admin/actions/enquete/VoirOtherEnquete.vue";
import EnqueteUser from "../components/user/actions/enquete/Enquete.vue";
import AjoutEnqueteUser from "../components/user/actions/enquete/AjoutEnquete.vue";
import VoirEnqueteUser from "../components/user/actions/enquete/VoirEnquete.vue";
import EditEnqueteUser from "../components/user/actions/enquete/EditEnquete.vue";
import VoirOtherEnqueteUser from "../components/user/actions/enquete/VoirOtherEnquete.vue";
import EnqueteResponsable from "../components/responsable/actions/enquete/Enquete.vue";
import VoirEnqueteResponsable from "../components/responsable/actions/enquete/VoirEnquete.vue";
import EditEnqueteResponsable from "../components/responsable/actions/enquete/EditEnquete.vue";
import EnqueteSuivi from "../components/suivi/actions/enquete/Enquete.vue";
import VoirEnqueteSuivi from "../components/suivi/actions/enquete/VoirEnquete.vue";
import EditEnqueteSuivi from "../components/suivi/actions/enquete/EditEnquete.vue";
import Cac from "../components/admin/actions/cac/Cac.vue";
import AjoutCac from "../components/admin/actions/cac/AjoutCac.vue";
import VoirCac from "../components/admin/actions/cac/VoirCac.vue";
import EditCac from "../components/admin/actions/cac/EditCac.vue";
import VoirOtherCac from "../components/admin/actions/cac/VoirOtherCac.vue";
import CacUser from "../components/user/actions/cac/Cac.vue";
import AjoutCacUser from "../components/user/actions/cac/AjoutCac.vue";
import VoirCacUser from "../components/user/actions/cac/VoirCac.vue";
import EditCacUser from "../components/user/actions/cac/EditCac.vue";
import VoirOtherCacUser from "../components/user/actions/cac/VoirOtherCac.vue";
import CacResponsable from "../components/responsable/actions/cac/Cac.vue";
import VoirCacResponsable from "../components/responsable/actions/cac/VoirCac.vue";
import EditCacResponsable from "../components/responsable/actions/cac/EditCac.vue";
import CacSuivi from "../components/suivi/actions/cac/Cac.vue";
import VoirCacSuivi from "../components/suivi/actions/cac/VoirCac.vue";
import EditCacSuivi from "../components/suivi/actions/cac/EditCac.vue";
import AuditInterneHistorique from "../components/admin/historiques/AuditInterne.vue";
import PtaHistorique from "../components/admin/historiques/Pta.vue";
import SwotHistorique from "../components/admin/historiques/Swot.vue";
import CacHistorique from "../components/admin/historiques/Cac.vue";
import AeHistorique from "../components/admin/historiques/Ae.vue";
import EnqueteHistorique from "../components/admin/historiques/Enquete.vue";
import AiiHistorique from "../components/admin/historiques/Aii.vue";
import AuditInterneHistoriqueUser from "../components/user/historiques/AuditInterne.vue";
import PtaHistoriqueUser from "../components/user/historiques/Pta.vue";
import SwotHistoriqueUser from "../components/user/historiques/Swot.vue";
import CacHistoriqueUser from "../components/user/historiques/Cac.vue";
import AeHistoriqueUser from "../components/user/historiques/Ae.vue";
import EnqueteHistoriqueUser from "../components/user/historiques/Enquete.vue";
import AiiHistoriqueUser from "../components/user/historiques/Aii.vue";

const routes = [
    {
        path: "/",
        redirect: "/login",
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    // Route for admin
    {
        path: "/admin",
        meta: { requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "admin.dashboard",
                component: Dashboard,
            },
            {
                path: "utilisateurs/membres",
                name: "admin.utilisateurs.membres",
                component: Membre,
            },
            {
                path: "utilisateurs/membres/ajouter",
                name: "admin.utilisateurs.membres.ajouter",
                component: AjoutMembre,
            },
            {
                path: "utilisateurs/profile/:id",
                name: "admin.utilisateurs.profile",
                component: Profile,
                props: true,
            },
            {
                path: "informations/sources",
                name: "admin.informations.sources",
                component: Sources,
            },
            {
                path: "informations/sources/ajouter",
                name: "admin.informations.sources.ajouter",
                component: AjoutSources,
            },
            {
                path: "informations/sources/voir/:id",
                name: "admin.informations.sources.voir",
                component: VoirSources,
            },
            {
                path: "informations/sources/editer/:id",
                name: "admin.informations.sources.editer",
                component: EditSources,
            },
            {
                path: "informations/typeactions",
                name: "admin.informations.typeactions",
                component: TypeActions,
            },
            {
                path: "informations/typeactions/ajouter",
                name: "admin.informations.typeactions.ajouter",
                component: AjoutTypeActions,
            },
            {
                path: "informations/typeactions/voir/:id",
                name: "admin.informations.typeactions.voir",
                component: VoirTypeActions,
            },
            {
                path: "informations/typeactions/editer/:id",
                name: "admin.informations.typeactions.editer",
                component: EditTypeActions,
            },
            {
                path: "informations/responsable",
                name: "admin.informations.responsable",
                component: Responsable,
            },
            {
                path: "informations/responsable/ajouter",
                name: "admin.informations.responsable.ajouter",
                component: AjoutResponsable,
            },
            {
                path: "informations/responsable/voir/:id",
                name: "admin.informations.responsable.voir",
                component: VoirResponsable,
            },
            {
                path: "informations/responsable/editer/:id",
                name: "admin.informations.responsable.editer",
                component: EditResponsable,
            },
            {
                path: "informations/suivi",
                name: "admin.informations.suivi",
                component: Suivi,
            },
            {
                path: "informations/suivi/ajouter",
                name: "admin.informations.suivi.ajouter",
                component: AjoutSuivi,
            },
            {
                path: "informations/suivi/voir/:id",
                name: "admin.informations.suivi.voir",
                component: VoirSuivi,
            },
            {
                path: "informations/suivi/editer/:id",
                name: "admin.informations.suivi.editer",
                component: EditSuivi,
            },
            {
                path: "informations/constat",
                name: "admin.informations.constat",
                component: Constat,
            },
            {
                path: "informations/constat/ajouter",
                name: "admin.informations.constat.ajouter",
                component: AjoutConstat,
            },
            {
                path: "informations/constat/voir/:id",
                name: "admin.informations.constat.voir",
                component: VoirConstat,
            },
            {
                path: "informations/constat/editer/:id",
                name: "admin.informations.constat.editer",
                component: EditConstat,
            },
            {
                path: "actions/auditinterne",
                name: "admin.actions.auditinterne",
                component: AuditInterne,
            },
            {
                path: "actions/auditinterne/ajouter",
                name: "admin.actions.auditinterne.ajouter",
                component: AjoutAuditInterne,
            },
            {
                path: "actions/auditinterne/voir/:id",
                name: "admin.actions.auditinterne.voir",
                component: VoirAuditInterne,
            },
            {
                path: "actions/auditinterne/voir/other/:id",
                name: "admin.actions.auditinterne.voir.other",
                component: VoirOtherAuditInterne,
            },
            {
                path: "actions/auditinterne/editer/:id",
                name: "admin.actions.auditinterne.editer",
                component: EditAuditInterne,
            },
            {
                path: "actions/pta",
                name: "admin.actions.pta",
                component: Pta,
            },
            {
                path: "actions/pta/ajouter",
                name: "admin.actions.pta.ajouter",
                component: AjoutPta,
            },
            {
                path: "actions/pta/voir/:id",
                name: "admin.actions.pta.voir",
                component: VoirPta,
            },
            {
                path: "actions/pta/voir/other/:id",
                name: "admin.actions.pta.voir.other",
                component: VoirOtherPta,
            },
            {
                path: "actions/pta/editer/:id",
                name: "admin.actions.pta.editer",
                component: EditPta,
            },
            {
                path: "actions/swot",
                name: "admin.actions.swot",
                component: Swot,
            },
            {
                path: "actions/swot/ajouter",
                name: "admin.actions.swot.ajouter",
                component: AjoutSwot,
            },
            {
                path: "actions/swot/voir/:id",
                name: "admin.actions.swot.voir",
                component: VoirSwot,
            },
            {
                path: "actions/swot/editer/:id",
                name: "admin.actions.swot.editer",
                component: EditSwot,
            },
            {
                path: "actions/swot/voir/other/:id",
                name: "admin.actions.swot.voir.other",
                component: VoirOtherSwot,
            },
            {
                path: "actions/aii",
                name: "admin.actions.aii",
                component: Aii,
            },
            {
                path: "actions/aii/ajouter",
                name: "admin.actions.aii.ajouter",
                component: AjoutAii,
            },
            {
                path: "actions/aii/voir/:id",
                name: "admin.actions.aii.voir",
                component: VoirAii,
            },
            {
                path: "actions/aii/editer/:id",
                name: "admin.actions.aii.editer",
                component: EditAii,
            },
            {
                path: "actions/aii/voir/other/:id",
                name: "admin.actions.aii.voir.other",
                component: VoirOtherAii,
            },
            {
                path: "actions/ae",
                name: "admin.actions.ae",
                component: Ae,
            },
            {
                path: "actions/ae/ajouter",
                name: "admin.actions.ae.ajouter",
                component: AjoutAe,
            },
            {
                path: "actions/ae/voir/:id",
                name: "admin.actions.ae.voir",
                component: VoirAe,
            },
            {
                path: "actions/ae/editer/:id",
                name: "admin.actions.ae.editer",
                component: EditAe,
            },
            {
                path: "actions/ae/voir/other/:id",
                name: "admin.actions.ae.voir.other",
                component: VoirOtherAe,
            },
            {
                path: "actions/enquete",
                name: "admin.actions.enquete",
                component: Enquete,
            },
            {
                path: "actions/enquete/ajouter",
                name: "admin.actions.enquete.ajouter",
                component: AjoutEnquete,
            },
            {
                path: "actions/enquete/voir/:id",
                name: "admin.actions.enquete.voir",
                component: VoirEnquete,
            },
            {
                path: "actions/enquete/editer/:id",
                name: "admin.actions.enquete.editer",
                component: EditEnquete,
            },
            {
                path: "actions/enquete/voir/other/:id",
                name: "admin.actions.enquete.voir.other",
                component: VoirOtherEnquete,
            },
            {
                path: "actions/cac",
                name: "admin.actions.cac",
                component: Cac,
            },
            {
                path: "actions/cac/ajouter",
                name: "admin.actions.cac.ajouter",
                component: AjoutCac,
            },
            {
                path: "actions/cac/voir/:id",
                name: "admin.actions.cac.voir",
                component: VoirCac,
            },
            {
                path: "actions/cac/editer/:id",
                name: "admin.actions.cac.editer",
                component: EditCac,
            },
            {
                path: "actions/cac/voir/other/:id",
                name: "admin.actions.cac.voir.other",
                component: VoirOtherCac,
            },
            {
                path: "notifications",
                name: "admin.notifications",
                component: Notifications,
            },
            {
                path: "historiques/auditinterne",
                name: "admin.historiques.auditinterne",
                component: AuditInterneHistorique,
            },
            {
                path: "historiques/pta",
                name: "admin.historiques.pta",
                component: PtaHistorique,
            },
            {
                path: "historiques/swot",
                name: "admin.historiques.swot",
                component: SwotHistorique,
            },
            {
                path: "historiques/cac",
                name: "admin.historiques.cac",
                component: CacHistorique,
            },
            {
                path: "historiques/ae",
                name: "admin.historiques.ae",
                component: AeHistorique,
            },
            {
                path: "historiques/enquete",
                name: "admin.historiques.enquete",
                component: EnqueteHistorique,
            },
            {
                path: "historiques/aii",
                name: "admin.historiques.aii",
                component: AiiHistorique,
            },
        ],
    },

    // Route for user
    {
        path: "/user",
        meta: { requiresAuth: true },
        children: [
            {
                path: "dashboard",
                name: "user.dashboard",
                component: DashboardUser,
            },
            {
                path: "notifications",
                name: "user.notifications",
                component: NotificationsUser,
            },
            {
                path: "profile/:id",
                name: "user.profile",
                component: ProfileUser,
                props: true,
            },
            {
                path: "informations/constat",
                name: "user.informations.constat",
                component: ConstatUser,
            },
            {
                path: "informations/constat/ajouter",
                name: "user.informations.constat.ajouter",
                component: AjoutConstatUser,
            },
            {
                path: "informations/constat/editer/:id",
                name: "user.informations.constat.editer",
                component: EditConstatUser,
            },
            {
                path: "informations/constat/voir/:id",
                name: "user.informations.constat.voir",
                component: VoirConstatUser,
            },
            {
                path: "informations/responsable",
                name: "user.informations.responsable",
                component: ResponsableUser,
            },
            {
                path: "informations/responsable/ajouter",
                name: "user.informations.responsable.ajouter",
                component: AjoutResponsableUser,
            },
            {
                path: "informations/responsable/voir/:id",
                name: "user.informations.responsable.voir",
                component: VoirResponsableUser,
            },
            {
                path: "informations/responsable/editer/:id",
                name: "user.informations.responsable.editer",
                component: EditResponsableUser,
            },
            {
                path: "informations/sources",
                name: "user.informations.sources",
                component: SourcesUser,
            },
            {
                path: "informations/sources/ajouter",
                name: "user.informations.sources.ajouter",
                component: AjoutSourcesUser,
            },
            {
                path: "informations/sources/voir/:id",
                name: "user.informations.sources.voir",
                component: VoirSourcesUser,
            },
            {
                path: "informations/sources/editer/:id",
                name: "user.informations.sources.editer",
                component: EditSourcesUser,
            },
            {
                path: "informations/suivi",
                name: "user.informations.suivi",
                component: SuiviUser,
            },
            {
                path: "informations/suivi/ajouter",
                name: "user.informations.suivi.ajouter",
                component: AjoutSuiviUser,
            },
            {
                path: "informations/suivi/voir/:id",
                name: "user.informations.suivi.voir",
                component: VoirSuiviUser,
            },
            {
                path: "informations/suivi/editer/:id",
                name: "user.informations.suivi.editer",
                component: EditSuiviUser,
            },
            {
                path: "informations/typeactions",
                name: "user.informations.typeactions",
                component: TypeActionsUser,
            },
            {
                path: "informations/typeactions/ajouter",
                name: "user.informations.typeactions.ajouter",
                component: AjoutTypeActionsUser,
            },
            {
                path: "informations/typeactions/voir/:id",
                name: "user.informations.typeactions.voir",
                component: VoirTypeActionsUser,
            },
            {
                path: "informations/typeactions/editer/:id",
                name: "user.informations.typeactions.editer",
                component: EditTypeActionsUser,
            },
            {
                path: "actions/auditinterne",
                name: "user.actions.auditinterne",
                component: AuditInterneUser,
            },
            {
                path: "actions/auditinterne/ajouter",
                name: "user.actions.auditinterne.ajouter",
                component: AjoutAuditInterneUser,
            },
            {
                path: "actions/auditinterne/voir/:id",
                name: "user.actions.auditinterne.voir",
                component: VoirAuditInterneUser,
            },
            {
                path: "actions/auditinterne/voir/other/:id",
                name: "user.actions.auditinterne.voir.other",
                component: VoirOtherAuditInterneUser,
            },
            {
                path: "actions/auditinterne/editer/:id",
                name: "user.actions.auditinterne.editer",
                component: EditAuditInterneUser,
            },
            {
                path: "actions/pta",
                name: "user.actions.pta",
                component: PtaUser,
            },
            {
                path: "actions/pta/ajouter",
                name: "user.actions.pta.ajouter",
                component: AjoutPtaUser,
            },
            {
                path: "actions/pta/voir/:id",
                name: "user.actions.pta.voir",
                component: VoirPtaUser,
            },
            {
                path: "actions/pta/voir/other/:id",
                name: "user.actions.pta.voir.other",
                component: VoirOtherPtaUser,
            },
            {
                path: "actions/pta/editer/:id",
                name: "user.actions.pta.editer",
                component: EditPtaUser,
            },
            {
                path: "actions/swot",
                name: "user.actions.swot",
                component: SwotUser,
            },
            {
                path: "actions/swot/ajouter",
                name: "user.actions.swot.ajouter",
                component: AjoutSwotUser,
            },
            {
                path: "actions/swot/voir/:id",
                name: "user.actions.swot.voir",
                component: VoirSwotUser,
            },
            {
                path: "actions/swot/editer/:id",
                name: "user.actions.swot.editer",
                component: EditSwotUser,
            },
            {
                path: "actions/swot/voir/other/:id",
                name: "user.actions.swot.voir.other",
                component: VoirOtherSwotUser,
            },
            {
                path: "actions/aii",
                name: "user.actions.aii",
                component: AiiUser,
            },
            {
                path: "actions/aii/ajouter",
                name: "user.actions.aii.ajouter",
                component: AjoutAiiUser,
            },
            {
                path: "actions/aii/voir/:id",
                name: "user.actions.aii.voir",
                component: VoirAiiUser,
            },
            {
                path: "actions/aii/editer/:id",
                name: "user.actions.aii.editer",
                component: EditAiiUser,
            },
            {
                path: "actions/aii/voir/other/:id",
                name: "user.actions.aii.voir.other",
                component: VoirOtherAiiUser,
            },
            {
                path: "actions/ae",
                name: "user.actions.ae",
                component: AeUser,
            },
            {
                path: "actions/ae/ajouter",
                name: "user.actions.ae.ajouter",
                component: AjoutAeUser,
            },
            {
                path: "actions/ae/voir/:id",
                name: "user.actions.ae.voir",
                component: VoirAeUser,
            },
            {
                path: "actions/ae/editer/:id",
                name: "user.actions.ae.editer",
                component: EditAeUser,
            },
            {
                path: "actions/ae/voir/other/:id",
                name: "user.actions.ae.voir.other",
                component: VoirOtherAeUser,
            },
            {
                path: "actions/enquete",
                name: "user.actions.enquete",
                component: EnqueteUser,
            },
            {
                path: "actions/enquete/ajouter",
                name: "user.actions.enquete.ajouter",
                component: AjoutEnqueteUser,
            },
            {
                path: "actions/enquete/voir/:id",
                name: "user.actions.enquete.voir",
                component: VoirEnqueteUser,
            },
            {
                path: "actions/enquete/editer/:id",
                name: "user.actions.enquete.editer",
                component: EditEnqueteUser,
            },
            {
                path: "actions/enquete/voir/other/:id",
                name: "user.actions.enquete.voir.other",
                component: VoirOtherEnqueteUser,
            },
            {
                path: "actions/cac",
                name: "user.actions.cac",
                component: CacUser,
            },
            {
                path: "actions/cac/ajouter",
                name: "user.actions.cac.ajouter",
                component: AjoutCacUser,
            },
            {
                path: "actions/cac/voir/:id",
                name: "user.actions.cac.voir",
                component: VoirCacUser,
            },
            {
                path: "actions/cac/editer/:id",
                name: "user.actions.cac.editer",
                component: EditCacUser,
            },
            {
                path: "actions/cac/voir/other/:id",
                name: "user.actions.cac.voir.other",
                component: VoirOtherCacUser,
            },
            {
                path: "historiques/auditinterne",
                name: "user.historiques.auditinterne",
                component: AuditInterneHistoriqueUser,
            },
            {
                path: "historiques/pta",
                name: "user.historiques.pta",
                component: PtaHistoriqueUser,
            },
            {
                path: "historiques/swot",
                name: "user.historiques.swot",
                component: SwotHistoriqueUser,
            },
            {
                path: "historiques/cac",
                name: "user.historiques.cac",
                component: CacHistoriqueUser,
            },
            {
                path: "historiques/ae",
                name: "user.historiques.ae",
                component: AeHistoriqueUser,
            },
            {
                path: "historiques/enquete",
                name: "user.historiques.enquete",
                component: EnqueteHistoriqueUser,
            },
            {
                path: "historiques/aii",
                name: "user.historiques.aii",
                component: AiiHistoriqueUser,
            },
        ],
    },

    // Route pour responsable des actions
    {
        path: "/responsable",
        meta: { requiresAuth: true },
        children: [
            {
                path: "actions/auditinterne",
                name: "responsable.actions.auditinterne",
                component: AuditInterneResponsable,
            },
            {
                path: "actions/auditinterne/voir/:id",
                name: "responsable.actions.auditinterne.voir",
                component: VoirAuditInterneResponsable,
            },
            {
                path: "actions/auditinterne/editer/:id",
                name: "responsable.actions.auditinterne.editer",
                component: EditAuditInterneResponsable,
            },
            {
                path: "actions/pta",
                name: "responsable.actions.pta",
                component: PtaResponsable,
            },
            {
                path: "actions/pta/voir/:id",
                name: "responsable.actions.pta.voir",
                component: VoirPtaResponsable,
            },
            {
                path: "actions/pta/editer/:id",
                name: "responsable.actions.pta.editer",
                component: EditPtaResponsable,
            },
            {
                path: "actions/swot",
                name: "responsable.actions.swot",
                component: SwotResponsable,
            },
            {
                path: "actions/swot/voir/:id",
                name: "responsable.actions.swot.voir",
                component: VoirSwotResponsable,
            },
            {
                path: "actions/swot/editer/:id",
                name: "responsables.actions.swot.editer",
                component: EditSwotResponsable,
            },
            {
                path: "actions/aii",
                name: "responsable.actions.aii",
                component: AiiResponsable,
            },
            {
                path: "actions/aii/voir/:id",
                name: "responsable.actions.aii.voir",
                component: VoirAiiResponsable,
            },
            {
                path: "actions/aii/editer/:id",
                name: "responsable.actions.aii.editer",
                component: EditAiiResponsable,
            },
            {
                path: "actions/ae",
                name: "responsable.actions.ae",
                component: AeResponsable,
            },
            {
                path: "actions/ae/voir/:id",
                name: "responsable.actions.ae.voir",
                component: VoirAeResponsable,
            },
            {
                path: "actions/ae/editer/:id",
                name: "responsable.actions.ae.editer",
                component: EditAeResponsable,
            },
            {
                path: "actions/enquete",
                name: "responsable.actions.enquete",
                component: EnqueteResponsable,
            },
            {
                path: "actions/enquete/voir/:id",
                name: "responsable.actions.enquete.voir",
                component: VoirEnqueteResponsable,
            },
            {
                path: "actions/enquete/editer/:id",
                name: "responsable.actions.enquete.editer",
                component: EditEnqueteResponsable,
            },
            {
                path: "actions/cac",
                name: "responsable.actions.cac",
                component: CacResponsable,
            },
            {
                path: "actions/cac/voir/:id",
                name: "responsable.actions.cac.voir",
                component: VoirCacResponsable,
            },
            {
                path: "actions/cac/editer/:id",
                name: "responsable.actions.cac.editer",
                component: EditCacResponsable,
            },
        ],
    },

    // Route pour les suivis d'actions
    {
        path: "/suivi",
        meta: { requiresAuth: true },
        children: [
            {
                path: "actions/auditinterne",
                name: "suivi.actions.auditinterne",
                component: AuditInterneSuivi,
            },
            {
                path: "actions/auditinterne/voir/:id",
                name: "suivi.actions.auditinterne.voir",
                component: VoirAuditInterneSuivi,
            },
            {
                path: "actions/auditinterne/editer/:id",
                name: "suivi.actions.auditinterne.editer",
                component: EditAuditInterneSuivi,
            },
            {
                path: "actions/pta",
                name: "suivi.actions.pta",
                component: PtaSuivi,
            },
            {
                path: "actions/pta/voir/:id",
                name: "suivi.actions.pta.voir",
                component: VoirPtaSuivi,
            },
            {
                path: "actions/pta/editer/:id",
                name: "suivi.actions.pta.editer",
                component: EditPtaSuivi,
            },
            {
                path: "actions/swot",
                name: "suivi.actions.swot",
                component: SwotSuivi,
            },
            {
                path: "actions/swot/voir/:id",
                name: "suivi.actions.swot.voir",
                component: VoirSwotSuivi,
            },
            {
                path: "actions/swot/editer/:id",
                name: "suivi.actions.swot.editer",
                component: EditSwotSuivi,
            },
            {
                path: "actions/aii",
                name: "suivi.actions.aii",
                component: AiiSuivi,
            },
            {
                path: "actions/aii/voir/:id",
                name: "suivi.actions.aii.voir",
                component: VoirAiiSuivi,
            },
            {
                path: "actions/aii/editer/:id",
                name: "suivi.actions.aii.editer",
                component: EditAiiSuivi,
            },
            {
                path: "actions/ae",
                name: "suivi.actions.ae",
                component: AeSuivi,
            },
            {
                path: "actions/ae/voir/:id",
                name: "suivi.actions.ae.voir",
                component: VoirAeSuivi,
            },
            {
                path: "actions/ae/editer/:id",
                name: "suivi.actions.ae.editer",
                component: EditAeSuivi,
            },
            {
                path: "actions/enquete",
                name: "suivi.actions.enquete",
                component: EnqueteSuivi,
            },
            {
                path: "actions/enquete/voir/:id",
                name: "suivi.actions.enquete.voir",
                component: VoirEnqueteSuivi,
            },
            {
                path: "actions/enquete/editer/:id",
                name: "suivi.actions.enquete.editer",
                component: EditEnqueteSuivi,
            },
            {
                path: "actions/cac",
                name: "suivi.actions.cac",
                component: CacSuivi,
            },
            {
                path: "actions/cac/voir/:id",
                name: "suivi.actions.cac.voir",
                component: VoirCacSuivi,
            },
            {
                path: "actions/cac/editer/:id",
                name: "suivi.actions.cac.editer",
                component: EditCacSuivi,
            },
        ],
    },

    // Route non trouvé
    {
        path: "/:pathMatch(.*)*",
        name: "notfound",
        component: NotFound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
    return auth({ to, next });
});

export default router;
