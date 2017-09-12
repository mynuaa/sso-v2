<template>
    <div class="user">
        <header>
            <router-link to="/user">
                <img src="../assets/img/logo-mini.png" class="logo-mini">
                <h1>用户中心</h1>
            </router-link>
            <span class="userInfo">
                <span>{{this.$props.user.username}}</span>
            </span>
        </header>
        <div class="scroll">
            <div class="rs-card">
                <h1 class="title">个人信息</h1>
                <img :src="`/ucenter/avatar.php?uid=${$props.user.id}&size=middle`" class="avatar">
                <table class="info-table">
                    <tr>
                        <th>用户ID</th>
                        <td>{{$props.user.id}}</td>
                    </tr>
                    <tr>
                        <th>用户昵称</th>
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
                <h1 class="title">快速访问</h1>
                <div class="clear fast-links">
                    <a href="/xiaohongmao/">小红帽志愿服务管理认证平台</a>
                    <a href="/baike/index.php?title=首页">南航百科：首页</a>
                    <a href="/mall/?c=newgoods">南航Mall：发布新商品</a>
                    <a href="/bottle/#/mybottles">南航Bottle：我的瓶子</a>
                    <a href="/forum.php?mod=guide&view=my">纸飞机论坛：我的帖子</a>
                    <a href="/plugin.php?id=video:video">纸飞机论坛：视频区</a>
                </div>
                <h1 class="title">操作</h1>
                <div class="clear actions">
                    <a href="/home.php?mod=spacecp&ac=avatar" target="_blank">
                        <md-button class="md-raised md-primary btn-logout">修改头像</md-button>
                    </a>
                    <a href="/home.php?mod=spacecp&ac=profile&op=password" target="_blank">
                        <md-button class="md-raised md-primary btn-logout">修改密码</md-button>
                    </a>
                    <a href="/home.php?mod=spacecp&ac=profile&op=password" target="_blank">
                        <md-button class="md-raised md-accent btn-logout" v-if="this.$props.user.openid">解绑微信</md-button>
                        <md-button class="md-raised md-primary btn-logout" v-else>绑定微信</md-button>
                    </a>
                    <md-button class="md-raised md-accent btn-logout" @click="logout">注销</md-button>
                </div>
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
        async logout() {
            await resource.get('/api/user/logout');
            this.eventBus.$emit('userLogout');
        }
    }
};
</script>

<style scoped>
header {
    height: 50px;
    line-height: 50px;
    background: #333;
    color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}
.logo-mini {
    float: left;
    height: 20px;
    margin: 15px 10px 15px 20px;
}
header h1 {
    color: #fff;
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
    position: absolute;
    top: 50px;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 20px 0;
    overflow: auto;
}
.rs-card {
    width: calc(100% - 40px);
    max-width: 640px;
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
    width: 106px;
    height: 106px;
    float: left;
    margin: 0 10px 0 0;
    border-radius: 50%;
    box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
}
.info-table {
    float: left;
    line-height: 24px;
}
th,
td {
    padding: 0 10px;
    text-align: left;
}
.btn-logout {
    margin: 0;
}
.fast-links a {
    color: #2196f3;
    display: block;
    line-height: 28px;
}
.actions button {
    margin: 5px 5px 5px 0;
}
@media screen and (max-width: 400px) {
    .avatar {
        float: none;
        display: block;
        margin: 0 auto 15px;
        width: 80px;
        height: 80px;
    }
}
</style>
