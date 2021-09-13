<?php

namespace rabint\finance\controllers;

use app\modules\coupon\services\CouponService;
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
        $get=\Yii::$app->request->get();
        $coupon = $get['coupon']??'';
        $coupon_id = \app\modules\coupon\services\CouponService::factory()->checkCoupon($coupon,Yii::$app->user->id);
        $discount = isset($coupon)?\app\modules\coupon\services\CouponService::factory()->getDiscount($coupon,Yii::$app->user->id,self::getAmount($this->findModel($id)->metadata,\app\modules\coupon\models\Coupon::class)):0;
        if($discount!=0)
            self::actionAddItem($id,\Yii::t('rabint','تخفیف'),1,\app\modules\coupon\models\Coupon::class,$discount*-1,$coupon_id);
        if(!empty($coupon)&&$discount==0){
            \Yii::$app->session->setFlash('warning',\Yii::t('rabint','کد تخفیف نا معتبر'));
        }
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
            }//select gateway class
            $callbackUrl = \yii\helpers\Url::to(['/finance/default/afterpay', 'tid' => $model->id, 'token' => $model->token,'coupon'=>$coupon], TRUE);

            $model->status = FinanceTransactions::TRANSACTION_INPROCESS;
            $model->gateway = $gateway;
            if ($model->save(false)) {
                $gatewayClass = new $selectedGateway;
//                if(isset($_GET['coupon']))
//                    CouponService::factory()->useCoupon($_GET['coupon'],Yii::$app->user->id,Yii::$app->request->userIP,Yii::$app->request->userAgent,$model->id,$model->amount);
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
            'discount'=> $discount
        ]);
    }

    /**
     * @param $id integer
     * @param $title string
     * @param $count int
     * @param $object string
     * @param $price string
     * @param $object_id integer
     * @return bool
     */

    public function actionAddItem($id,$title,$count,$object,$price,$object_id){
        $model = $this->findModel($id);
        if(!in_array($model->status,[FinanceTransactions::TRANSACTION_INPROCESS,FinanceTransactions::TRANSACTION_PENDING]))
            return false;
        $meta = json_decode($model->metadata,'true');
        $meta = array_filter($meta,function ($item)use ($object,$object_id){
            if(isset($item[5]) && $item[5]==$object && $item[4]==$object_id){
                return false;
            }
            else
                return true;
        });
        array_push($meta,[$title,$count,$price,$price*$count,$object_id,$object]);
        $model->metadata = json_encode($meta);
        $model->amount = self::getAmount($meta);
        return $model->save()===true?true:false;
    }

    /**
     * @param $meta array|string
     * @param $without array
     * @return int
     */

    private static function getAmount($meta,$without = []){
        if(!is_array($without))
            $without = [$without];
        if(!is_array($meta))
            $meta = json_decode($meta,true);
        $amount = 0;
        foreach ($meta as $item){
            if(isset($item[5])&&in_array($item[5],$without))continue;
            $amount += $item[3];
        }
        return $amount;
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
