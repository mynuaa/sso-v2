<template>
    <div class="user">
        <header>
            <img src="../assets/img/logo-mini.png" class="logo-mini">
            <h1>用户中心</h1>
            <span class="userInfo">
                <span>{{this.$props.user.username}}</span>
            </span>
        </header>
        <div class="scroll">
            <div class="rs-card">
                <h1 class="title">个人信息</h1>
                <img :src="`/ucenter/avatar.php?uid=${$props.user.uid}&size=middle`" class="avatar">
                <table class="info-table">
                    <tr>
                        <th>用户ID</th>
                        <td>{{$props.user.uid}}</td>
                    </tr>
                    <tr>
                        <th>用户名</th>
                        <td>{{$props.user.username}}</td>
                    </tr>
                    <tr>
                        <th>真实姓名</th>
                        <td>{{$props.user.name}}</td>
                    </tr>
                    <tr>
                        <th>学号/工号</th>
                        <td>{{$props.user.stu_num}}</td>
                    </tr>
                </table>
                <h1 class="title">操作</h1>
                <md-button class="md-raised md-primary btn-logout" @click="logout">注销</md-button>
            </div>
        </div>
    </div>
</template>

<script>
import resource from 'utils/resource';
export default {
    data() {
        return {};
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    methods: {
        logout() {
            resource.get('/api/user/logout').then(() => {
                eventBus.$emit('userLogout');
            });
        }
    }
};
</script>

<style scoped>
header {
    height: 40px;
    line-height: 40px;
    background: #333;
    color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}
.logo-mini {
    float: left;
    height: 20px;
    margin: 10px 10px 10px 20px;
}
h1 {
    float: left;
    margin: 0;
    font-size: 16px;
    font-weight: normal;
}
.userInfo {
    float: right;
    padding: 0 20px;
}
.scroll {
    padding: 40px 0;
    overflow: auto;
    height: calc(100vh - 40px);
}
.rs-card {
    max-width: calc(100% - 80px);
    margin: auto;
    overflow: hidden;
}
.rs-card .title {
    width: 100%;
    padding: 5px 0;
    border-bottom: 1px solid #ddd;
    margin: 15px 0;
    clear: both;
}
.rs-card .title:first-child {
    margin-top: 0;
}
.avatar {
    float: left;
    margin: 0 10px 0 0;
    border-radius: 50%;
    box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
}
.info-table {
    float: left;
    line-height: 28px;
}
th,
td {
    padding: 0 10px;
    text-align: left;
}
.btn-logout {
    float: left;
    margin: 0;
}
</style>
