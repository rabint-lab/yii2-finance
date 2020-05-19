<?php

namespace rabint\finance\controllers;

use Yii;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceTransactionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AdminController extends \rabint\controllers\AdminController
{

    public function behaviors()
    {
        return parent::behaviors([
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    //
    public function actionWallet()
    {
        $searchModel = new \rabint\finance\models\FinanceWalletSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('wallet', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeWallet()
    {
        $model = new \rabint\finance\models\FinanceWallet;
        $money_unit = \rabint\helpers\currency::title();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->amount <= 0) {
                Yii::$app->session->setFlash('success', 'مبلغ تنظیم نشده است.');
                return $this->refresh();
            }
            $model->created_at = time();
            $model->transactioner = \rabint\helpers\user::id();
            $model->transactioner_ip = \rabint\helpers\user::ip();
            if ($model->change_action == 0) {
                $message = 'مبلغ ' . number_format($model->amount) .' '.$money_unit . '  از حساب ' . $model->user->displayName . ' کسر شد.';
                $model->amount *= -1;
            } else {
                $message = 'مبلغ ' . number_format($model->amount) .' '.$money_unit . ' به حساب ' . $model->user->displayName . ' افزوده شد.';
            }
            $model->save();
            Yii::$app->session->setFlash('success', $message);
            return $this->refresh();
        }
        return $this->render('change-wallet', ['model' => $model]);
    }
}
