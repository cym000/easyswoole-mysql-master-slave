# easyswoole-mysql-master-slave
在easySwoole/mysqli 基础上增加了多主多从、随机读写分离，其中也用到了easySwoole/pool

## 安装方式
```
git https://github.com/cym000/easyswoole-mysql-master-slave.git
composer update

```

## 使用方式
```
可参考根目录中 test.php
打印数据：
read1 SELECT  * FROM c_link
read2 SELECT  * FROM c_link
write UPDATE c_link SET `rel` = 1 WHERE  id = 1
read1 SELECT  * FROM c_link
read1 SELECT  * FROM c_link

```

## 只是提供了思路，用于实际生产环境还需要修改MasterSalveClient.php里面诸多方法
## 使用事务需自行增加代码，因为里面每次都用新的mysql pool连接
## 需自己实现Master及Salve服务宕机时连接切换及异常报错
