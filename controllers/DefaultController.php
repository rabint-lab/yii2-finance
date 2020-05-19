<?php

namespace rabint\finance\controllers;

use Yii;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceTransactionsSearch;
#use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DefaultController extends \rabint\controllers\DefaultController {

    var $enableCsrfValidation = false;

    public function behaviors() {
        return parent::behaviors([
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'afterpay' => ['post'],
                ],
            ],
        ]);
    }

    //
    public function actionSelectgateway() {
        
    }

    //
    public function actionAddfund() {
        
    }

    public function actionEndtest() {
        $model = FinanceTransactions::findOne($_GET['tid']);
        echo '<pre>';
        print_r($model);
        echo '</pre>';
        die('--');
    }

    public function actionTest() {
        if (Yii::$app->user->isGuest) {
            die('pleace login...');
        }
        $aditionalData = [
            [ 'amount' => -100, 'description' => 'حق مشتری مداری', 'metadata' => ['ali' => '10toman', 'hasan' => '120']],
            ['user_id' => '2', 'amount' => 100, 'description' => 'حق مشتری مداری', 'metadata' => ['ali' => '10toman', 'hasan' => '120']],
            [ 'amount' => -900, 'description' => 'پول جنس', 'metadata' => 'stringData'],
            ['user_id' => '3', 'amount' => 900, 'description' => 'پول جنس', 'metadata' => 'stringData'],
        ];
        $res = \rabint\finance\finance::pay([
                    'amount' => 1000,
                    'internal_reciept' => 'test-1',
                    'return_url' => \yii\helpers\Url::to(['endtest']),
                    'internal_meta' => json_encode(['key' => 'velue']),
                    'additional_rows' => $aditionalData
        ]);
    }

    public function actionAfterpay($tid, $token) {
        $transaction = FinanceTransactions::find()->where(['id' => $tid, 'token' => $token])->one();
        if ($transaction == null) {
            throw new NotFoundHttpException(Yii::t('rabint', 'اطلاعات پرداخت نا معتبر است'));
            return;
        }
        $selectedGateway = FinanceTransactions::$paymentGateways[$transaction->gateway];
        $gateway = new $selectedGateway['class'];
        $payResult = $gateway->afterPay($transaction);
        if (isset($gateway->messages[$payResult])) {
            $flashType = ($payResult == $gateway->gatewaySuccessStatus) ? 'success' : 'warning';
            Yii::$app->getSession()->setFlash($flashType, $gateway->messages[$payResult]);
        }
        $returnUrl = $transaction->return_url;
//        if (strpos($returnUrl, '?') !== FALSE) {
//            $returnUrl .='&tid=' . $tid;
//        } else {
//            $returnUrl .='?tid=' . $tid;
//        }
        
        $_SESSION['finance']['transactionID'] = $tid;
        $_SESSION['finance']['pay_status'] = $transaction->status;
        
        return Yii::$app->controller->redirect($returnUrl);
    }

}
