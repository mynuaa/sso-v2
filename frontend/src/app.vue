<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<style>
@import '~vue-material/dist/vue-material.css';

:-webkit-autofill {
    box-shadow: inset 0 0 1000px #fff;
}
html,
body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-size: 13px;
}
body {
    background: url(assets/img/bg.png) no-repeat top center;
    background-size: cover;
}
body, input, button {
    outline: none;
    font-family: 'PingFang SC', 'Helvetica Neue', 'Hiragino Sans GB', 'Segoe UI', 'Microsoft YaHei UI', 微软雅黑, sans-serif;
}
div {
    box-sizing: border-box;
}
#app,
.layout {
    height: 100%;
}
.iziToast-capsule {
    text-align: center;
}
.iziToast {
    min-height: 0;
    font-family: inherit;
    padding: 5px 40px 5px 20px;
}
.iziToast-body {
    margin: 0 !important;
    padding-left: 0 !important;
}
.rs-card {
    padding: 20px;
    border-radius: 4px;
    background: #fff;
    box-shadow: 1px 3px 5px rgba(0, 0, 0, 0.2);
}
.fullscreen-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.wrapper {
    width: 95%;
    margin: auto;
}
.clear {
    clear: both;
}
@media screen and (min-width: 600px) {
    html,
    body {
        font-size: 14px;
    }
    .wrapper {
        width: 80%;
        max-width: 640px;
    }
}
</style>
