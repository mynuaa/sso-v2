<template>
    <div class="layout">
        <router-view :user="user"></router-view>
    </div>
</template>

<script>
import resource from '../resource';
export default {
    data() {
        return {
            user: {}
        };
    },
    mounted() {
        const next = this.$route.query.next || '/user';
        eventBus.$on('userLogin', user => {
            if (user) {
                this.user = user;
                this.$router.push(next);
            }
        });
        eventBus.$on('userLogout', () => {
            this.user = {};
            this.$router.push(`/login?next=${next}`);
        });
        // next page after login
        resource.get('/api/user/current').then(data => {
            if (data) {
                eventBus.$emit('userLogin', data);
            } else {
                this.$router.push(`/login?next=${next}`);
            }
        });
    }
};
</script>
