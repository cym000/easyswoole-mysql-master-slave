<?php
/**
 * Created by PhpStorm.
 * User: CYM 601780673@qq.com
 * Date: 19-9-12
 * Time: 上午11:32
 */
require 'vendor/autoload.php';

// read
$config = new \EasySwoole\Mysqli\Config([
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'user'          => 'root',
    'password'      => '123456',
    'database'      => 'chat',
    'timeout'       => 5,
    'charset'       => 'utf8mb4',
]);

/* 原来easySwoole/mysqli
$client = new \EasySwoole\Mysqli\Client($config);
go(function ()use($client){
    //构建sql
    $client->queryBuilder()->get('c_link');
    //执行sql
    var_dump($client->execBuilder());
});
*/

\EasySwoole\Component\Pool\PoolManager::getInstance()->registerAnonymous('write' ,function() use($config){
    $obj = new \Swoole\Coroutine\MySQL();
    $obj->connect($config->toArray());
    return $obj;
});

$config = new \EasySwoole\Mysqli\Config([
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'user'          => 'root',
    'password'      => '123456',
    'database'      => 'chat',
    'timeout'       => 5,
    'charset'       => 'utf8mb4',
]);

$config1 = new \EasySwoole\Mysqli\Config([
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'user'          => 'root',
    'password'      => '123456',
    'database'      => 'chat',
    'timeout'       => 5,
    'charset'       => 'utf8mb4',
]);

\EasySwoole\Component\Pool\PoolManager::getInstance()->registerAnonymous('read1' ,function() use($config){
//    var_dump($config->toArray());
    $obj = new \Swoole\Coroutine\MySQL();
    $obj->connect($config->toArray());
    return $obj;
});

\EasySwoole\Component\Pool\PoolManager::getInstance()->registerAnonymous('read2' ,function() use($config1){
//    var_dump($config1->toArray());
    $obj = new \Swoole\Coroutine\MySQL();
    $obj->connect($config1->toArray());
    return $obj;
});

$client = new \MasterSalve\MasterSalveClient();
$client->setWriteList('write');
$client->setReadList(['read1', 'read2']);
// 广州艾特推推信息科技有限公司
go(function ()use($client){
    //构建sql
    $client->queryBuilder()->get('c_link');
    $client->execBuilder();
    $client->queryBuilder()->get('c_link');
    $client->execBuilder();

    $client->queryBuilder()->where('id', 1)->update('c_link', ['rel' => 1]);
    $client->execBuilder();

    $client->queryBuilder()->get('c_link');
    $client->execBuilder();
    $client->queryBuilder()->get('c_link');
    $client->execBuilder();

    \EasySwoole\Component\Pool\PoolManager::getInstance()->clearPool();
});
