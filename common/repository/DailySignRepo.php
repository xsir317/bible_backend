<?php

namespace common\repository;

use common\components\DistributedLock;
use common\models\DailyCheckIn;
use yii\db\Transaction;

class DailySignRepo extends BaseRepo
{
    /**
     * 签到
     * @param $uid
     * @return array|bool
     */
    public static function doCheckIn($uid){
        if(!DistributedLock::getLock('check_in:'.$uid , 5)){
            return self::_err('频繁操作，请稍后重试');
        }
        $transaction = DailyCheckIn::getDb()->beginTransaction(Transaction::REPEATABLE_READ);
        try{
            $record = DailyCheckIn::find()
                ->where(['uid' => $uid])
                ->orderBy('id desc')
                ->limit(1)
                ->one();
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d' , time() - 86400);
            if($record && $record->last_check_date == $today){
                return self::_err('今日已经签到过了');
            }
            if(!$record || $record->last_check_date != $yesterday){
                $record = new DailyCheckIn();
                $record->uid = $uid;
                $record->start_date = date('Y-m-d');
                $record->days_count = 0;
                $record->created_time = date('Y-m-d H:i:s');
            }
            $record->last_check_date = date('Y-m-d');
            $record->updated_time = date('Y-m-d H:i:s');
            $record->days_count ++;
            $record->save();
            $transaction->commit();
            return self::reward($uid , $record->days_count , $today);
        }catch (\Exception $e){
            $transaction->rollBack();
            return self::_err('Error: '.$e->getMessage());
        }
    }

    /**
     * 补签
     * @param $uid
     * @return array|bool
     */
    public static function doFillUpCheckIn($uid){
        if(!DistributedLock::getLock('check_in:'.$uid , 5)){
            return self::_err('频繁操作，请稍后重试');
        }

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d' , time() - 86400);
        $transaction = DailyCheckIn::getDb()->beginTransaction(Transaction::REPEATABLE_READ);
        try{
            $records = DailyCheckIn::find()
                ->where(['uid' => $uid])
                ->orderBy('id desc')
                ->limit(2)
                ->all();

            $current = null;
            $previous = null;

            //如果有签到记录，而且签到记录是当前有效的（昨天或者今天），那么就设置为当前签到记录
            //否则就是上一条（断掉的）签到记录
            if(isset($records[0])){
                if($records[0]['last_check_date'] == $today || $records[0]['last_check_date'] == $yesterday){
                    $current = $records[0];
                    if(isset($records[1])){
                        $previous = $records[1];
                    }
                }else{
                    $previous = $records[0];
                }
            }

            //如果有当前记录，那么补签按当前的来计算
            if(!$current){
                $current = new DailyCheckIn();
                $current->uid = $uid;
                $current->start_date = $today;
                $current->days_count = 0;
                $current->created_time = date('Y-m-d H:i:s');
                $current->last_check_date = $yesterday;
            }
            //把current 的start_date 往前推一天，这是实际签到的那一天
            $check_in_date = date('Y-m-d' , strtotime($current->start_date) - 86400);
            $current->start_date = $check_in_date;
            $current->days_count ++;
            //如果跟上一条签到能连上，那么就加上。
            if($previous){ //这里就算一下补签之后的效果
                if($check_in_date == date('Y-m-d' , strtotime($previous->last_check_date) + 86400)){
                    $current->start_date = $previous->start_date;
                    $current->days_count += $previous->days_count;
                    $previous->delete();
                }
            }

            $current->save();
            $transaction->commit();
            return self::reward($uid , $current->days_count , $check_in_date);
        }catch (\Exception $e){
            $transaction->rollBack();
            return self::_err('Error: '.$e->getMessage());
        }
    }

    /**
     * @param $uid
     * @return array
     */
    public static function status($uid){
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d' , time() - 86400);
        $records = DailyCheckIn::find()
            ->where(['uid' => $uid])
            ->orderBy('id desc')
            ->limit(2)
            ->all();

        $current = null;
        $previous = null;

        $return = [
            'today_checked' => false, // 今天是否已经签到了
            'check_in_days' => 0,
            'fill_up_check_in_date' => $yesterday,
            'fill_up_check_in_count' => 1,
        ];

        //如果有签到记录，而且签到记录是当前有效的（昨天或者今天），那么就设置为当前签到记录
        //否则就是上一条（断掉的）签到记录
        if(isset($records[0])){
            if($records[0]['last_check_date'] == $today){
                $return['today_checked'] = true;
            }
            if($records[0]['last_check_date'] == $today || $records[0]['last_check_date'] == $yesterday){
                $return['check_in_days'] = $records[0]['days_count'];
                $current = $records[0];
                if(isset($records[1])){
                    $previous = $records[1];
                }
            }else{
                $previous = $records[0];
            }
        }

        //如果有当前记录，那么补签按当前的来计算
        if($current){
            $return['fill_up_check_in_date'] = date('Y-m-d' , strtotime($current['start_date'])-86400);
            $return['fill_up_check_in_count'] = $current['days_count'] +1;
        }
        //如果跟上一条签到能连上，那么就加上。
        if($previous){ //这里就算一下补签之后的效果
            if($return['fill_up_check_in_date'] == date('Y-m-d' , strtotime($previous['last_check_date']) + 86400)){
                $return['fill_up_check_in_count'] += $previous['days_count'];
            }
        }

        return $return;
    }

    private static function reward($uid, $days , $date){
        return null;
    }
}