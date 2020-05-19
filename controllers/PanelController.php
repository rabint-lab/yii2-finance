<?php

namespace rabint\finance\controllers;

use Yii;
use rabint\finance\models\FinanceDraft;
use rabint\finance\models\FinanceDraftSearch;

class PanelController extends \rabint\controllers\PanelController {

    public function actions() {
        return [
            'check-upload' => [
                'class' => 'rabint\attachment\actions\UploadAction',
                'modelName' => FinanceDraft::className(),
                'attribute' => 'check_url',
                'type' => 'image',
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors() {
        $ret = parent::behaviors();
        return $ret + [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'check-upload' => ['post']
                ],
            ],
//            'environment' => [
//                'class' => \rabint\filters\EnvironmentFilter::className(),
//                'actions' => [
//                    'index' => 'panel',
//                    'profile' => 'panel',
//                ],
//            ],
        ];
    }

    public function actionAddDraft() {
        $model = new FinanceDraft();

        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = \rabint\helpers\user::id();
            $model->status = FinanceDraft::STATUS_DRAFT;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', \Yii::t('rabint', 'حواله شما با موفقیت ثبت شد'));
                return $this->redirect(['drafts']);
            } else {
                Yii::$app->session->setFlash('danger', \Yii::t('rabint', 'متاسفانه در هنگام ثبت حواله خطایی رخ داده است'));
            }
        }
        return $this->render('add-draft', [
                    'model' => $model,
        ]);
    }

    public function actionDrafts() {
        $searchModel = new FinanceDraftSearch();
        $params = Yii::$app->request->queryParams;
        $params['FinanceDraftSearch']['user_id'] = \rabint\helpers\user::id();
        $dataProvider = $searchModel->search($params);

        return $this->render('drafts', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCharge() {
        $model = new \rabint\finance\models\FinanceWallet;
        if ($model->load(Yii::$app->request->post())) {
            $userId = \rabint\helpers\user::id();
            $userIp = \rabint\helpers\user::ip();
            $amount = $model->amount;
            $res = \rabint\finance\finance::pay([
                        'amount' => $amount,
                        'internal_reciept' => 'FinanceCharge',
                        'return_url' => \yii\helpers\Url::to(['wallet']),
                        'internal_meta' => '',
                        'additional_rows' => [],
//                        'description' => $model->description
                        'forcePay' => TRUE
            ]);
            /* =================================================================== */
            if ($res == FALSE) {
                Yii::$app->session->setFlash('warning', \Yii::t('rabint', 'متاسفانه در حال حاضر انتقال به درگاه بانک مقدور نیست.'));
            }
            return $this->redirect(['index']);
        }
        return $this->render('charge', ['model' => $model]);
    }

    public function actionWallet() {
        $searchModel = new \rabint\finance\models\FinanceWalletSearch();
        $params = Yii::$app->request->queryParams;
        $params['FinanceWalletSearch']['user_id'] = \rabint\helpers\user::id();
        $dataProvider = $searchModel->search($params);

        return $this->render('wallet', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

}
