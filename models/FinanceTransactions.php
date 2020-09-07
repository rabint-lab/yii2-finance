<?php

namespace rabint\finance\models;

include_once dirname(__DIR__) . '/Config.php';

use Yii;
use rabint\finance\models\FinanceWallet;
use rabint\finance\Config;

use common\models\User;


class FinanceTransactions extends Config
{

    static function setGatewayData($tid, $data)
    {
        $model = FinanceTransactions::findOne($tid);
        if (empty($model))
            return FALSE;
        $model->gateway_meta = json_encode($data);
        return ($model->save(false)) ? TRUE : FALSE;
    }

    static function setPayFailed($model)
    {
        $model->status = self::TRANSACTION_SKIPPED;
        return ($model->save(false)) ? TRUE : FALSE;
    }

    static function setPayPaid($model, $gateway_reciept, $gateway_meta)
    {
        $model->status = self::TRANSACTION_PAID;
        $model->gateway_reciept = (string) $gateway_reciept;
        $model->gateway_meta = \yii\helpers\Json::encode($gateway_meta);
        return ($model->save(false)) ? TRUE : FALSE;
    }

    static function setPayVerified($model)
    {
        $model->status = self::TRANSACTION_COMPLETED;
        if (!$model->save(false)) {
            return FALSE;
        }
        $incRes = FinanceWallet::inc(
            $model->transactioner,
            $model->amount,
            $model->transactioner,
            $model->transactioner_ip,
            'شارژ حساب کاربر بر اثر پرداخت شماره: ' . $model->id,
            ['transaction_id' => $model->id]
        );
        if ($incRes) {
            $res = FinanceWallet::balancingPay(
                $model->transactioner,
                json_decode($model->additional_rows, TRUE),
                $model->transactioner,
                $model->transactioner_ip
            );
            return TRUE;
        }
        return FALSE;
    }

    /** 
     * @return \common\models\base\ActiveQuery 
     */
    public function getTransactionerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'transactioner']);
    }
}
