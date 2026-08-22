export const ACCESS_TOKEN_KEY = "accessTokenJkApp";

let refreshInProgress = null;

export async function fetchAPI(path, data, method) {
    let response = await doFetch(path, data, method);
    if (response.status === 401 && response.data.code === "ACCESS_TOKEN_EXPIRED") {
        refreshInProgress = doFetch("/auth/refresh", null, "POST");
        const refreshed = await refresh(refreshInProgress);
        if(refreshed){
            response = await fetchAPI(path, data, method);
        }
    }
    return response;
}

async function doFetch(path, data, method) {
    const token = sessionStorage.getItem(ACCESS_TOKEN_KEY);
    const config = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': token ? `Bearer ${token}` : ''
        },
        credentials: 'include'
    }

    if (data && method !== 'GET') {
        config.body = JSON.stringify(data);
    }
    try {
        const uri = 'http://localhost:8000' + path
        const response = await fetch(uri, config);
        console.log("[DEBUG - API]: ", response);
        const response_formated = {
            ok: response.ok,
            status: response.status,
            data: await response.json()
        }
        console.log("[DEBUG - API - FORMATED]: ", response_formated);
        return response_formated;
    } catch (error) {
        return {
            ok: false,
            status: null,
            data: null,
            error: error.message
        };
    }
}

async function refresh(refreshPromise){
    try {
        const response = await refreshPromise;
        if (response.status !== 200 ) {
            window.sessionStorage.removeItem(ACCESS_TOKEN_KEY);
            window.location.replace("/Auth/Login/login.html");
            return false;
        }
        window.sessionStorage.setItem(ACCESS_TOKEN_KEY, response.data.data.accessToken);
        return true;
    } finally {
        refreshInProgress = null;
    }
}