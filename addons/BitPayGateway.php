<?php

namespace rabint\finance\addons;

use Yii;

class BitPayGateway extends GatewayAbstract
{

    public static $API_BASE_URL = 'https://bitpay.ir/payment';

    public $gatewaySuccessStatus = 1;

    public function __construct()
    {
        $this->code = 10;
        $this->slug = 'BitPay';
        $this->title = Yii::t('rabint', 'آیدی پی');

        $this->gatewaySuccessStatus = 1;
        $this->config = config('SERVICE.finance.gateways_config.BitPay');

        $this->messages = [
            1001 => Yii::t('rabint', 'خطا در اتصال به درگاه بانک'),
            1002 => Yii::t('rabint', 'خطای نامشخص'),
            1003 => Yii::t('rabint', 'در حال انتقال به درگاه بانک'),
            1004 => Yii::t('rabint', 'پرداخت معتبر نمی باشد'),
            1005 => Yii::t('rabint', 'خطای امنیتی رخ داده است'),
            -1 => Yii::t('rabint', 'APIارسالی با نوعAPIتعریف شده درbitpayسازگار نیست'),
            -2 => Yii::t('rabint', 'مقدارamountداده عددي نمیباشد و یا کمتر از 1000ریال است'),
            -3 => Yii::t('rabint', 'مقدارredirectرشتهnullاست'),
            -4 => Yii::t('rabint', 'درگاهی با اطالعات ارسالی شما وجود ندارد و یا در حالت انتظار میباشد'),
            -5 => Yii::t('rabint', 'خطا در اتصال به درگاه، لطفا مجددا تالش کنید'),
            /* ------------------------------------------------------ */
            1 => Yii::t('rabint', 'پرداخت موفقیت آمیز بود'),
        ];
    }


    public function startPay($orderId, $amount, $callbackUrl)
    {
        $url = self::$API_BASE_URL . '/gateway-send';
        $api = $this->config['apikay'];
        $amount = (int)$amount;
        $redirect = urlencode($callbackUrl);
//        $name = '';//ekhtiari
//        $email = '';//ekhtiari
//        $description = '';//ekhtiari
        $factorId = $orderId;//ekhtiari

        $result = $this->send($url, $api, $amount, $redirect, $factorId, $name, $email, $description);

        if ($result > 0 && is_numeric($result)) {
            /* Success ========================================================== */
            $this->setGatewayData($orderId, $result);
            $go = self::$API_BASE_URL . "/gateway-$result-get";
            return redirect($go);
        }

        /* Error ========================================================== */
        return $result['error_message'];
    }

    public function payStatus($orderId, $gatewayData = [])
    {
        $url = $url = self::$API_BASE_URL . '/gateway-result-second';
        $api = $this->config['apikay'];
        $trans_id = $_GET['trans_id'];
        $id_get = $_GET['id_get'];
        $result = $this->get($url, $api, $trans_id, $id_get);

        $parseDecode = json_decode($result);

        if ($parseDecode->status == 1) {
            $return = [
                'status' => $this->gatewaySuccessStatus,
                'gateway_reciept' => $parseDecode->factorId,
                'gateway_meta' => $parseDecode
            ];
            return $return;
//            //mablagh ersali
//            echo $parseDecode->amount;
//
//            //factore ersali (ekhtiari)
//            echo $parseDecode->factorId;
//
//            //shomare kart pardakht konanade
//            echo $parseDecode->cardNum;

        }

        $return = [
            'status' => $parseDecode->status,
            'gateway_reciept' => NULL,
            'gateway_meta' => $parseDecode
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


    /**
     * @param string $point
     * @param array $params
     * @return array
     */
    public function sendRequest($point, $params = [])
    {
        $headers = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$API_BASE_URL . $point);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->config['apikay'],
            'X-SANDBOX: ' . $this->config['sandbox'] ?? 0,
        ]);

        // this function is called by curl for each header received
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );

        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        /* Check for errors ================================================= */
        return [
            'statuscode' => $statusCode,
            'headers' => $headers,
            'body' => json_decode($result, 1),
        ];
    }


    function send($url, $api, $amount, $redirect, $factorId, $name, $email, $description)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api=$api&amount=$amount&redirect=$redirect&factorId=$factorId&name=$name&email=$email&description=$description");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    function get($url, $api, $trans_id, $id_get)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "api=$api&id_get=$id_get&trans_id=$trans_id&json=1");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

}
