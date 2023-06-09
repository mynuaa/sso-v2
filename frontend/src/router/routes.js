import Layout from 'views/layout.vue';
import Oauth from 'views/oauth.vue';
import Login from 'views/login.vue';
import User from 'views/user.vue';
import Complete from 'views/complete.vue';

export default [{
    path: '/',
    component: Layout,
    children: [{
        path: '/login',
        component: Login
    }, {
        path: '/user',
        component: User
    }, {
        path: '/complete/:type',
        component: Complete
    }]
}, {
    path: '/oauth/:appid',
    component: Oauth,
    children: []
}];
