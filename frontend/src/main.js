import Vue from 'vue';
import moment from 'moment';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueMaterial from 'vue-material';
import router from './router';
import App from './app.vue';

Vue.use(VueAxios, axios);
Vue.use(VueMaterial);

Vue.material.registerTheme('default', {
    primary: 'blue',
    danger: 'red',
    warn: 'orange',
    background: 'grey'
});

iziToast.settings({
    close: true,
    timeout: 4000,
    resetOnHover: true,
    transitionIn: 'fadeInDown',
    transitionOut: 'fadeOutUp',
    transitionInMobile: 'fadeInDown',
    transitionOutMobile: 'fadeOutUp',
    position: 'topCenter'
});

Vue.prototype.eventBus = new Vue();

window.moment = moment;

if (window.top !== window) {
    alert('检测到本系统被嵌入其它网站的页面，请立即离开。');
} else {
    new Vue({
        router,
        render: h => h(App)
    }).$mount('#app');
}

