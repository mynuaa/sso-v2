import axios from 'axios';
import navigation from 'utils/navigation';

const handle = res => {
    const data = res.data;
    switch (data.status) {
    case 0:
        return data.data;
    case 1:
        iziToast.error({
            title: 'Error',
            message: data.msg
        });
        break;
    case 2:
        navigation.go(data.url);
        break;
    }
    return null;
};

export default {
    get: (url, config) => axios.get(`/sso-v2${url}`, config).then(handle),
    post: (url, data, config) => axios.post(`/sso-v2${url}`, data, config).then(handle),
    put: (url, data, config) => axios.put(`/sso-v2${url}`, data, config).then(handle),
    patch: (url, data, config) => axios.patch(`/sso-v2${url}`, data, config).then(handle),
    delete: (url, config) => axios.delete(`/sso-v2${url}`, config).then(handle)
};
