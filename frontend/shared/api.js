export const ACCESS_TOKEN_KEY = "accessTokenJkApp";
export async function fetchAPI(path, data, method) {
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
        const uri = 'http://localhost:8000'+path
        const response = await fetch(uri,config);
        //console.log("[DEBUG - API]: ",response);
        return {
            ok:response.ok,
            status:response.status,
            data: await response.json()
        }
    } catch (error) {
        return {
            ok:false,
            status:null,
            data:null,
            error: error.message
        };
    }

}