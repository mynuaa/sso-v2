import Layout from '../views/layout.vue';
import Login from '../views/login.vue';
import User from '../views/user.vue';

export default [{
    path: '/',
    component: Layout,
    children: [{
        path: '/login',
        component: Login,
    }, {
        path: '/user',
        component: User,
    }]
}];
