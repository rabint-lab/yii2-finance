<?php

namespace rabint\finance\controllers;

use rabint\finance\addons\WalletGateway;
use rabint\finance\models\FinanceWallet;
use Yii;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceTransactionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * TransactionController implements the CRUD actions for FinanceTransactions model.
 */
class TransactionController extends \rabint\controllers\PanelController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return parent::behaviors([
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all FinanceTransactions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FinanceTransactionsSearch();
        $params = Yii::$app->request->queryParams;
        $params['FinanceTransactionsSearch']['transactioner'] = \rabint\helpers\user::id();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FinanceTransactions model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->transactioner != \rabint\helpers\user::id()) {
            throw new ForbiddenHttpException(\Yii::t('rabint', 'این صورتحساب مربوط به شما نمی باشد'));
        }
        if (Yii::$app->request->isPost) {
            $doPay = Yii::$app->request->post("do_pay");
            $gateway = Yii::$app->request->post("gateway");

            if ($gateway == 'wallet') {
                $gateway= 0;
                $selectedGateway = WalletGateway::class;
            } else {

                if (!isset(FinanceTransactions::paymentGateways()[$gateway])) {
                    throw new ForbiddenHttpException(\Yii::t('rabint', 'درگاه انتخابی نا معتبر است'));
                }
                $selectedGateway = FinanceTransactions::paymentGateways()[$gateway]['class'];
            }
            $callbackUrl = \yii\helpers\Url::to(['/finance/default/afterpay', 'tid' => $model->id, 'token' => $model->token], TRUE);

            $model->status = FinanceTransactions::TRANSACTION_INPROCESS;
            $model->gateway = $gateway;
            if ($model->save(false)) {
                $gatewayClass = new $selectedGateway;
                $error = $gatewayClass->startPay($model->id, $model->amount, $callbackUrl);
                if ($error == $gatewayClass->gatewaySuccessStatus) {
                    return TRUE;
                } else {
                    if (isset($gatewayClass->messages[$error])) {
                        return $this->render('error', [
                            'error_type' => 'danger',
                            'error_number' => $error,
                            'error_text' => $gatewayClass->messages[$error],
                            'redirect' => \yii\helpers\Url::to($model->return_url, true),
                        ]);
                    }
                    return $this->render('error', [
                        'error_type' => 'danger',
                        'error_number' => $error,
                        'error_text' => \Yii::t('rabint', 'خطا در اتصال به درگاه، لطفا دوباره تلاش کنید'),
                        'redirect' => \yii\helpers\Url::to($model->return_url, true),
                    ]);
                }
            } else {
                Yii::$app->session->setFlash('danger', \Yii::t('rabint', 'خطا در اتصال به درگاه، لطفا دوباره تلاش کنید'));
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the FinanceTransactions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FinanceTransactions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FinanceTransactions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(\Yii::t('rabint', 'The requested page does not exist.'));
        }
    }
}
