import { toast } from "../../shared/toast.js";
import { validations, validateField } from "../../shared/form-validations.js";
import { fetchAPI } from "../../shared/api.js"

window.document.addEventListener('DOMContentLoaded', init);

let recoverForm = null
let spinner = null;

function init() {
    console.log("[DEBUG]: RECOVER SCRIPT LOADED");
    spinner = window.document.getElementById("spinner");
    spinner.classList.add("hidden");
    recoverForm = {
        email: {
            element: window.document.getElementById("email"),
            validations: [validations.NOT_NULL, validations.IS_EMAIL],
            msgs: ["El email no puede ir vacío.", "Esto no parece un email."]
        }
    };

    handleSubmit();
}

function handleSubmit() {
    let form = window.document.getElementById("form");
    let btnSubmit = window.document.getElementById("recover-btn");
    btnSubmit.addEventListener('click', async (ev) => {
        ev.preventDefault();
        validateForm();
        if ([...form.getElementsByClassName("error")].length > 0) return;
        const dataToSend = {
            email: recoverForm.email.element.value
        }
        console.log("[DEBUG_DATA_TO_SEND]: ", dataToSend);
        spinner.classList.remove("hidden");
        form.classList.add("hidden");
        const response = await fetchAPI("/auth/request-new-password",dataToSend,"POST");
        if(!response.ok && !response.status){
            toast(null,"error",response.error);
        }
        if(!response.ok && response.status){
            toast(null,"error",response.data.msg);
        }
        if(response.ok){
            toast(null,"success",response.data.msg);
            form.reset();
        }
        spinner.classList.add("hidden");
        form.classList.remove("hidden");
    })
}

function validateForm() {
    Object.entries(recoverForm).forEach(([key, fieldObj]) => {
        validateField(key, fieldObj);
    })
}
