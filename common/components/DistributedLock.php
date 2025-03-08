<?php

namespace common\components;

class DistributedLock
{
    private $lock_str = '';
    private $expire_ts = 0;
    private function __construct($lock_stt,$expire_ts){
        $this->lock_str = $lock_stt;
        $this->expire_ts = $expire_ts;
    }
    /**
     * @param $lock
     * @param $hold_time
     * @return bool | DistributedLock
     * 基于redis 的锁
     */
    public static function getLock($lock,$hold_time=1){
        if(!is_string($lock) || empty($lock)){
            return false;
        }
        $hold_time = intval($hold_time);
        if(self::_redis()->setNx( self::_s($lock) ,1)){
            self::_redis()->expire( self::_s($lock),$hold_time);
            return new self($lock,time() + $hold_time);
        }
        return false;
    }

    /**
     * @param $hold_time
     * @return bool | DistributedLock
     */
    public function renewLock($hold_time){
        if(empty($this->lock_str)){
            return false;
        }
        $hold_time = intval($hold_time);
        $hold_time = max(1, min($hold_time,5));
        if(self::checkLock($this->lock_str)){
            self::_redis()->expire( self::_s($this->lock_str),$hold_time);
            return $this;
        }

        return false;
    }

    /**
     * @param $lock
     * 主动释放掉锁。 注意， 只有获得锁的才能释放锁
     */
    public function release(){
        if(time() <= $this->expire_ts){
            self::_redis()->del( self::_s($this->lock_str));
        }
    }

    /**
     * 尝试检查lock是否存在，但是不试图获取它
     * @param $lock
     * @return false
     */
    public static function checkLock($lock){
        if(!is_string($lock) || empty($lock)){
            return false;
        }
        return !empty(self::_redis()->get(self::_s($lock)));
    }

    private static function _redis(){
        return \Yii::$app->get('redis');
    }

    private static function _s($lock_name){
        return 'glblock:'.$lock_name;
    }
}