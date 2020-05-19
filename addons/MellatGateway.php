<?php

namespace rabint\finance\addons;

use Yii;

class MellatGateway extends GatewayAbstract {

//    function afterPay() {
//        
//    }

    public function __construct() {
        $this->code = 2;
        $this->slug = 'mellat';
        $this->title = Yii::t('rabint', 'بانک ملت');
        $this->config = [
            'url' => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'au_url' => 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat',
            'namespace' => 'http://interfaces.core.sw.bps.com/',
            'username' => '',
            'password' => '',
            'terminal_id' => '',
            'payer_id' => '',
        ];
        $this->gatewaySuccessStatus = 0;
        $this->messages = [
            1001 => Yii::t('rabint', 'خطا در اتصال به درگاه بانک'),
            1002 => Yii::t('rabint', 'خطای نامشخص'),
            1003 => Yii::t('rabint', 'در حال انتقال به درگاه بانک'),
            1004 => Yii::t('rabint', 'پرداخت معتبر نمی باشد'),
            1005 => Yii::t('rabint', 'خطای امنیتی رخ داده است'),
            /* ------------------------------------------------------ */
            0 => Yii::t('rabint', 'پرداخت موفقیت آمیز بود'),
            11 => Yii::t('rabint', 'شماره كارت نامعتبر است'),
            12 => Yii::t('rabint', 'موجودي كافي نيست'),
            13 => Yii::t('rabint', 'رمز نادرست است'),
            14 => Yii::t('rabint', 'تعداد دفعات وارد كردن رمز بيش از حد مجاز است'),
            15 => Yii::t('rabint', 'كارت نامعتبر است'),
            16 => Yii::t('rabint', 'دفعات برداشت وجه بيش از حد مجاز است'),
            17 => Yii::t('rabint', 'كاربر از انجام تراكنش منصرف شده است'),
            18 => Yii::t('rabint', 'تاريخ انقضاي كارت گذشته است'),
            19 => Yii::t('rabint', 'مبلغ برداشت وجه بيش از حد مجاز است'),
            21 => Yii::t('rabint', 'پذيرنده نامعتبر است'),
            23 => Yii::t('rabint', 'خطاي امنيتي رخ داده است'),
            24 => Yii::t('rabint', 'اطلاعات كاربري پذيرنده نامعتبر است'),
            25 => Yii::t('rabint', 'مبلغ نامعتبر است'),
            31 => Yii::t('rabint', 'پاسخ نامعتبر است'),
            32 => Yii::t('rabint', 'فرمت اطلاعات وارد شده صحيح نمي باشد'),
            33 => Yii::t('rabint', 'حساب نامعتبر است'),
            34 => Yii::t('rabint', 'خطاي سيستمي'),
            35 => Yii::t('rabint', 'تاريخ نامعتبر است'),
            41 => Yii::t('rabint', 'شماره درخواست تكراري است'),
            42 => Yii::t('rabint', 'يافت نشد Sale تراكنش'),
            43 => Yii::t('rabint', 'قبلا درخواستVerifyداده شده است '),
            44 => Yii::t('rabint', 'درخواستVerfiy يافت نشد '),
            45 => Yii::t('rabint', ' تراكنشSettle شده است'),
            46 => Yii::t('rabint', 'تراكنشSettle نشده است'),
            47 => Yii::t('rabint', ' تراكنشSettle يافت نشد'),
            48 => Yii::t('rabint', ' تراكنشReverse شده است'),
            49 => Yii::t('rabint', 'تراكنشRefund يافت نشد '),
            51 => Yii::t('rabint', 'تراكنش تكراري است'),
            54 => Yii::t('rabint', 'تراكنش مرجع موجود نيست'),
            55 => Yii::t('rabint', 'تراكنش نامعتبر است'),
            61 => Yii::t('rabint', 'خطا در واريز'),
            111 => Yii::t('rabint', 'صادر كننده كارت نامعتبر است'),
            112 => Yii::t('rabint', 'خطاي سوييچ صادر كننده كارت'),
            113 => Yii::t('rabint', 'پاسخي از صادر كننده كارت دريافت نشد'),
            114 => Yii::t('rabint', 'دارنده كارت مجاز به انجام اين تراكنش نيست'),
            412 => Yii::t('rabint', 'شناسه قبض نادرست است'),
            413 => Yii::t('rabint', 'شناسه پرداخت نادرست است'),
            414 => Yii::t('rabint', 'سازمان صادر كننده قبض نامعتبر است'),
            415 => Yii::t('rabint', 'زمان جلسه كاري به پايان رسيده است'),
            416 => Yii::t('rabint', 'خطا در ثبت اطلاعات'),
            417 => Yii::t('rabint', 'شناسه پرداخت كننده نامعتبر است'),
            418 => Yii::t('rabint', 'اشكال در تعريف اطلاعات مشتري'),
            419 => Yii::t('rabint', 'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است'),
            421 => Yii::t('rabint', ' IPنامعتبر است'),
        ];
    }

    public function startPay($orderId, $amount, $callbackUrl) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "nusoap.php";
        $client = new nusoap_client($this->config['url']);
        $namespace = $this->config['namespace'];
        $err = $client->getError();
        if ($err) {
            return 1001;
        }

        /* Connect successful =============================================== */

        $parameters = [
            'terminalId' => $this->config['terminal_id'],
            'userName' => $this->config['username'],
            'userPassword' => $this->config['password'],
            'payerId' => $this->config['payer_id'],
            'orderId' => $orderId,
            'amount' => (int) $amount,
            'localDate' => date("ymd"),
            'localTime' => date("His"),
            'additionalData' => '',
            'callBackUrl' => $callbackUrl,
        ];
        $result = $client->call('bpPayRequest', $parameters, $namespace);

        /* Check for errors ================================================= */

        if ($client->fault) {
            return (isset($this->messages[$result])) ? $result : 1002;
        }
        $err = $client->getError();
//        print_r($err);
        if ($err) {
            return (isset($this->messages[$err])) ? $err : 1002;
        }

        /* Success ========================================================== */

        list($ResCode, $RefId) = explode(',', $result);

        $this->setGatewayData($orderId, ['ResCode' => $ResCode, 'RefId' => $RefId]);

        if ($ResCode == $this->gatewaySuccessStatus) {
            echo $this->messages[1003];
            ?>
            <script language="javascript" type="text/javascript">
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "<?= $this->config['au_url']; ?>");
                form.setAttribute("target", "_self");
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", "<?= $RefId; ?>");
                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            </script>
            <?php
            return 1003;
        } else {
            return (isset($this->messages[$ResCode])) ? $ResCode : 1002;
        }
    }

    public function payStatus($orderId, $gatewayData = []) {
        $return = [
            'status' => NULL,
            'gateway_reciept' => NULL,
            'gateway_meta' => NULL
        ];
        if ((!isset($_POST['RefId'])) OR ( !isset($_POST['ResCode']))) {
            $return['status'] = 1004;
        }
//        if ($_POST['RefId'] != '') {
//            $return['status'] = 1005;
//        }
        if ($_POST['ResCode'] != $this->gatewaySuccessStatus) {
            $return['status'] = $_POST['ResCode'];
        } else {
            $return['status'] = $this->gatewaySuccessStatus;
            $return['gateway_reciept'] = isset($_POST['SaleReferenceId']) ? $_POST['SaleReferenceId'] : 0;
            $return['gateway_meta'] = $_POST;
        }
        /* ------------------------------------------------------ */
        return $return;
    }

    public function verifyPay($orderId, $gatewayMeta = []) {
        $ResCode = $_POST['ResCode'];
        $SaleOrderId = $_POST['SaleOrderId'];
        $SaleReferenceId = $_POST['SaleReferenceId'];
        $RefId = $_POST['RefId'];
        /* ================================================================== */
        $client = new nusoap_client($this->config['url']);
        $namespace = $this->config['namespace'];

        $err = $client->getError();
        if ($err) {
            return 1001;
        }

        $parameters = array(
            'terminalId' => $this->config['terminal_id'],
            'userName' => $this->config['username'],
            'userPassword' => $this->config['password'],
            'orderId' => $_POST['VerifyOrderId'],
            'saleOrderId' => $SaleOrderId,
            'saleReferenceId' => $SaleReferenceId
        );
        $result = $client->call('bpVerifyRequest', $parameters, $namespace);

        /* Check for errors ================================================= */

        if ($client->fault) {
            return (isset($this->messages[$result])) ? $result : 1002;
        }
        $err = $client->getError();
        if ($err) {
            return (isset($this->messages[$err])) ? $err : 1002;
        }

        /* Settle ======================================================== */
        $parameters = array(
            'terminalId' => $this->config['terminal_id'],
            'userName' => $this->config['username'],
            'userPassword' => $this->config['password'],
            'orderId' => $SaleOrderId,
            'saleOrderId' => $SaleOrderId,
            'saleReferenceId' => $SaleReferenceId
        );
        $result = $client->call('bpSettleRequest', $parameters, $namespace);

        /* Check for errors ================================================= */

        if ($client->fault) {
            return (isset($this->messages[$result])) ? $result : 1002;
        }
        $err = $client->getError();
        if ($err) {
            return (isset($this->messages[$err])) ? $err : 1002;
        }
        /* success ==================================================== */
        return $this->gatewaySuccessStatus;
    }

    public function rollBack($orderId, $gatewayMeta = []) {
        
    }

}
