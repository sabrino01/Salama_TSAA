import { createApp } from "vue";
import App from "./App.vue";
import "./bootstrap";
import "../css/app.css";
import router from "./router";
import Swal from "sweetalert2";
import { Buffer } from "buffer";

window.Buffer = Buffer;

window.Swal = Swal;
const toast = {
    success(message) {
        Swal.fire({
            icon: "success",
            title: "Succès",
            text: message,
            timer: 2000,
            showConfirmButton: false,
            timerProgressBar: true,
        });
    },
    error(message) {
        Swal.fire({
            icon: "error",
            title: "Erreur",
            text: message,
            timer: 3000,
            showConfirmButton: false,
            timerProgressBar: true,
        });
    },
    confirm(message, callback) {
        Swal.fire({
            title: "Confirmation",
            text: message,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    },
};
window.toast = toast;

createApp(App).use(router).mount("#app");
