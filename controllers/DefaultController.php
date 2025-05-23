<?php

namespace rabint\finance\controllers;

use rabint\finance\addons\WalletGateway;
use rabint\finance\models\FinanceOfflinePay;
use rabint\finance\models\FinanceTransactions;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

#use yii\web\Controller;

class DefaultController extends \rabint\controllers\DefaultController
{

    var $enableCsrfValidation = false;

    public function behaviors()
    {
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
    public function actionSelectgateway()
    {

    }

    //
    public function actionAddfund()
    {

    }

    public function actionEndtest($code)
    {
        $model = FinanceTransactions::findOne(['internal_reciept' => $code]);
        echo '<pre>';
        print_r($model);
        echo '</pre>';
        die('--');
    }

    public function actionTest()
    {
        if (Yii::$app->user->isGuest) {
            die('pleace login...');
        }
        $aditionalData = [
            ['amount' => -1000, 'description' => 'کسر از حساب بابت خرید جنس', 'metadata' => ['ali' => '10toman', 'hasan' => '120']],
            ['user_id' => 0, 'amount' => 1000, 'description' => 'افزودن به حساب سایت بابت خرید جنس', 'metadata' => ['ali' => '10toman', 'hasan' => '120']],
            ['amount' => -900, 'description' => 'پول جنس', 'metadata' => 'stringData'],
            ['user_id' => 0, 'amount' => 900, 'description' => 'پول جنس', 'metadata' => 'stringData'],
        ];
        $trackingCode = 'test-' . uniqid();
        $res = \rabint\finance\finance::pay([
            'amount' => 1000,
            'internal_reciept' => $trackingCode,
            'return_url' => \yii\helpers\Url::to(['endtest', 'code' => $trackingCode]),
            'internal_meta' => json_encode(['key' => 'velue']),
            'additional_rows' => $aditionalData
        ]);
        var_dump($res);
    }

    public function actionAfterpay($tid, $token)
    {
        $transaction = FinanceTransactions::find()->where(['id' => $tid, 'token' => $token])->one();
        if ($transaction == null) {
            throw new NotFoundHttpException(Yii::t('rabint', 'اطلاعات پرداخت نا معتبر است'));
            return;
        }
        if ($transaction->gateway === 0) {
            $selectedGateway = WalletGateway::class;
        } else {
            $selectedGateway = FinanceTransactions::paymentGateways()[$transaction->gateway]['class'];
        }
        $gateway = new $selectedGateway;
        $payResult = $gateway->afterPay($transaction);
//        die('aaaaaaa');
        if (isset($gateway->messages[$payResult])) {
            if ($payResult == $gateway->gatewaySuccessStatus) {
                $flashType = 'success';
            } else {
                $flashType = 'warning';
            }
            Yii::$app->getSession()->setFlash($flashType, $gateway->messages[$payResult]);
        }
        $returnUrl = $transaction->return_url;
        if ($returnUrl == '-') {
            return $this->render('after-pay', [
                'transaction' => $transaction,
                'pay_result' => ($payResult == $gateway->gatewaySuccessStatus),
                'message' => $gateway->messages[$payResult] ?? "",
            ]);
        }
        if (strpos($returnUrl, '?') !== FALSE) {
            $returnUrl .= '&tid=' . $tid;
        } else {
            $returnUrl .= '?tid=' . $tid;
        }

        $_SESSION['finance']['transactionID'] = $tid;
        $_SESSION['finance']['pay_status'] = $transaction->status;

        return Yii::$app->controller->redirect($returnUrl);
    }

    public function actionOfflinePay($id)
    {
        $request = Yii::$app->request;
        $model = FinanceOfflinePay::findOne($id);
        if (!empty($request->post())) {
            $post = $request->post();
            $model->load($post);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rabint', 'فیش با موفقیت ثبت شد.پس از تایید مدیر فاکتور شما ثبت میگردد.'));
                return $this->redirect(['/']);
            } else {
                var_dump($model->errors);
                exit();
                Yii::$app->session->setFlash('warning', Yii::t('rabint', 'ثبت فیش با مشکل مواجه شد.'));
            }
        }
        if ($model->user_id != Yii::$app->user->id) {
            return false;
        }
        return $this->render('offline-pay', ['model' => $model]);
    }

    public function actionOfflinePayChangeStatus($id, $status)
    {
        $model = FinanceOfflinePay::findOne($id);
        if ($model != null) {
            $model->status = $status;
            if ($model->save()) {
                if ($status == FinanceOfflinePay::STATUS_ACCEPTED)
                    redirect($model->callback);
            } else {
                Yii::$app->session->setFlash('warning', Yii::t('rabint', 'ثبت وضعیت با مشکل مواجه شد.'));
            }
        }
        return false;
    }

}
