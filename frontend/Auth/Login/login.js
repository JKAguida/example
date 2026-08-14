import { validations, validateField } from "../../shared/form-validations.js";
import { fetchAPI, ACCESS_TOKEN_KEY } from "../../shared/api.js";
import { toast } from "../../shared/toast.js";

window.document.addEventListener('DOMContentLoaded', init);

let loginForm = null;
let spinner = null;

function init() {
    console.log("[DEBUG]: login loaded");
    spinner = window.document.getElementById('spinner');
    spinner.classList.add('hidden');
    loginForm = {
        email: {
            element: window.document.getElementById('email'),
            validations: [validations.NOT_NULL, validations.IS_EMAIL],
            msgs: ["El email no puede ir vacío.", "Esto no parece un email."]
        },
        rawPassword: {
            element: window.document.getElementById('rawPassword'),
            validations: [validations.NOT_NULL],
            msgs: ["La contraseña no puede ir vacía."]
        }
    }
    handleSubmit();
}

function handleSubmit() {
    let form = window.document.getElementById('form');
    let btnSubmit = window.document.getElementById('login-btn');

    btnSubmit.addEventListener('click', async (ev) => {
        ev.preventDefault();
        validateForm();
        if ([...form.getElementsByClassName("error")].length > 0) {
            return;
        }
        const dataToSend = {
            email: loginForm.email.element.value,
            rawPassword: loginForm.rawPassword.element.value
        }
        //console.log("[DEBUG_DATA]: Data to send..." + JSON.stringify(dataToSend));
        form.classList.add('hidden');
        spinner.classList.remove('hidden');

        const response = await fetchAPI('/auth/login', dataToSend, 'POST');

        if (!response.ok && !response.status) {
            toast(null, 'error', response.error);
        }

        if (!response.ok && response.status) {
            toast(null, 'error', response.data.msg);
            if (response.data.code === 'NOT_VERIFIED') {
                if(!window.document.getElementById("btn-resend-email")){
                    const link = document.createElement('a');
                    link.id = "btn-resend-email";
                    link.href = '/Auth/ResendConfirmAccountToken/resendConfirmAccountToken.html';
                    link.textContent = '¿No recibiste ningún email? Solicita un nuevo email de confirmación.';
                    link.classList.add("form__links--link");
                    form.insertAdjacentElement('beforeend', link);
                }
            }

        }

        if (response.ok && response.status) {
            form.reset();
            toast(null, 'success', response.data.msg);
            window.sessionStorage.setItem(ACCESS_TOKEN_KEY, response.data.data.accessToken);
        }
        form.classList.remove('hidden');
        spinner.classList.add('hidden');
        //console.log(response);
    })
}

function validateForm() {
    Object.entries(loginForm).forEach(([key, fieldObj]) => {
        //console.log(fieldObj);
        validateField(key, fieldObj);
    })
}