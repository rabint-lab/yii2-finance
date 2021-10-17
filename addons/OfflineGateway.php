<?php

namespace rabint\finance\addons;

use rabint\finance\models\FinanceOfflinePay;
use Yii;

class OfflineGateway extends GatewayAbstract
{

//    function afterPay() {
//        
//    }

    public function __construct()
    {
        $this->code = 1;
        $this->slug = 'Offline';
        $this->title = Yii::t('rabint', 'کارت به کارت');
        $this->config = [
        ];
        $this->gatewaySuccessStatus = 0;
        $this->messages = [
            0 => Yii::t('rabint', 'پرداخت موفقیت آمیز بود'),
            1 => Yii::t('rabint', 'پرداخت نا موفق بود'),
            2 => Yii::t('rabint', 'درحال انتقال به بانک'),
        ];
    }

    public function startPay($orderId, $amount, $callbackUrl)
    {
        list($ResCode, $RefId) = explode(',', '0,123456');

        $this->setGatewayData($orderId, ['ResCode' => $ResCode, 'RefId' => $RefId]);
        $model = new FinanceOfflinePay();
        $model->setScenario(FinanceOfflinePay::SCENARIO_PREREGISTER);
        $model->callback = $callbackUrl;
        $model->user_id = Yii::$app->user->id;
        $model->transaction_id = $orderId;
        $model->amount = strval($amount);
        if(!$model->save()){
         var_dump($model->errors);exit();
        }
        redirect(['/finance/default/offline-pay','id'=>$model->id]);
    }

    public function payStatus($orderId, $gatewayData = [])
    {
        $return = [
            'status' => $this->gatewaySuccessStatus,
            'gateway_reciept' => time(),
            'gateway_meta' => md5(time())
        ];
        return $return;
    }

    public function verifyPay($orderId, $gatewayMeta = [])
    {
        return $this->gatewaySuccessStatus;
    }

    public function rollBack($orderId, $gatewayMeta = [])
    {

    }

}
