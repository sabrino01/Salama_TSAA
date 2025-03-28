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
