## Neo CMS集成技術

Redis <br/>
RabbitMQ <br/>
Workerman <br/>
OSS <br/>

## 分佈式部署

Neo CMS高併發場景下，可以進行分佈式部署：<br/>
服務器1 - web端頁面網站+ CDN<br/>
服務器2 - cms端頁面網站+ CDN<br/>
服務器3 -後端主系統+ OSS/COS<br/>
服務器4 - RabbitMQ消息隊列<br/>
MySQL和Redis使用Cloud Database<br/>

<br/>
後續可能推出更多中介軟體功能滿足擴展項目需求

## Laravel .env文件配置

```shell
cp .env.example .env
```

`.env`文件主要新增了以下配置：<br/>


```
# 後臺token key
TOKENKEY="隨便寫"

# 阿里雲OSS
OSS_MEDIA_URL="XXX"
OSS_ACCESS_KEY="XXX"
OSS_SECRET_KEY="XXX"
OSS_ENDPOINT="XXX"
OSS_BUCKET="XXX"
OSS_IS_CNAME=true

# 默認隊列驅動名稱
#QUEUE_DRIVER=database
QUEUE_DRIVER=rabbitmq

RABBITMQ_HOST=XXX.XXX.XXX.XXX
# RabbitMQ的端口
RABBITMQ_PORT=5672
# 通過15672創建的RabbitMQ虛擬主機名稱，默認是'/'
RABBITMQ_VHOST=test
# RabbitMQ的登錄名稱
RABBITMQ_USER=username
# RabbitMQ的密碼
RABBITMQ_PASSWORD=password
# 通過15672創建的RabbitMQ隊列名稱
RABBITMQ_QUEUE=test_task
```

## vue-element-admin安裝包

由於vue-element-admin的安裝包node_modules下載時容易出現問題，所以開放了一個測試可行的[node_modules安裝包](https://github.com/neo-163/node_modules-vue-element-admin)。

## 後臺頁面demo
![](https://github.com/neo-163/neo-cms/blob/main/project-admin/public/admin/images/20220914183929.png?raw=true)
s

## 后台页面demo
![](https://github.com/neo-163/neo-cms/blob/main/project-admin/public/admin/images/20220914183929.png?raw=true)
s

## License

Neo，協議 [MIT license](https://opensource.org/licenses/MIT).

## Vue project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```
