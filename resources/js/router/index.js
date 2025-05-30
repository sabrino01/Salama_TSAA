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
                path: "notifications",
                name: "admin.notifications",
                component: Notifications,
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
