import Vue from 'vue';
import Router from 'vue-router';
import routes from './routes';

Vue.use(Router);

const router = new Router({
    scrollBehavior: () => ({ x: 0, y: 0 }),
    routes,
    mode: 'history'
});

export default router;
