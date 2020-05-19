<?php

namespace rabint\finance\addons;

use rabint\finance\models\FinanceTransactions;

abstract class GatewayAbstract {

    protected $config = [];
    public $code;
    public $title;
    public $slug;
    public $messages = [];
    public $gatewaySuccessStatus = 0;

    abstract public function __construct();

    abstract public function startPay($tid, $amount, $returnURL);

    abstract public function payStatus($tid, $gatewayData = []);

    abstract public function verifyPay($tid, $gatewayData = []);

    abstract public function rollBack($tid, $gatewayData = []);

    protected function setGatewayData($tid, $data) {
        return FinanceTransactions::setGatewayData($tid, $data);
    }

    protected function setPayFailed($model) {
        return FinanceTransactions::setPayFailed($model);
    }

    protected function setPayPaid($model, $gateway_reciept, $gateway_meta = []) {
        return FinanceTransactions::setPayPaid($model, $gateway_reciept, $gateway_meta);
    }

    protected function setPayVerified($model) {
        return FinanceTransactions::setPayVerified($model);
    }

    final public function afterPay($model) {
        $payStatus = $this->payStatus($model->id, $model->gateway_meta);

        $callback = json_decode($model->settle_callback_function,true);
        if(is_array($callback) && count($callback))
        {
            $class = $callback['model'];
            $method = $callback['method'];
            $params = $callback['params'];
            $_model = new $class;
            $moduleGoftTrue = $_model->{$method}($params);
        }
        else
            $moduleGoftTrue = true;
        if ($payStatus['status'] === $this->gatewaySuccessStatus  && $moduleGoftTrue) {
            /**
             * settle a transaction
             */
            $this->setPayPaid($model, $payStatus['gateway_reciept'], $payStatus['gateway_meta']);
            $endResult = $this->verifyPay($model->id, $payStatus['gateway_meta']);
            if ($endResult == $this->gatewaySuccessStatus) {
                $this->setPayVerified($model);
            }
            return $endResult;
        }
        $this->setPayFailed($model);
        return $payStatus['status'];
    }

}
