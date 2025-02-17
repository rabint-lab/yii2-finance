<?php

namespace rabint\finance\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "finance_wallet_connection".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property integer $expire_date
 * @property integer $created_at
 *
 * @property User $user
 */
class WalletConnection extends \common\models\base\ActiveRecord     /* \yii\db\ActiveRecord */
{
    const SCENARIO_CUSTOM = 'custom';
    /* statuses */
    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_PUBLISH = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'finance_wallet_connection';
    }


    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => time(),
            ],
        ];
    }
//
//    public function scenarios()
//    {
//        $scenarios = parent::scenarios();
//// $scenarios[self::SCENARIO_CUSTOM] = ['status'];
//        return $scenarios;
//    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'provider', 'created_at'], 'required'],
            [['user_id', 'expire_date', 'created_at'], 'integer'],
            [['provider'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rabint', 'شناسه'),
            'user_id' => Yii::t('rabint', 'کاربر'),
            'provider' => Yii::t('rabint', 'ارايه دهنده'),
            'expire_date' => Yii::t('rabint', 'تاریخ انقضاء'),
            'created_at' => Yii::t('rabint', 'تاریخ اتصال'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
//if(!empty($this->publish_at)){
//    $this->publish_at = \rabint\helpers\locality::anyToGregorian($this->publish_at);
//    $this->publish_at = strtotime($this->publish_at);// if timestamp needs
//}
        return parent::beforeSave($insert);
    }


    /**
     * @return \common\models\base\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
