<?php

namespace rabint\finance\addons;

use vendor\rabint\finance\addons\nusoap_client;
use Yii;

class ZarrinpalGateway extends GatewayAbstract {

    const amountFactor=0.1;

    public function __construct() {
        $this->code = 3;
        $this->slug = 'zarrinpal';
        $this->title = Yii::t('rabint', 'درگاه پرداخت زرین‌پال');
        $this->config = [
            'url' => 'https://www.zarinpal.com/pg/services/WebGate/wsdl',
            'merchantid' => '5a98f41f-1672-496e-8762-db9173f04ffb',
        ];
        $this->gatewaySuccessStatus = 100;
        $this->messages = [
            1001 => Yii::t('rabint', 'خطا در اتصال به درگاه بانک'),
            1002 => Yii::t('rabint', 'خطای نامشخص'),
            1003 => Yii::t('rabint', 'در حال انتقال به درگاه بانک'),
            1004 => Yii::t('rabint', 'پرداخت معتبر نمی باشد'),
            1005 => Yii::t('rabint', 'خطای امنیتی رخ داده است'),
            1006 => Yii::t('rabint', 'درگاه بانک در دسترس نیست'),
            1007 => Yii::t('rabint', 'درخواست پرداخت با خطا مواجه بود'),
            1008 => Yii::t('rabint', 'تاییدیه پرداخت با خطا مواجه بود'),
            /* ------------------------------------------------------ */
            -1 => Yii::t('rabint', 'اطلاعات ارسالی ناقص است'),
            -2 => Yii::t('rabint', 'IP یا کد مرجع صحیح نیست'),
            -3 => Yii::t('rabint', 'رقم تراکنش باید بالای ۱۰۰ تومان باشد'),
            -4 => Yii::t('rabint', 'سطح تایید پذیرنده از سطح نقره‌ای پایین‌تر است'),
            -11 => Yii::t('rabint', 'درخواست موردنظر یافت نشد'),
            -21 => Yii::t('rabint', 'هیچ نوع تراکنش مالی برای این تراکنش یافت نشد'),
            -22 => Yii::t('rabint', 'تراکنش ناموفق است'),
            -33 => Yii::t('rabint', 'رقم تراکنش با رقم پرداخت شده مطابقت ندارد'),
            -54 => Yii::t('rabint', 'درخواست موردنظر آرشیو شده'),
            100 => Yii::t('rabint', 'عملیات با موفقیت انجام شد'),
            101 => Yii::t('rabint', 'عملیات با موفقیت انجام شده، اما متد Verify قبلا بر روی این تراکنش اعمال شده است'),
        ];
        require_once __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "nusoap.php";
    }

    //
    public function startPay($orderId, $amount, $callbackUrl) {

        $amount *= self::amountFactor;

        try {
            //$client = new \SoapClient($this->config['url']);
            $client = new nusoap_client($this->config['url'], 'wsdl');
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = FALSE;
        } catch (\Exception $exception) {
            return (isset($this->messages[$result['status']])) ? $result['status'] : 1006;
        }

        try {
            $result = $client->call('PaymentRequest',  array(
                'MerchantID' => $this->config['merchantid'],
                'Amount' => $amount,
                'Description' => 'پرداخت سفارش شماره ' . $orderId,
                'CallbackURL' => $callbackUrl,
            ));
//            $result = (array) $client->PaymentRequest(
//                            array(
//                                'MerchantID' => $this->config['merchantid'],
//                                'Amount' => $amount,
//                                'Description' => 'پرداخت سفارش شماره ' . $orderId,
//                                'CallbackURL' => $callbackUrl,
//            ));
        } catch (\Exception $exception) {
            return (isset($this->messages[$result['status']])) ? $result['status'] : 1007;
        }



//        $result = $client->call("PaymentRequest", array(
//            array(
//                'MerchantID' => $this->config['merchantid'],
//                'Amount' => $amount,
//                'Description' => 'پرداخت سفارش شماره ' . $orderId,
//                'CallbackURL' => $callbackUrl,
//        )));

        /* Check for errors ================================================= */

        $status = $result['Status'];
        if ($status != $this->gatewaySuccessStatus) {
            return (isset($this->messages[$status])) ? $status : 1002;
        }

        /* Success ========================================================== */

        $PayPath = 'https://www.zarinpal.com/pg/StartPay/' . $result['Authority'] . '/ZarinGate';
        redirect($PayPath);
//        header('location:' . );
        die();
        ?>
        <script language="javascript" type="text/javascript">
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", "<?= $PayPath; ?>");
            form.setAttribute("target", "_self");
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        </script>
        <?php
        die();
        return $status;
    }

    //
    public function payStatus($orderId, $gatewayData = []) {
//        \Yii::warning('pay_get:' . print_r($_GET, TRUE) . ' - pay_post:' . print_r($_POST, TRUE), 'payCheck');
        try {
            //$client = new \SoapClient($this->config['url']);
            $client = new nusoap_client($this->config['url'], 'wsdl');
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = FALSE;
        } catch (\Exception $exception) {
            return (isset($this->messages[$result['status']])) ? $result['status'] : 1006;
        }
        $return = [
            'status' => NULL,
            'gateway_reciept' => NULL,
            'gateway_meta' => NULL
        ];
        $transaction = \rabint\finance\models\FinanceTransactions::findOne($orderId);
        if ((!isset($_GET['Status'])) OR ( isset($_GET['Status']) AND ( $_GET['Status'] != 'OK'))) {
            $return['status'] = 1004;
        } else {
            $params = [
                'MerchantID' => $this->config['merchantid'],
                'Authority' => $_GET['Authority'],
                'Amount' => $transaction->amount,
            ];

            try {
                $result = (array) $client->call('PaymentVerification',$params);
            } catch (\Exception $exception) {
                return (isset($this->messages[$result['status']])) ? $result['status'] : 1008;
            }

//            var_dump($result);
//            die('!!!!!');
            /* Check for errors ================================================= */

//            if ($client->fault) {
//                $return['status'] = (isset($this->messages[$result['status']])) ? $result['status'] : 1002;
//            }
            $status = strtolower($result['Status']);
            if ($status == 100 || $status == 101) {
                $return['status'] = $this->gatewaySuccessStatus;
                $return['gateway_reciept'] = $result['RefID'];
                $return['gateway_meta'] = $result['verifyPaymentResult'];
            } elseif (!isset($this->messages[$status])) {
                $return['status'] = 1002;
            } else {
                $return['status'] = $status;
            }
        }
        return $return;
    }

    //
    public function verifyPay($orderId, $gatewayMeta = []) {
//        return $this->payStatus($orderId,$gatewayMeta);
        return $this->gatewaySuccessStatus;
    }

    //
    public function rollBack($orderId, $gatewayMeta = []) {
        
    }

}
