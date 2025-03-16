<?php

namespace rabint\finance\controllers;

use rabint\finance\models\FinanceTransactions;
use rabint\finance\models\FinanceTransactionsSearch;
use rabint\helpers\user;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AdminTransactionController implements the CRUD actions for FinanceTransactions model.
 */
class AdminTransactionController extends \rabint\controllers\AdminController
{

    const BULK_ACTION_SETDRAFT = 'bulk-draft';
    const BULK_ACTION_SETPUBLISH = 'bulk-publish';
    const BULK_ACTION_DELETE = 'bulk-delete';

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
                    'bulk' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manager', 'FinView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update'],
                        'roles' => ['manager', 'FinAddInvoice'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * list of bulk action as static
     * @return array
     */
    public static function bulkActions()
    {
        return [
            //static::BULK_ACTION_SETPUBLISH => ['title' =>  Yii::t('rabint', 'set publish'),'class'=>'success','icon'=>'fas fa-check'],
            //static::BULK_ACTION_SETDRAFT => ['title' =>  Yii::t('rabint', 'set draft'),'class'=>'warning','icon'=>'fas fa-times'],
            static::BULK_ACTION_DELETE => ['title' => Yii::t('rabint', 'delete all'), 'class' => 'danger', 'icon' => 'fas fa-trash-alt'],
        ];
    }


    /**
     * bulk action
     * @return mixed
     */
    public function actionBulk($action)
    {

        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks')); // Array or selected records primary keys

        if (!isset(static::bulkActions()[$action])) {
            Yii::$app->session->setFlash('warning', Yii::t('rabint', 'Bulk action Not found!'));
            return $this->redirect(\rabint\helpers\uri::referrer());
        }
        $selection = (array)$pks;
        $err = 0;
        switch ($action) {
            case static::BULK_ACTION_SETPUBLISH:
                if (FinanceTransactions::updateAll(['status' => FinanceTransactions::STATUS_DRAFT], ['id' => $selection])) {
                    Yii::$app->session->setFlash('success', Yii::t('rabint', 'Bulk action was successful'));
                } else {
                    $err++;
                }
                break;
            case static::BULK_ACTION_SETDRAFT:
                if (FinanceTransactions::updateAll(['status' => FinanceTransactions::STATUS_DRAFT], ['id' => $selection])) {
                    Yii::$app->session->setFlash('success', Yii::t('rabint', 'Bulk action was successful'));
                } else {
                    $err++;
                }
                break;
            case static::BULK_ACTION_DELETE:
                if (FinanceTransactions::deleteAll(['id' => $selection])) {
                    Yii::$app->session->setFlash('success', Yii::t('rabint', 'Bulk action was successful'));
                } else {
                    $err++;
                }
                break;
        }
        if ($err) {
            Yii::$app->session->setFlash('danger', Yii::t('rabint', 'عملیات ناموفق بود'));
        }

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#ajaxCrudDatatable'];
        }
        return $this->redirect(\rabint\helpers\uri::referrer());
    }

    /**
     * Lists all FinanceTransactions models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FinanceTransactionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FinanceTransactions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FinanceTransactions();
        $model->scenario = FinanceTransactions::SCENARIO_ADMIN_INVOICE;
        $request = Yii::$app->request;

        if ($model->load(Yii::$app->request->post())) {

            $model->amount = str_replace('٫', '', $model->amount);
            $model->amount = str_replace(',', '', $model->amount);
            if (!is_numeric($model->amount) OR $model->amount <= 0) {
                Yii::$app->session->setFlash('success', 'مبلغ تنظیم نشده است یا فرمت مبلغ صحیح نیست.');
                return $this->refresh();
            }
            $model->amount = intval($model->amount);

            $title = $model->metadata;
            $userIp = user::ip();
            $items = [
                [
                    $model->metadata,
                    1,
                    $model->amount,
                    $model->amount
                ],
            ];
            $aditionalData = [
                [
                    'requested_time' => time(),
                    'user_id' => $model->transactioner,
                    'amount' => $model->amount * -1,
                    'transactioner' => user::id(),
                    'transactioner_ip' => $userIp,
                    'description' => 'پرداخت هزینه صورتحساب ' . $title,
                    'metadata' => NULL
                ],
                [
                    'requested_time' => time(),
                    'user_id' => 0,
                    'amount' => $model->amount,
                    'transactioner' => user::id(),
                    'transactioner_ip' => $userIp,
                    'description' => 'دریافت هزینه صورتحساب ' . $title,
                    'metadata' => NULL
                ],
            ];
            $model->status = FinanceTransactions::TRANSACTION_PENDING;
            $model->internal_reciept = 'TR-' . uniqid();
            $model->return_url = '-';
            $model->metadata = json_encode($items);
            $model->additional_rows = json_encode($aditionalData);
            $model->created_at = time();

            $model->gateway = FinanceTransactions::defaultPaymentGatewayId();
            $model->gateway_reciept = NULL;
            $model->gateway_meta = NULL;
            $model->transactioner_ip = $userIp;
            $model->token = md5(uniqid('FinanceTransactions', TRUE));
            $model->settle_callback_function = json_encode([]);


            if ($model->save()) {
                if (isset(Yii::$app->notify)) {
                    Yii::$app->notify->sendTemplate(
                        $model->transactioner,
                        'fInvoiceAdmin', $model->id, null, null,
                        ['/finance/transaction/view', 'id' => $model->id],
                        ['priority' => FinanceTransactions::getNotifyConfig()]
                    );
                }
                Yii::$app->session->setFlash('success', Yii::t('rabint', 'Item successfully created.'));

                if ($request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'forceReload' => '#ajaxCrudDatatable',
                        'title' => Yii::t('rabint', 'Create new') . ' ' . Yii::t('rabint', '\'FinanceTransactions\''),
                        'content' => '<span class="text-success">' . Yii::t('rabint', 'Create {item} success', [
                                'item' => '<?= $modelClass ?>',
                            ]) . '</span>',
                        'footer' => Html::button(Yii::t('rabint', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::a(Yii::t('rabint', 'Create More'), ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])

                    ];
                }
                return $this->redirect(['index']);
            } else {
                $model->metadata = $title;
                $errors = \rabint\helpers\str::modelErrToStr($model->getErrors());
                Yii::$app->session->setFlash('danger', Yii::t('rabint', 'Unable to create item.') . "<br/>" . $errors);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FinanceTransactions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//
//
//        $model = $this->findModel($id);
//
//        $request = Yii::$app->request;
//
//        if ($model->load(Yii::$app->request->post())) {
//            if ($model->save()) {
//                Yii::$app->session->setFlash('success', Yii::t('rabint', 'Item successfully updated.'));
//
//                if ($request->isAjax) {
//                    Yii::$app->response->format = Response::FORMAT_JSON;
//                    [
//                        'forceReload' => '#ajaxCrudDatatable',
//                        'title' => Yii::t('rabint', 'Updating') . ' ' . Yii::t('rabint', '\'FinanceTransactions\''),
//                        'content' => $this->renderAjax('view', [
//                            'model' => $model,
//                        ]),
//                        'footer' => Html::button(Yii::t('rabint', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
//                            Html::a('Edit', [Yii::t('rabint', 'update'), 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
//                    ];
//                }
//                return $this->redirect(['index']);
//            } else {
//                $errors = \rabint\helpers\str::modelErrToStr($model->getErrors());
//                Yii::$app->session->setFlash('danger', Yii::t('rabint', 'Unable to update item.') . "<br/>" . $errors);
//            }
//        }
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//
//    }

    /**
     * Deletes an existing FinanceTransactions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//
//        $request = Yii::$app->request;
//
//        if($this->findModel($id)->delete()){
//            Yii::$app->session->setFlash('success', Yii::t('rabint', 'Item successfully deleted.'));
//        }else{
//            Yii::$app->session->setFlash('danger', Yii::t('rabint', 'Unable to delete item.'));
//        }
//
//        if ($request->isAjax) {
//            /*
//            *   Process for ajax request
//            */
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ['forceClose' => true, 'forceReload' => '#ajaxCrudDatatable'];
//        }
//
//        return $this->redirect(['index']);
//
//    }

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
