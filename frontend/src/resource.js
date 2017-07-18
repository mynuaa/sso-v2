import axios from 'axios';
import router from './routes/index';

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
        router.history.push(data.url);
        break;
    }
    return null;
};

export default {
    get: (url, config) => axios.get(url, config).then(handle),
    post: (url, data, config) => axios.post(url, data, config).then(handle),
    put: (url, data, config) => axios.put(url, data, config).then(handle),
    patch: (url, data, config) => axios.patch(url, data, config).then(handle),
    delete: (url, config) => axios.delete(url, config).then(handle)
};
