<?php

namespace rabint\finance\controllers;

use rabint\finance\Config;
use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceWallet;
use Yii;
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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['FinView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['change-wallet'],
                        'roles' => ['manager', 'FinChangeWallet'],
                    ]
                    // everything else is denied
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
            $model->amount = str_replace('٫', '', $model->amount);
            $model->amount = str_replace(',', '', $model->amount);
            if (!is_numeric($model->amount) or $model->amount <= 0) {
                Yii::$app->session->setFlash('success', 'مبلغ تنظیم نشده است یا فرمت مبلغ صحیح نیست.');
                return $this->refresh();
            }
            $model->amount = intval($model->amount);
            $model->created_at = time();
            $model->transactioner = \rabint\helpers\user::id();
            $model->transactioner_ip = \rabint\helpers\user::ip();
            if ($model->change_action == 0) {
                $message = 'مبلغ ' . number_format($model->amount) . ' ' . $money_unit . '  از حساب ' . $model->user->displayName . ' کسر شد.';
                $model->amount *= -1;

                if (!Config::ALLOW_NEGATIVE_WALLET) {
                    $cash = FinanceWallet::cash($model->user_id);
                    if ($cash + $model->amount < 0) {
                        Yii::$app->session->setFlash('danger', Yii::t('app', 'تراکنش به علت منفی شدن حساب کاربر به مبلغ {amount} ریال انجام نشد!', ['amount' => number_format($cash + $model->amount)]));
                        return $this->refresh();
                    }
                }


            } else {
                $message = 'مبلغ ' . number_format($model->amount) . ' ' . $money_unit . ' به حساب ' . $model->user->displayName . ' افزوده شد.';
            }
            $model->save(false);

            if (isset(Yii::$app->notify)) {
                Yii::$app->notify->sendTemplate(
                    $model->transactioner,
                    ($model->change_action == 0) ? 'fDecWalletAdmin' : 'fIncWalletAdmin',
                    $model->amount, null, null,
                    null,
                    ['priority' => FinanceTransactions::getNotifyConfig()]
                );
            }

            Yii::$app->session->setFlash('success', $message);
            return $this->refresh();
        }
        return $this->render('change-wallet', ['model' => $model]);
    }
}
