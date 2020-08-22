<?php

namespace rabint\finance\addons;

use Yii;

class TestGateway extends GatewayAbstract
{

//    function afterPay() {
//        
//    }

    public function __construct()
    {
        $this->code = 1;
        $this->slug = 'test';
        $this->title = Yii::t('rabint', 'درگاه تست');
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

        //echo $this->messages[2];
//        header('location:'.$callbackUrl);
        echo <<<EOT

<div style="font-family: Tahoma; margin: 20px auto; text-align: center;direction: rtl">
<h3>
تستر درگاه بانکی
</h3>
<p>
کاربر گرامی !
</p>
<p>
صفحه ای که مشاهده می فرمایید ، صرفا جهت تست روند پرداخت می باشد و دارای هیچ گونه اعتبار دیگری نمی باشد.
</p>
<p>
<br/>
</p>
<p>
لطفا برای ادامه روند تست دکمه زیر را کلیک کنید 
</p>
<p>
<a href="{$callbackUrl}">ادامه پرداخت</a>
</p>
</div>
EOT;
        die('');
        //exit('<META http-equiv="refresh" content="0;URL=' . $callbackUrl . '">');
        //return 2;
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
