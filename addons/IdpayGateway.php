<?php

namespace rabint\finance\addons;

use Yii;

class IdpayGateway extends GatewayAbstract
{

    public static $API_BASE_URL = 'https://api.idpay.ir/v1.1';

    public $gatewaySuccessStatus = 10;

    public function __construct()
    {
        $this->code = 5;
        $this->slug = 'idpay';
        $this->title = Yii::t('rabint', 'آیدی پی');

        $this->gatewaySuccessStatus = 10;
        $this->config = config('SERVICE.finance.gateways_config.idpay');
        $this->messages = [
            1001 => Yii::t('rabint', 'خطا در اتصال به درگاه بانک'),
            1002 => Yii::t('rabint', 'خطای نامشخص'),
            1003 => Yii::t('rabint', 'در حال انتقال به درگاه بانک'),
            1004 => Yii::t('rabint', 'پرداخت معتبر نمی باشد'),
            1005 => Yii::t('rabint', 'خطای امنیتی رخ داده است'),
            /* ------------------------------------------------------ */
            0 => Yii::t('rabint', 'پرداخت موفقیت آمیز بود'),
        ];
    }

    public function startPay($orderId, $amount, $callbackUrl)
    {

        $params = array(
            'order_id' => $orderId,
            'amount' => (int)$amount,
            'callback' => $callbackUrl,
//            'name' => '',
//            'phone' => '',
//            'mail' => '',
//            'desc' => '',
        );

        $res = $this->sendRequest('/payment', $params);
        $httpcode = $res['statuscode'];
        $result = $res['body'];

        /* Check for errors ================================================= */

        if ($httpcode >= 400 or isset($result['error_message'])) {
            //return $result['error_code'];
            return $result['error_message'];
        }

        /* Success ========================================================== */


        $this->setGatewayData($orderId, $result);
        return redirect($result['link']);
    }

    public function payStatus($orderId, $gatewayData = [])
    {
        $postData = Yii::$app->request->post();
        $return = [
            'status' => NULL,
            'gateway_reciept' => NULL,
            'gateway_meta' => $postData
        ];

        if ((!isset($postData['status']))) {
            $return['status'] = 1004;
            return $return;
        }

        $return['status'] = $postData['status'];

        $baseData = $this->getGatewayData($orderId);


        if ($return['status'] >= $this->gatewaySuccessStatus) {
            $return['status'] = $this->gatewaySuccessStatus;
            $return['gateway_reciept'] = isset($postData['track_id']) ? $postData['track_id'] : 0;
            $return['gateway_meta'] = array_merge($postData, $baseData);
        }
        /* ------------------------------------------------------ */
        return $return;
    }

    public function verifyPay($orderId, $gatewayMeta = [])
    {
        $postData = Yii::$app->request->post();
        $return = [
            'status' => NULL,
            'gateway_reciept' => NULL,
            'gateway_meta' => $postData
        ];


        $params = array(
            'id' => $gatewayMeta['id'],
            'order_id' => $orderId,
        );

        $res = $this->sendRequest('/payment/verify', $params);

        /* Check for errors ================================================= */

        $httpcode = $res['statuscode'];
        $result = $res['body'];

        /* Check for errors ================================================= */

        if ($httpcode >= 400 or isset($result['error_message'])) {
            //return $result['error_code'];
            return $result['error_message'];
        }

        /* Success ========================================================== */


        $this->setGatewayData($orderId, $result);
        if ($result['status'] >= $this->gatewaySuccessStatus) {
            $return['status'] = $this->gatewaySuccessStatus;
        }
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
        $statusCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        /* Check for errors ================================================= */
        return [
            'statuscode' => $statusCode,
            'headers' => $headers,
            'body' => json_decode($result, 1),
        ];
    }


}
