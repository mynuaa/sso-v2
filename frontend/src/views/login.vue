<template>
    <div class="rs-card fullscreen-center wrapper">
        <img src="../assets/img/logo.png" class="logo">
        <md-tabs md-fixed class="md-transparent">
            <md-tab id="nuaa" md-label="学号/工号">
                <div class="info">使用你的教务处/研究生院/教师账号和密码登录</div>
                <form novalidate @submit.stop.prevent="() => submit('nuaa')">
                    <md-input-container>
                        <label>用户名</label>
                        <md-input v-model="form.nuaa.username"></md-input>
                    </md-input-container>
                    <md-input-container md-has-password>
                        <label>密码</label>
                        <md-input type="password" v-model="form.nuaa.password"></md-input>
                        <input type="password" style="position:absolute;top:-1000px;">
                    </md-input-container>
                    <md-button type="submit" class="md-raised md-primary btn-login">登录</md-button>
                </form>
            </md-tab>
            <md-tab id="discuz" md-label="论坛账号">
                <div class="info">使用你的纸飞机论坛账号和密码登录</div>
                <form novalidate @submit.stop.prevent="() => submit('discuz')">
                    <md-input-container>
                        <label>用户名</label>
                        <md-input v-model="form.discuz.username"></md-input>
                    </md-input-container>
                    <md-input-container md-has-password>
                        <label>密码</label>
                        <md-input type="password" v-model="form.discuz.password"></md-input>
                        <input type="password" style="position:absolute;top:-1000px">
                    </md-input-container>
                    <md-button type="submit" class="md-raised md-primary btn-login">登录</md-button>
                </form>
            </md-tab>
            <md-tab id="wechat" md-label="微信扫码">
                <div class="info">使用纸飞机微信公众号菜单中的“纸飞机→万能扫码”扫描下方二维码</div>
                <img :src="wechatImgSrc" class="qrcode">
            </md-tab>
        </md-tabs>
    </div>
</template>

<script>
import resource from 'utils/resource';
export default {
    data() {
        return {
            form: {
                nuaa: {
                    username: '',
                    password: ''
                },
                discuz: {
                    username: '',
                    password: ''
                }
            },
            wechatImgSrc: 'http://my.nuaa.edu.cn/mytools/?tool=qrcode&text=这个二维码是占位用的'
        };
    },
    methods: {
        async submit(type) {
            const user = await resource.post('/api/user/login', {
                type,
                username: this.form[type].username,
                password: this.form[type].password
            });
            if (user && user.stu_num === 'FRESHMAN') {
                user.stu_num = '未绑定';
                user.name = '未绑定学号';
            }
            this.eventBus.$emit('userLogin', user);
        }
    }
};
</script>

<style scoped>
.logo {
    max-width: 100%;
    display: block;
    margin: auto;
    pointer-events: none;
}
.info {
    border-radius: 4px;
    background: rgba(41, 128, 185, 0.5);
    color: #fff;
    text-align: center;
    padding: 8px;
}
form {
    width: 90%;
    height: 180px;
    margin: auto;
    margin-top: 20px;
}
.btn-login {
    margin: auto;
    display: block;
}
.qrcode {
    display: block;
    height: 180px;
    margin: auto;
    margin-top: 20px;
}
</style>
