<?php

namespace rabint\finance;

use Yii;

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
 * @property string $token
 * @property string $return_url
 * @property string $additional_rows
 * @property string $metadata
 */
class Config extends \yii\db\ActiveRecord
{

    const TAX_PERCENT = 0;

    //const GATEWAY_SELECT_METHOD = 'auto';
    const GATEWAY_SELECT_METHOD = 'manual';
    const ALLOW_USE_WALLET = true;
    const AUTO_PAY_BY_WALLET = FALSE;
    const SHOW_FACTURE_PAGE = true;

    const TRANSACTION_PENDING = 0;
    const TRANSACTION_INPROCESS = 1;
    const TRANSACTION_PAID = 2;
    const TRANSACTION_COMPLETED = 3;
    const TRANSACTION_SKIPPED = 4;


    var $displayTitle = 'مدیریت مالی';
    public static $defaultPaymentGatewayId = 4;
    public static $paymentGateways = [
        //1 => ['title' => 'درگاه زرین پال', 'class' => '\rabint\finance\addons\ZarrinpalGateway'],
        // 2 => ['title'=>'درگاه تست','class' => '\rabint\finance\addons\TestGateway'],
        4 => ['title'=>'درگاه پارسیان','class' => '\rabint\finance\addons\ParsianGateway'],
        // 3 => ['title'=>'درگاه ملت','class' => '\rabint\finance\addons\MellatGateway']
    ];
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

    public static function paymentGateways()
    {
        $paymentGateways = static::$paymentGateways;
        if (USER_CAN_DEBUG) {
            //$paymentGateways[2] = ['title' => 'درگاه تست', 'class' => '\rabint\finance\addons\TestGateway'];
        }
        return $paymentGateways;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'transactioner', 'amount', 'status', 'gateway', 'transactioner_ip', 'internal_reciept', 'token', 'return_url', 'additional_rows'], 'required'],
            [['created_at', 'transactioner', 'amount', 'status', 'gateway'], 'integer'],
            [['additional_rows', 'metadata','settle_callback_function','gateway_meta'], 'string'],
            [['gateway_reciept', 'transactioner_ip', 'internal_reciept', 'token', 'return_url'], 'string', 'max' => 255]
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
            'transactioner' => Yii::t('rabint', 'انجام دهنده'),
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
            'metadata' => Yii::t('rabint', 'اطلاعات متا'),
        ];
    }


    /* =================================================================== */
}
