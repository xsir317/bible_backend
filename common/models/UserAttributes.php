<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_attributes".
 *
 * @property int $id
 * @property int $uid
 * @property int $attr_id
 * @property int $attr_value
 * @property int $is_latest
 * @property string $created_at
 */
class UserAttributes extends \yii\db\ActiveRecord
{
    // 定义属性常量
    const ATTR_BUJU = 1; // 布局
    const ATTR_JISUAN = 2; // 计算
    const ATTR_SHOUJIN = 3;//手筋
    const ATTR_SIHUO = 4;//死活
    const ATTR_SHIZHAN = 5;//实战
    const ATTR_GUANZI = 6;//官子
    const ATTR_DINGSHI = 7;//定式
    const ATTR_QILI = 8;//棋理
    const ATTR_XIGUAN = 9;//习惯
    const ATTR_XINTAI = 10;//心态

    // 属性的显示文本
    const ATTR_TEXT = [
        self::ATTR_BUJU => '布局',
        self::ATTR_JISUAN => '计算',
        self::ATTR_SHOUJIN =>'手筋',
        self::ATTR_SIHUO =>'死活',
        self::ATTR_SHIZHAN =>'实战',
        self::ATTR_GUANZI =>'官子',
        self::ATTR_DINGSHI =>'定式',
        self::ATTR_QILI =>'棋理',
        self::ATTR_XIGUAN =>'习惯',
        self::ATTR_XINTAI =>'心态',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'attr_id'], 'required'],
            [['uid', 'attr_id', 'attr_value', 'is_latest'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'attr_id' => 'Attr ID',
            'attr_value' => 'Attr Value',
            'is_latest' => 'Is Latest',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 维护用户属性的方法
     * @param int $uid 用户ID
     * @param array $attributes 属性数组 [attr_id => attr_value]
     * @return bool 是否成功
     */
    public static function updateUserAttr($uid, $attributes)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $current = self::getUserAttributes($uid);
            foreach ($current as $row){
                if(isset($attributes[$row['id']]) && $row['value'] == $attributes[$row['id']]){
                    unset($attributes[$row['id']]);
                }
            }
            foreach ($attributes as $attr_id => $attr_value) {
                // 将旧记录设为非最新
                self::updateAll(
                    ['is_latest' => 0],
                    ['uid' => $uid, 'attr_id' => $attr_id, 'is_latest' => 1]
                );

                // 插入新记录
                $model = new self();
                $model->uid = $uid;
                $model->attr_id = $attr_id;
                $model->attr_value = $attr_value;
                $model->is_latest = 1;
                $model->save();
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 获取指定用户的最新属性数据
     * @param int $uid 用户ID
     * @return array 属性数据数组
     */
    public static function getUserAttributes($uid)
    {
        // 查询最新的属性记录
        $attributes = self::find()
            ->select(['attr_id', 'attr_value'])
            ->where(['uid' => $uid, 'is_latest' => 1])
            ->asArray()
            ->indexBy('attr_id')
            ->all();

        // 格式化为所需结构
        $return = [];
        foreach (self::ATTR_TEXT as $k => $txt){
            $return[] = [
                'id' => $k,
                'txt' => $txt,
                'value' => isset($attributes[$k]) ? intval($attributes[$k]['attr_value']) : 0
            ];
        }

        return $return;
    }
}
