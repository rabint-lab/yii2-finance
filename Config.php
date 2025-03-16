<?php

namespace rabint\finance;

use common\models\User;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This is the model class for table "finance_transactions".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $transactioner
 * @property integer $amount
 * @property integer $status
 * @property integer $gateway
 * @property string $gateway_reciept
 * @property string $gateway_meta
 * @property string $transactioner_ip
 * @property string $internal_reciept
 * @property string $settle_callback_function
 * @property string $token
 * @property string $return_url
 * @property string $additional_rows
 * @property integer $creator_id
 * @property string $internal_meta
 * @property string $metadata
 */
class Config extends \yii\db\ActiveRecord
{

    const SCENARIO_ADMIN_INVOICE = 'admin_invoice';

    const TAX_PERCENT = 0;

    //const GATEWAY_SELECT_METHOD = 'auto';
    const GATEWAY_SELECT_METHOD = 'manual';
    const ALLOW_USE_WALLET = true;
    const ALLOW_NEGATIVE_WALLET = false;
    const AUTO_PAY_BY_WALLET = FALSE;
    const SHOW_FACTURE_PAGE = true;

    const TRANSACTION_PENDING = 0;
    const TRANSACTION_INPROCESS = 1;
    const TRANSACTION_PAID = 2;
    const TRANSACTION_COMPLETED = 3;
    const TRANSACTION_SKIPPED = 4;


    var $displayTitle = 'مدیریت مالی';
//    public static $paymentGateways = [
//        //1 => ['title' => 'درگاه زرین پال', 'class' => '\rabint\finance\addons\ZarrinpalGateway'],
//        // 2 => ['title'=>'درگاه تست','class' => '\rabint\finance\addons\TestGateway'],
//        4 => ['title'=>'درگاه پارسیان','class' => '\rabint\finance\addons\ParsianGateway'],
//        // 3 => ['title'=>'درگاه ملت','class' => '\rabint\finance\addons\MellatGateway']
//    ];
    public static $credit = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'finance_transactions';
    }

    public static function statuses()
    {
        return [
            self::TRANSACTION_PENDING => ['title' => \Yii::t('rabint', 'منتظر پرداخت'), 'class' => 'info'],
            self::TRANSACTION_INPROCESS => ['title' => \Yii::t('rabint', 'درحال پرداخت'), 'class' => 'warning'],
            self::TRANSACTION_PAID => ['title' => \Yii::t('rabint', 'پرداخت شده'), 'class' => 'success'],
            self::TRANSACTION_COMPLETED => ['title' => \Yii::t('rabint', 'تکمیل شده'), 'class' => 'primary'],
            self::TRANSACTION_SKIPPED => ['title' => \Yii::t('rabint', 'خطا در پرداخت'), 'class' => 'danger'],
        ];
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
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

//    public function scenarios()
//    {
//        $scenarios = parent::scenarios();
//        $scenarios[self::SCENARIO_ADMIN_INVOICE] = ['status'];
//        return $scenarios;
//    }


    public static function defaultPaymentGatewayId()
    {
        return config('SERVICE.finance.defaultGatewayId', 1);
    }

    public static function getNotifyConfig()
    {
        return config('SERVICE.finance.notify', 0);
    }

    public static function paymentGateways()
    {
        //$gateways = static::$paymentGateways;
        $gateways = config('SERVICE.finance.gateways', false);
        if (empty($gateways)) {
            throw new InvalidConfigException('لطفا تنظیمات درگاه های بانکی را در بخش انوایرومنت وبسایت انجام دهید');
        }
        if (USER_CAN_DEBUG) {
            $gateways[] = ['title' => 'درگاه تست', 'class' => '\rabint\finance\addons\TestGateway'];
        }
        return $gateways;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'transactioner', 'amount', 'status', 'gateway', 'transactioner_ip', 'internal_reciept', 'token', 'return_url', 'additional_rows'], 'required'],
            [['created_at', 'transactioner', 'status', 'gateway', 'creator_id'], 'integer'],
            [['additional_rows', 'metadata', 'settle_callback_function', 'gateway_meta', 'internal_meta'], 'string'],
            [['gateway_reciept', 'transactioner_ip', 'internal_reciept', 'token', 'return_url'], 'string', 'max' => 255],
            [['metadata'], 'required', 'on' => self::SCENARIO_ADMIN_INVOICE],
            ['amount', 'integer', 'on' => self::SCENARIO_DEFAULT],
            ['amount', 'safe', 'on' => self::SCENARIO_ADMIN_INVOICE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rabint', 'شناسه'),
            'created_at' => Yii::t('rabint', 'زمان درخواست'),
            'transactioner' => Yii::t('rabint', 'کاربر'),
            'amount' => Yii::t('rabint', 'مبلغ'),
            'status' => Yii::t('rabint', 'وضعیت'),
            'gateway' => Yii::t('rabint', 'درگاه'),
            'gateway_reciept' => Yii::t('rabint', 'کد پیگیری درگاه'),
            'gateway_meta' => Yii::t('rabint', 'اطلاعات متای درگاه'),
            'transactioner_ip' => Yii::t('rabint', 'آی پی انجام دهنده'),
            'internal_reciept' => Yii::t('rabint', 'کد پیگیری داخلی'),
            'token' => Yii::t('rabint', 'توکن'),
            'return_url' => Yii::t('rabint', 'لینک بازگشت'),
            'additional_rows' => Yii::t('rabint', 'تراکنش های مرتبط'),
            'internal_meta' => Yii::t('rabint', 'متادیتای داخلی'),
            'creator_id' => Yii::t('rabint', 'سازنده'),
            'metadata' => Yii::t('rabint', 'شرح صورتحساب'),
        ];
    }


    /* =================================================================== */

    /**
     * @return \common\models\base\ActiveQuery
     */
    public function getTransactionerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'transactioner']);
    }

    /**
     * @return \common\models\base\ActiveQuery
     */
    public function getCreatorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }
}
