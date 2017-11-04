# SSO V2 前端项目

## 安装、运行、编译

```bash
# 安装
yarn install
# 运行
yarn run dev
# 编译
yarn run build
```

## Nginx 设置

参考后端的配置。

## Host 设置

目前调试过程中 `/api` 和 `/ucenter` 默认的 Proxy 是指向 `test.my.nuaa.edu.cn` 的，若想修改（例如修改为 `localhost`），可设置环境变量后运行：

```bash
HOST=localhost npm run dev
```

## Alias 设置

由于每次 import 的时候写明相对路径太麻烦，因此加了几个 Alias：

```
components => src/components
router => src/router
views => src/views
utils => src/utils
```

可以直接 import：

```javascript
import resource from 'utils/resource';
```

## 编译设置

目前打算将此项目放到服务器的 `/sso-v2` 路径下，因此 `npm run build` 生成的 `index.html` 中，文件路径均以 `/sso-v2` 开头。

## ESlint 设置

请保持开启 ESlint。
