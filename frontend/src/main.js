import Vue from 'vue';
import router from './routes/index';
import App from './app.vue';
import moment from 'moment';

import RsCard from './components/rs-card/rs-card.vue';

Vue.component('rs-card', RsCard);

window.eventBus = new Vue();
window.moment = moment;

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
