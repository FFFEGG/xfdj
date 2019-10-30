<?php


namespace App\Helps\api;


use Illuminate\Support\Facades\Redis;

class redisLbs
{
    /** @var Redis */
    private $redis;
    public function __construct($config = array())
    {
        $host = isset($config['host']) ? $config['host'] : 'laradock_redis';
        $port = isset($config['port']) ? $config['port'] : '6379';
//        dd($host);
        $redis = new \Redis();
//        dd($redis);die;
        $redis->connect($host, $port);
        if (env('APP_ENV') != 'local'){
            $redis->auth('myRedis');
            $redis ->set( "root" , "myRedis");
        }
        $this->setRedis($redis);
    }
    public function getRedis()
    {
        return $this->redis;
    }
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    //添加点
    public function geoAdd($uin, $lon, $lat)
    {
        $redis = $this->getRedis();
        $redis->geoAdd('moments', $lon, $lat, $uin);
        return true;
    }

    //获取点
    public function geoNearFind($longitude , $latitude , $maxDistance = 0, $unit = 'km')
    {
        $redis = $this->getRedis();
        $options = ['WITHDIST','ASC','WITHCOORD']; //显示距离
        $list = $redis->geoRadius('moments', $longitude, $latitude , $maxDistance, $unit, $options);
        return $list;
    }
}
