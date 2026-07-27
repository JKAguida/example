import { fetchAPI } from "../../shared/api.js";
import { validations, validateField, isEquals } from "../../shared/form-validations.js"
import { toast } from "../../shared/toast.js";


window.document.addEventListener('DOMContentLoaded', init);

let spinner = null;
let confirmContainer = null;
let confirmMsg = null;
let form = null;
let resetForm = {};

function init() {
    console.log("[DEBUG]: reset-password loaded.");
    spinner = window.document.getElementById('spinner');
    confirmContainer = window.document.getElementById('confirm-container');
    confirmMsg = window.document.getElementById('confirm-msg');
    form = window.document.getElementById("form");
    resetForm = {
        rawPassword: {
            element: window.document.getElementById("rawPassword"),
            validations: [validations.NOT_NULL, validations.MIN_LENGTH],
            options: [null, { min: 8 }],
            msgs: ["La nueva contraseña no puede ir vacía", "La contraseña debe tener al menos 8 caracteres"]
        },
        repeatRawPassword: {
            element: window.document.getElementById("rawPasswordRepeat"),
            validations: [validations.NOT_NULL],
            msgs: ["Este campo no puede ir vacío"]
        }
    }
    handleConfirmAccount();
}

async function handleConfirmAccount() {
    const params = new URLSearchParams(window.location.search);
    const allParams = Object.fromEntries(params);


    console.log("[DEBUG_PARAMS]: ", params);
    console.log("[DEBUG__ALL_PARAMS]: ", allParams);

    if (!allParams.confirmation) {
        spinner.classList.add("hidden");
        confirmMsg.innerText = "No se encontró el token de confirmación.";
        return;
    }
    const response = await fetchAPI(`/auth/verify-reset-token?confirmation=${allParams.confirmation}`, null, 'GET');

    if (!response.ok && !response.status) {
        spinner.classList.add("hidden");
        confirmMsg.innerText = response.error;
        return;
    }

    if (response) {
        spinner.classList.add("hidden");
        confirmMsg.innerText = response.data.msg;
        if (response.ok) {
            setTimeout(() => {
                confirmContainer.classList.add("hidden");
                form.classList.remove("hidden");
            }, 2500);
            handleSubmit(allParams.confirmation);
        }
    } else {
        return;
    }
}

function handleSubmit(token) {
    let btnSubmit = window.document.getElementById("reset-pwd-btn");
    btnSubmit.addEventListener("click", async (ev) => {
        ev.preventDefault();
        validateFormFields();
        if ([...form.getElementsByClassName("error")].length > 0) return;
        const dataToSend = {
            rawPassword: resetForm.rawPassword.element.value
        }
        spinner.classList.remove("hidden");
        form.classList.add("hidden");
        console.log("[DEBUG_DATA_TO_SEND]: ", dataToSend);
        const response = await fetchAPI(`/auth/reset-password?confirmation=${token}`, dataToSend, "POST");
        if (!response.ok && !response.status) {
            toast(null, "error", response.error);
        }
        if (!response.ok && response.status) {
            toast(null, "error", response.data.msg);
        }

        if (response.ok && response.status) {
            toast(null, "success", response.data.msg);
            form.reset();
            setTimeout(() => {
                window.location.replace("/Auth/Login/login.html");
            }, 2500)
        }
        spinner.classList.add("hidden");
        form.classList.remove("hidden");

    })
}

function validateFormFields() {
    Object.entries(resetForm).forEach(([key, fieldObj]) => {
        validateField(key, fieldObj);
    })
    isEquals(resetForm.rawPassword.element, resetForm.repeatRawPassword.element, "Las contraseñas deben ser iguales");
}