import axios from 'axios';
import navigation from 'utils/navigation';

let showErrorToast = true;

const handle = res => {
    const data = res.data;
    switch (data.ret) {
        case 301:
        case 302:
            navigation.go(data.url);
            break;
        case 400:
        case 401:
        case 402:
        case 403:
            if (showErrorToast) {
                iziToast.error({
                    title: 'Error',
                    message: data.msg
                });
            }
            break;
        default: // 2xx
            return data.data;
    }
    return null;
};

export default {
    get: (url, config) => {
        showErrorToast = (url !== '/api/user/current');
        return axios.get(`/sso-v2${url}`, config).then(handle);
    },
    post: (url, data, config) => {
        showErrorToast = true;
        return axios.post(`/sso-v2${url}`, data, config).then(handle);
    },
    put: (url, data, config) => {
        showErrorToast = true;
        return axios.put(`/sso-v2${url}`, data, config).then(handle);
    },
    patch: (url, data, config) => {
        showErrorToast = true;
        return axios.patch(`/sso-v2${url}`, data, config).then(handle);
    },
    delete: (url, config) => {
        showErrorToast = true;
        return axios.delete(`/sso-v2${url}`, config).then(handle);
    }
};
