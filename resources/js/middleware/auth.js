export default function auth({ to, next }) {
    const user = JSON.parse(localStorage.getItem("user"));
    const token = localStorage.getItem("token");

    // Si la route requiert une authentification
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        // Vérifier si l'utilisateur est connecté
        if (!user || !token) {
            return next("/login");
        }

        // Vérifier les routes admin
        if (to.path.startsWith("/admin")) {
            if (user.role !== "admin") {
                return next("/user/dashboard");
            }
        }

        // Vérifier les routes user
        if (to.path.startsWith("/user")) {
            if (user.role !== "user") {
                return next("/admin/dashboard");
            }
        }
    }

    // Redirection si déjà connecté
    if (to.path === "/login" && user && token) {
        return next(
            user.role === "admin" ? "/admin/dashboard" : "/user/dashboard"
        );
    }

    return next();
}
