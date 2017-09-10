# 纸飞机同步登录系统后端

## 配置步骤

1. 修改 config.php（后续在 docker 中会省略）
2. 导入 nginx 配置（后续在 docker 中会省略）：

```nginx
location /sso-v2 {
    alias $my_root/sso-v2/frontend/dist/;
    try_files $uri $uri/ /sso-v2/index.html;
    location ~ /sso-v2/docs {
        root $my_root/sso-v2/backend/public;
        try_files /docs.php =404;
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $my_root/sso-v2/backend/public/docs.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_NAME /sso-v2/api/;
    }
    location ~ /sso-v2/api {
        rewrite ^/sso-v2/api/([^/]+?)/([^/]+?)$ /sso-v2/api/?s=$1.$2 last;
        rewrite ^/sso-v2/api/([^/]+?)$ /sso-v2/api/?s=$1 last;
        root $my_root/sso-v2/backend/public;
        try_files /index.php =404;
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $my_root/sso-v2/backend/public/index.php;
        include fastcgi_params;
    }
}
```

## 在线接口调试

使用浏览器访问 `http://nginx/sso-v2/docs` 即可。
