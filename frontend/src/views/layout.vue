<template>
    <div class="layout">
        <router-view :user="user"></router-view>
    </div>
</template>

<script>
import navigation from 'utils/navigation';
import resource from 'utils/resource';
export default {
    data() {
        return {
            user: {},
            unJumpedRoute: ['/login', '/complete']
        };
    },
    mounted() {
        const next = this.$route.query.next || '/user';
        eventBus.$on('userLogin', user => {
            if (user) {
                this.user = user;
                navigation.next();
            }
        });
        eventBus.$on('userLogout', () => {
            this.user = {};
            navigation.addNext(next).go('/login');
        });
        resource.get('/api/user/current').then(data => {
            if (data) {
                eventBus.$emit('userLogin', data);
            } else if (!this.unJumpedRoute.find(item => this.$route.path.indexOf(item) === 0)) {
                navigation.addNext(next).go('/login');
            }
        });
    }
};
</script>
