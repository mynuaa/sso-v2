<template>
    <div class="rs-card fullscreen-center wrapper">
        <img src="../assets/img/logo.png" class="logo">
        <md-tabs md-fixed class="md-transparent" v-if="$route.params.type == 'nuaa'">
            <md-tab id="nuaa" md-label="激活">
                <div class="info">输入你的教务处/研究生院/教师账号和密码来激活该账号</div>
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
            <md-tab id="cancel" md-label="放弃">
                <div class="info">放弃当前的操作，你的数据不会被记录</div>
                <br>
                <md-button class="md-raised md-primary btn-login">放弃</md-button>
            </md-tab>
        </md-tabs>
        <md-tabs md-fixed class="md-transparent" v-if="$route.params.type == 'discuz'">
            <md-tab id="bind" md-label="绑定">
                <div class="info">输入你的纸飞机账号和密码来绑定该学号</div>
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
                    <md-button type="submit" class="md-raised md-primary btn-login">绑定</md-button>
                </form>
            </md-tab>
            <md-tab id="register" md-label="注册">
                <div class="info">使用该学号注册一个新的纸飞机账号</div>
                <form novalidate @submit.stop.prevent="() => submit('discuz')">
                    <md-input-container>
                        <label>用户名</label>
                        <md-input v-model="form.discuz.username"></md-input>
                    </md-input-container>
                    <md-input-container>
                        <label>邮箱</label>
                        <md-input type="email" v-model="form.discuz.email"></md-input>
                    </md-input-container>
                    <md-button type="submit" class="md-raised md-primary btn-login">注册</md-button>
                </form>
            </md-tab>
            <md-tab id="cancel" md-label="放弃">
                <div class="info">放弃当前的操作，你的数据不会被记录</div>
                <br>
                <md-button class="md-raised md-primary btn-login">放弃</md-button>
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
                    email: ''
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
            this.eventBus.$emit('userLogin', user);
        }
    },
    mounted() {
        iziToast.info({
            title: 'Info',
            message: '你的账号尚未激活，请按提示操作'
        });
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
    width: 80%;
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
