import { createRouter, createWebHistory } from "vue-router";
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

const routes = [
    {
        path: "/",
        name: "login",
        component: Login,
    },
    {
        path: "/admin/dashboard",
        name: "admin.dashboard",
        component: Dashboard,
    },
    {
        path: "/admin/utilisateurs/membres",
        name: "admin.utilisateurs.membres",
        component: Membre,
    },
    {
        path: "/admin/utilisateurs/membres/ajouter",
        name: "admin.utilisateurs.membres.ajouter",
        component: AjoutMembre,
    },
    {
        path: "/admin/utilisateurs/profile",
        name: "admin.utilisateurs.profile",
        component: Profile,
    },
    {
        path: "/admin/informations/sources",
        name: "admin.informations.sources",
        component: Sources,
    },
    {
        path: "/admin/informations/sources/ajouter",
        name: "admin.informations.sources.ajouter",
        component: AjoutSources,
    },
    {
        path: "/admin/informations/sources/voir",
        name: "admin.informations.sources.voir",
        component: VoirSources,
    },
    {
        path: "/admin/informations/sources/editer",
        name: "admin.informations.sources.editer",
        component: EditSources,
    },
    {
        path: "/admin/informations/typeactions",
        name: "admin.informations.typeactions",
        component: TypeActions,
    },
    {
        path: "/admin/informations/typeactions/ajouter",
        name: "admin.informations.typeactions.ajouter",
        component: AjoutTypeActions,
    },
    {
        path: "/admin/informations/typeactions/voir",
        name: "admin.informations.typeactions.voir",
        component: VoirTypeActions,
    },
    {
        path: "/admin/informations/typeactions/editer",
        name: "admin.informations.typeactions.editer",
        component: EditTypeActions,
    },
    {
        path: "/admin/informations/responsable",
        name: "admin.informations.responsable",
        component: Responsable,
    },
    {
        path: "/admin/informations/responsable/ajouter",
        name: "admin.informations.responsable.ajouter",
        component: AjoutResponsable,
    },
    {
        path: "/admin/informations/responsable/voir",
        name: "admin.informations.responsable.voir",
        component: VoirResponsable,
    },
    {
        path: "/admin/informations/responsable/editer",
        name: "admin.informations.responsable.editer",
        component: EditResponsable,
    },
    {
        path: "/admin/informations/suivi",
        name: "admin.informations.suivi",
        component: Suivi,
    },
    {
        path: "/admin/informations/suivi/ajouter",
        name: "admin.informations.suivi.ajouter",
        component: AjoutSuivi,
    },
    {
        path: "/admin/informations/suivi/voir",
        name: "admin.informations.suivi.voir",
        component: VoirSuivi,
    },
    {
        path: "/admin/informations/suivi/editer",
        name: "admin.informations.suivi.editer",
        component: EditSuivi,
    },
    {
        path: "/admin/informations/constat",
        name: "admin.informations.constat",
        component: Constat,
    },
    {
        path: "/admin/informations/constat/ajouter",
        name: "admin.informations.constat.ajouter",
        component: AjoutConstat,
    },
    {
        path: "/admin/informations/constat/voir",
        name: "admin.informations.constat.voir",
        component: VoirConstat,
    },
    {
        path: "/admin/informations/constat/editer",
        name: "admin.informations.constat.editer",
        component: EditConstat,
    },
    {
        path: "/admin/actions/auditinterne",
        name: "admin.actions.auditinterne",
        component: AuditInterne,
    },
    {
        path: "/admin/actions/auditinterne/ajouter",
        name: "admin.actions.auditinterne.ajouter",
        component: AjoutAuditInterne,
    },
    {
        path: "/admin/actions/auditinterne/voir",
        name: "admin.actions.auditinterne.voir",
        component: VoirAuditInterne,
    },
    {
        path: "/admin/actions/auditinterne/editer",
        name: "admin.actions.auditinterne.editer",
        component: EditAuditInterne,
    },
    {
        path: "/admin/actions/pta",
        name: "admin.actions.pta",
        component: Pta,
    },
    {
        path: "/admin/actions/pta/ajouter",
        name: "admin.actions.pta.ajouter",
        component: AjoutPta,
    },
    {
        path: "/admin/actions/pta/voir",
        name: "admin.actions.pta.voir",
        component: VoirPta,
    },
    {
        path: "/admin/actions/pta/editer",
        name: "admin.actions.pta.editer",
        component: EditPta,
    },
    {
        path: "/admin/notifications",
        name: "admin.notifications",
        component: Notifications,
    },
    // Route for user
    {
        path: "/user/dashboard",
        name: "user.dashboard",
        component: DashboardUser,
    },
    {
        path: "/user/notifications",
        name: "user.notifications",
        component: NotificationsUser,
    },
    {
        path: "/user/profile",
        name: "user.profile",
        component: ProfileUser,
    },
    {
        path: "/user/informations/constat",
        name: "user.informations.constat",
        component: ConstatUser,
    },
    {
        path: "/user/informations/constat/ajouter",
        name: "user.informations.constat.ajouter",
        component: AjoutConstatUser,
    },
    {
        path: "/user/informations/constat/editer",
        name: "user.informations.constat.editer",
        component: EditConstatUser,
    },
    {
        path: "/user/informations/constat/voir",
        name: "user.informations.constat.voir",
        component: VoirConstatUser,
    },
    {
        path: "/user/informations/responsable",
        name: "user.informations.responsable",
        component: ResponsableUser,
    },
    {
        path: "/user/informations/responsable/ajouter",
        name: "user.informations.responsable.ajouter",
        component: AjoutResponsableUser,
    },
    {
        path: "/user/informations/responsable/voir",
        name: "user.informations.responsable.voir",
        component: VoirResponsableUser,
    },
    {
        path: "/user/informations/responsable/editer",
        name: "user.informations.responsable.editer",
        component: EditResponsableUser,
    },
    {
        path: "/user/informations/sources",
        name: "user.informations.sources",
        component: SourcesUser,
    },
    {
        path: "/user/informations/sources/ajouter",
        name: "user.informations.sources.ajouter",
        component: AjoutSourcesUser,
    },
    {
        path: "/user/informations/sources/voir",
        name: "user.informations.sources.voir",
        component: VoirSourcesUser,
    },
    {
        path: "/user/informations/sources/editer",
        name: "user.informations.sources.editer",
        component: EditSourcesUser,
    },
    {
        path: "/user/informations/suivi",
        name: "user.informations.suivi",
        component: SuiviUser,
    },
    {
        path: "/user/informations/suivi/ajouter",
        name: "user.informations.suivi.ajouter",
        component: AjoutSuiviUser,
    },
    {
        path: "/user/informations/suivi/voir",
        name: "user.informations.suivi.voir",
        component: VoirSuiviUser,
    },
    {
        path: "/user/informations/suivi/editer",
        name: "user.informations.suivi.editer",
        component: EditSuiviUser,
    },
    {
        path: "/user/informations/typeactions",
        name: "user.informations.typeactions",
        component: TypeActionsUser,
    },
    {
        path: "/user/informations/typeactions/ajouter",
        name: "user.informations.typeactions.ajouter",
        component: AjoutTypeActionsUser,
    },
    {
        path: "/user/informations/typeactions/voir",
        name: "user.informations.typeactions.voir",
        component: VoirTypeActionsUser,
    },
    {
        path: "/user/informations/typeactions/editer",
        name: "user.informations.typeactions.editer",
        component: EditTypeActionsUser,
    },
    {
        path: "/user/actions/auditinterne",
        name: "user.actions.auditinterne",
        component: AuditInterneUser,
    },
    {
        path: "/user/actions/auditinterne/ajouter",
        name: "user.actions.auditinterne.ajouter",
        component: AjoutAuditInterneUser,
    },
    {
        path: "/user/actions/auditinterne/voir",
        name: "user.actions.auditinterne.voir",
        component: VoirAuditInterneUser,
    },
    {
        path: "/user/actions/auditinterne/editer",
        name: "user.actions.auditinterne.editer",
        component: EditAuditInterneUser,
    },
    {
        path: "/user/actions/pta",
        name: "user.actions.pta",
        component: PtaUser,
    },
    {
        path: "/user/actions/pta/ajouter",
        name: "user.actions.pta.ajouter",
        component: AjoutPtaUser,
    },
    {
        path: "/user/actions/pta/voir",
        name: "user.actions.pta.voir",
        component: VoirPtaUser,
    },
    {
        path: "/user/actions/pta/editer",
        name: "user.actions.pta.editer",
        component: EditPtaUser,
    },
    // Not found route
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

export default router;
