<?php
/**
 * Created by PhpStorm.
 * User: CYM 601780673@qq.com
 * Date: 19-9-12
 * Time: 上午11:52
 */
namespace MasterSalve;


use EasySwoole\Mysqli\Exception\Exception;
use Swoole\Coroutine\MySQL;
use EasySwoole\Mysqli\QueryBuilder;

class MasterSalveClient
{

    protected $list;
    protected $queryBuilder;

    function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    function queryBuilder():QueryBuilder
    {
        return $this->queryBuilder;
    }

    function reset()
    {
        $this->queryBuilder()->reset();
    }

    function execBuilder(float $timeout = null)
    {
        if($timeout === null){
            $timeout = 5;
        }
        $pool = $this->connect();
        $obj = $pool->getObj();
        $stmt = $obj->prepare($this->queryBuilder()->getLastPrepareQuery(),$timeout);
        $ret = null;
        if($stmt){
            $ret = $stmt->execute($this->queryBuilder()->getLastBindParams(),$timeout);
        }
        $pool->unsetObj($obj);
        if($obj->errno){
            throw new Exception($obj->error);
        }
        return $ret;
    }

    function rawQuery(string $query,float $timeout = null)
    {
        if($timeout === null){
            $timeout = $this->config->getTimeout();
        }
        $pool = $this->connect($query);
        $obj = $pool->getObj();
        $ret = $obj->query($query,$timeout);

        $pool->unsetObj($obj);
        if($obj->errno){
            throw new Exception($obj->error);
        }
        return $ret;
    }

    /*
    function mysqlClient():?MySQL
    {
        return $this->mysqlClient;
    }
    */

    private function connect(string $query = null)
    {
        $sql = !empty($query) ? $query : trim($this->queryBuilder()->getLastQuery());
        $arr = ['insert', 'update', 'delete'];
        $write = 0;
        foreach($arr as $value){
            if(stripos($sql, $value) !== false){
                $write = 1;
            }
        }
        if($write == 0){ // read
            if(is_array($this->list['read'])){
                $key = $this->list['read'][array_rand($this->list['read'])];
            }else{
                $key = $this->list['read'];
            }

            $pool = \EasySwoole\Component\Pool\PoolManager::getInstance()->getPool($key);
        }else{ // write
            if(is_array($this->list['write'])){
                $key = $this->list['write'][array_rand($this->list['write'])];
            }else{
                $key = $this->list['write'];
            }
            $pool = \EasySwoole\Component\Pool\PoolManager::getInstance()->getPool($key);
        }
        echo $key.' '. $sql .PHP_EOL;
        return $pool;
    }

    function setWriteList($data){
        $this->list['write'] = $data;
    }

    function setReadList($data){
        $this->list['read'] = $data;
    }

    /*
    function close():bool
    {
        return true;
    }

    function __destruct()
    {
        $this->close();
    }
    */

}
