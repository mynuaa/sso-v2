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
    async mounted() {
        this.eventBus.$on('userLogin', user => {
            if (user) {
                if (user.id === -1) {
                    navigation.go(`/complete/${user.needType}`);
                } else {
                    this.user = user;
                    navigation.next();
                }
            }
        });
        this.eventBus.$on('userLogout', () => {
            this.user = {};
            navigation.go('/login');
        });
        if (!this.$route.query.oauth) {
            const next = this.$route.query.next || '/user';
            navigation.addNext(next);
            const user = await resource.get('/api/user/current');
            if (user) {
                if (user.stu_num === 'FRESHMAN') {
                    user.stu_num = '未绑定';
                    user.name = '未绑定学号';
                }
                this.eventBus.$emit('userLogin', user);
            } else if (!this.unJumpedRoute.test(this.$route.path)) {
                navigation.go('/login');
            }
        }
    }
};
</script>
