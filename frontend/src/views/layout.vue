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
            unJumpedRoute: /(\/login)|(\/complete\/.+)/
        };
    },
    mounted() {
        const next = this.$route.query.next || '/user';
        navigation.addNext(next);
        eventBus.$on('userLogin', user => {
            if (user) {
                this.user = user;
                navigation.next();
            }
        });
        eventBus.$on('userLogout', () => {
            this.user = {};
            navigation.go('/login');
        });
        resource.get('/api/user/current').then(data => {
            if (data) {
                eventBus.$emit('userLogin', data);
            } else if (!this.unJumpedRoute.test(this.$route.path)) {
                console.log('ok');
                navigation.go('/login');
            }
        });
    }
};
</script>
