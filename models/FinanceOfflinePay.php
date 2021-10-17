<?php

namespace rabint\finance\models;

use Yii;
use common\models\User;

/**
* This is the model class for table "finance_offline_pay".
*
    * @property integer $id
    * @property integer $user_id
    * @property integer $transaction_id
    * @property string $callback
    * @property string $status
    * @property string $amount
    * @property string $image
    * @property integer $date_pay
    * @property string $tracking_cod
    * @property string $description
    * @property integer $created_at
    * @property integer $updated_at
*/
class FinanceOfflinePay extends \common\models\base\ActiveRecord     /* \yii\db\ActiveRecord */
{
const SCENARIO_PREREGISTER = 'preregister';
/* statuses */
const STATUS_DRAFT = 0;
const STATUS_NOT_ACCEPTED = 1;
const STATUS_ACCEPTED = 2;

/**
* @inheritdoc
*/
public static function tableName()
{
return 'finance_offline_pay';
}


public function behaviors() {
return [
[
'class' => \yii\behaviors\TimestampBehavior::class,
'createdAtAttribute' => 'created_at',
'updatedAtAttribute' => 'updated_at',
'value' => time(),
],
//[
//'class' => \yii\behaviors\BlameableBehavior::class,
//'createdByAttribute' => 'created_by',
//'updatedByAttribute' => 'updated_by',
//],
// [
//     'class' =>\rabint\behaviors\SoftDeleteBehavior::class,
//     'attribute' => 'deleted_at',
//     'attribute' => 'deleted_by',
// ],
/*[
'class' => \rabint\behaviors\Slug::class,
'sourceAttributeName' => 'title', // If you want to make a slug from another attribute, set it here
'slugAttributeName' => 'slug', // Name of the attribute containing a slug
],*/
];
}

public function scenarios() {
$scenarios = parent::scenarios();
 $scenarios[self::SCENARIO_PREREGISTER] = ['id','user_id','status','transaction_id','callback','amount','created_at','updated_at'];
return $scenarios;
}


/* ====================================================================== */

public static function statuses() {
return [
static::STATUS_DRAFT => ['title' => \Yii::t('rabint', 'draft')],
static::STATUS_NOT_ACCEPTED => ['title' => \Yii::t('rabint', 'pending')],
static::STATUS_ACCEPTED => ['title' => \Yii::t('rabint', 'publish')],
];
}

/* ====================================================================== */

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['user_id', 'transaction_id', 'created_at', 'updated_at' ,'status'], 'integer'],
            [['callback', 'description'], 'string'],
            [['date_pay', 'tracking_cod'], 'required'],
            [['amount'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 255],
            [['tracking_cod'], 'string', 'max' => 45],
            [['date_pay'], 'safe'],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('rabint', 'شناسه'),
    'user_id' => Yii::t('rabint', 'User ID'),
    'transaction_id' => Yii::t('rabint', 'Transaction ID'),
    'callback' => Yii::t('rabint', 'Callback'),
    'amount' => Yii::t('rabint', 'مبلغ'),
    'status' => Yii::t('rabint', 'وضعیت'),
    'image' => Yii::t('rabint', 'تصویر فیش'),
    'date_pay' => Yii::t('rabint', 'تاریخ پرداخت'),
    'tracking_cod' => Yii::t('rabint', 'شماره پیگیری'),
    'description' => Yii::t('rabint', 'توضیحات'),
    'created_at' => Yii::t('rabint', 'تاریخ ایجاد'),
    'updated_at' => Yii::t('rabint', 'تاریخ تایید'),
];
}

/**
* @inheritdoc
*/
public function beforeSave($insert)
{
    if(!empty($this->date_pay)){
        $this->date_pay = \rabint\helpers\locality::anyToTimeStamp($this->date_pay);
    }
//if(!empty($this->publish_at)){
//    $this->publish_at = \rabint\helpers\locality::anyToGregorian($this->publish_at);
//    $this->publish_at = strtotime($this->publish_at);// if timestamp needs
//}
return parent::beforeSave($insert);
}
public function afterFind()
{
    $this->date_pay = \rabint\helpers\locality::anyToJalali($this->date_pay,'Y-m-d');
    parent::afterFind(); // TODO: Change the autogenerated stub
}


    /**
    * @inheritdoc
    * @return \rabint\models\query\PublishQuery the active query used by this AR class.
    */
    //public static function find()
    //{
    //    $publishQuery = new \rabint\models\query\PublishQuery(get_called_class());
    //    $publishQuery->statusField="status";
    //    $publishQuery->activeStatusValue=self::STATUS_PUBLISH;
    //    $publishQuery->ownerField="creator_id";
    //    $publishQuery->showNotActiveToOwners=true;
    //    return $publishQuery;
    //}

}
