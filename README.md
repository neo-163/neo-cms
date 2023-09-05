## Neo CMS集成技术

Redis <br/>
RabbitMQ <br/>
Workerman <br/>
OSS <br/>

## 分布式部署

Neo CMS高并发场景下，可以进行分布式部署：<br/>
服务器1 - web端页面站点 + CDN<br/>
服务器2 - cms端页面站点 + CDN<br/>
服务器3 - 后端主系统 + OSS/COS<br/>
服务器4 - RabbitMQ消息队列<br/>
MySQL和Redis使用云数据库<br/>

<br/>
后续可能推出更多中间件功能满足扩展项目需求

## Laravel .env文件配置

```shell
cp .env.example .env
```

`.env`文件主要新增了以下配置：<br/>


```
# 后台token key
TOKENKEY="随便写"

# 阿里云OSS
OSS_MEDIA_URL="XXX"
OSS_ACCESS_KEY="XXX"
OSS_SECRET_KEY="XXX"
OSS_ENDPOINT="XXX"
OSS_BUCKET="XXX"
OSS_IS_CNAME=true

# 默认队列驱动名称
#QUEUE_DRIVER=database
QUEUE_DRIVER=rabbitmq

RABBITMQ_HOST=XXX.XXX.XXX.XXX
# RabbitMQ的端口
RABBITMQ_PORT=5672
# 通过15672创建的RabbitMQ虚拟主机名，默认是'/'
RABBITMQ_VHOST=test
# RabbitMQ的登录名称
RABBITMQ_USER=username
# RabbitMQ的密码
RABBITMQ_PASSWORD=password
# 通过15672创建的RabbitMQ队列名称
RABBITMQ_QUEUE=test_task
```

## vue-element-admin安装包

由于vue-element-admin的安装包node_modules下载时容易出现问题，所以开放了一个测试可行的[node_modules安装包](https://github.com/neo-163/node_modules-vue-element-admin)。

## 后台页面demo
![](https://github.com/neo-163/neo-cms/blob/main/project-admin/public/admin/images/20220914183929.png?raw=true)
s

## License

Neo，协议 [MIT license](https://opensource.org/licenses/MIT).

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
