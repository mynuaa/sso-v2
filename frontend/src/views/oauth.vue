<template>
    <div class="oauth">
        <div class="wrapper">
            <div class="scroll">
                <div class="rs-card">
                    <img src="../assets/img/logo.png" class="logo">
                    <div class="info">授权“{{appinfo.appname}}”访问你的纸飞机账号</div>
                    <div class="user-info" v-if="user.id">
                        <img :src="`/ucenter/avatar.php?uid=${user.id}&size=small`">
                        <h1 class="em">{{appinfo.appname}}</h1>
                        <div>希望访问你的 <b class="em">{{user.username}}</b> 账户</div>
                    </div>
                    <div class="permissions">
                        <md-icon v-if="hasPrivateAccess(appinfo.permissions)">vpn_lock</md-icon>
                        <md-icon v-else>public</md-icon>
                        <h1 v-if="hasPrivateAccess(appinfo.permissions)">需要访问敏感信息</h1>
                        <h1 v-else>有限的公开信息</h1>
                        <ul>
                            <li
                                v-for="(item, index) in permissions"
                                :key="index"
                                v-if="hasPermission(appinfo.permissions, index)">
                                <span>{{item}}</span>
                                <md-icon class="private-data" v-if="index >= 10" title="这是敏感信息">lock</md-icon>
                            </li>
                        </ul>
                    </div>
                    <div class="authorize">
                        <md-button class="md-raised md-primary" @click="authorize">授权</md-button>
                        <div class="redirect-tip">授权后将跳至：<div class="em">{{getRedirectFullURL()}}</div></div>
                    </div>
                    <div class="comments" v-if="appinfo.offical !== null">
                        <span v-if="appinfo.offical"><md-icon>check</md-icon>此为纸飞机官方应用</span>
                        <span v-else><md-icon>clear</md-icon>此非纸飞机官方应用</span>
                        <span><md-icon>query_builder</md-icon>于 {{appinfo.created}} 创建</span>
                        <span><md-icon>group</md-icon>已有 {{appinfo.authorizers}} 人授权该应用</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import navigation from 'utils/navigation';
import resource from 'utils/resource';

export default {
    data() {
        return {
            appinfo: {
                name: '...',
                offical: null
            },
            user: {},
            permissions: [
                '用户ID、头像',
                '用户昵称',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '学号/工号',
                '注册邮箱',
                '绑定的微信信息'
            ]
        };
    },
    methods: {
        hasPermission(permission, index) {
            return (permission & (1 << index)) > 0;
        },
        hasPrivateAccess(permission) {
            return (permission >> 10) > 0;
        },
        async authorize() {
            const code = await resource.get(`/api/oauth/authorize?appid=${this.appinfo.appid}`);
            const url = this.$route.query.redirect_uri;
            location.href = url + (/\?/.test(url) ? '&' : '?') + 'code=' + code;
        },
        getRedirectFullURL() {
            return location.protocol + '//' + location.host + this.$route.query.redirect_uri;
        }
    },
    async mounted() {
        if (!this.$route.query.redirect_uri) {
            console.error('"redirect_uri" param not found!');
        }
        // TODO: get app info
        const appinfo = await resource.post('/api/oauth/appinfo', {
            appid: this.$route.params.appid
        });
        if (!appinfo) {
            console.error('no such app!');
        }
        appinfo.permissions = parseInt(appinfo.permissions);
        this.appinfo = appinfo;
        this.user = await resource.get('/api/user/current');
        if (this.user) {
            if (this.user.stu_num === 'FRESHMAN') {
                this.user.stu_num = '未绑定';
                this.user.name = '未绑定学号';
            }
        } else {
            navigation.addNext(`/oauth/${this.$route.params.appid}?redirect_uri=${this.$route.query.redirect_uri}`);
            navigation.go('/login?oauth=1');
        }
    }
};
</script>

<style scoped>
.oauth {
    position: relative;
    top: 20px;
}
.logo {
    max-width: 100%;
    display: block;
    margin: auto auto 20px;
    pointer-events: none;
}
.info {
    border-radius: 4px;
    background: rgba(41, 128, 185, 0.5);
    color: #fff;
    text-align: center;
    padding: 8px;
}
h1 {
    font-size: 16px;
    margin: 20px 0 10px;
    font-weight: normal;
}
.user-info,
.permissions {
    margin: 20px 0;
    background: #f2f4f6;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-size: 12px;
}
.user-info {
    color: #506070;
}
.permissions {
    overflow: hidden;
}
.user-info h1,
.permissions h1 {
    margin: 0;
    font-weight: bold;
    margin-bottom: 5px;
}
.permissions .md-icon {
    float: left;
    width: 40px;
    height: 40px;
    font-size: 40px;
    color: #506070;
    margin-right: 10px;
    text-align: center;
}
.permissions ul {
    float: left;
    list-style: none;
    margin: 0;
    padding-left: 0;
    color: #506070;
}
.permissions li {
    margin: 0;
    line-height: 20px;
    overflow: hidden;
}
.permissions li > * {
    float: left;
}
.permissions .private-data {
    font-size: 14px;
    width: 20px;
    height: 20px;
    min-width: 20px;
    min-height: 20px;
    float: none;
}
.user-info img {
    width: 40px;
    height: 40px;
    float: left;
    border-radius: 50%;
    margin-right: 10px;
}
.user-info div {
    margin: 0;
    height: 20px;
    line-height: 20px;
}
.em {
    color: #000;
    font-weight: bold;
}
.authorize {
    text-align: center;
}
.authorize button {
    min-width: 180px;
}
.redirect-tip {
    margin-top: 10px;
    font-size: 12px;
    color: #506070;
}
.comments {
    color: #506070;
    font-size: 0.9rem;
    text-align: center;
    margin: 20px 0 5px;
}
.comments span {
    display: inline-block;
    margin: 0 8px;
}
.comments i {
    font-size: 14px;
    min-width: 20px;
    width: 20px;
}
</style>
