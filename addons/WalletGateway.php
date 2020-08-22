<?php

namespace rabint\finance\addons;

use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceWallet;
use rabint\helpers\user;
use Yii;

class WalletGateway extends GatewayAbstract
{

//    function afterPay() {
//        
//    }

    public function __construct()
    {
        $this->code = 1;
        $this->slug = 'wallet';
        $this->title = Yii::t('rabint', 'درگاه کیف پول');
        $this->config = [
        ];
        $this->gatewaySuccessStatus = 0;
        $this->messages = [
            0 => Yii::t('rabint', 'پرداخت موفقیت آمیز بود'),
            1 => Yii::t('rabint', 'پرداخت نا موفق بود'),
            2 => Yii::t('rabint', 'موجودی کافی نیست'),
        ];
    }

    public function startPay($orderId, $amount, $callbackUrl)
    {

        $cash = \rabint\finance\models\FinanceWallet::cash(\rabint\helpers\user::id());
        if (user::isGuest()) {
            return 1;
        }
        if ($amount > $cash) {
            return 2;
        }

        $model = FinanceTransactions::findOne($orderId);

        /* pay with wallet -------------------------------------- */
        $balancingRes = FinanceWallet::balancingPay(
            $model->transactioner,
            json_decode($model->additional_rows, 1),
            $model->transactioner,
            $model->transactioner_ip
        );

        if ($balancingRes) {
            $this->setGatewayData($orderId, ['ResCode' => 0, 'RefId' => $balancingRes]);
            header('location:' . $callbackUrl);
            exit('<META http-equiv="refresh" content="0;URL=' . $callbackUrl . '">');
        }
        return 1;
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
