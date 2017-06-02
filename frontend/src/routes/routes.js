import Home from '../views/home.vue';
import Login from '../views/login.vue';

export default [{
    path: '/',
    component: Home,
    children: [{
        path: '/login',
        component: Login,
    }]
}];
