import { fetchAPI, ACCESS_TOKEN_KEY } from "./api.js";
import { toast } from "./toast.js";

export async function logout(){
    const response = await fetchAPI('/auth/logout',null,'POST');
    if(!response.ok){
        toast(null,'error',response.data.msg || "Error al cerrar sesión.")
        return;
    }
    window.sessionStorage.removeItem(ACCESS_TOKEN_KEY);
    window.location.replace("/Auth/Login/login.html");
}