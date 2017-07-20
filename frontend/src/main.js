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
    timeout: 5000,
    resetOnHover: true,
    icon: 'material-icons',
    transitionIn: 'fadeInDown',
    transitionOut: 'fadeOutUp',
    position: /mobile/i.test(navigator.userAgent) ? 'bottomCenter' : 'topCenter'
});

window.eventBus = new Vue();
window.moment = moment;

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
